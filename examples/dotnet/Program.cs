// ---------------------------------------------------------------------------
// One System CAS — minimal ASP.NET Core sample (.NET 8 minimal API)
//
// Proves the CasSystem.Client package end-to-end:
//   (a) trigger CAS login            -> GET /auth/login
//   (b) handle callback + validate   -> GET /cas/callback?token=...
//   (c) show the authenticated user  -> GET /dashboard   (protected)
//   (d) logout                       -> POST /auth/logout
//
// The package is referenced LOCALLY via a ProjectReference in CasDemo.csproj.
// Public API used (all from the package):
//   CasConfig                  - configuration object
//   AddCasClient(config)       - DI registration extension
//   CasClient.GetLoginUrl()    - builds {PublicUrl||ServerUrl}/sso/login?client_id=...
//   CasClient.ValidateTokenAsync(token) - server-to-server token validation
//   CasClient.LogoutAsync(token)        - optional CAS-server logout
//   CasAuthMiddleware          - protects configured paths using the session
// ---------------------------------------------------------------------------

using System.Text.Json;
using CasDemo;
using CasSystem.Client;

var builder = WebApplication.CreateBuilder(args);

// Bind to the assigned port (env PORT, default 9106). Listen on all interfaces
// so the container is reachable in the unified single-server deployment.
var port = Env("PORT", "9106");
builder.WebHost.UseUrls($"http://0.0.0.0:{port}");

// --- Read configuration from environment (see .env.example) -----------------
var casConfig = new CasConfig
{
    // Internal/back-channel base for server-to-server token validation.
    ServerUrl    = Env("CAS_BASE_URL", "http://localhost:8080"),
    // Public, browser-facing base for the SSO login redirect (split-horizon
    // deployments). Falls back to ServerUrl when CAS_PUBLIC_URL is unset.
    PublicUrl    = Env("CAS_PUBLIC_URL", ""),
    ClientId     = Env("CAS_CLIENT_ID", "dotnet-demo"),
    ClientSecret = Env("CAS_CLIENT_SECRET", "dotnet-demo-secret"),
    // The callback URL is the one REGISTERED with the CAS server for this client.
    // The CAS server redirects the browser here with ?token=<JWT> appended.
    CallbackUrl  = Env("CAS_CALLBACK_URL", "http://localhost:9106/cas/callback"),
};

// Register the CAS client in DI (package extension method).
builder.Services.AddCasClient(casConfig);

// --- Local username/password store (SQLite) ---------------------------------
// Separate from CAS: the app keeps its OWN accounts in a SQLite file. The path
// is configurable (APP_DB_PATH); the default lives under ./data so the Docker
// image can mount/keep it writable. The store is a singleton — it creates the
// schema and seeds the demo users on startup.
var dbPath = Env("APP_DB_PATH", Path.Combine(AppContext.BaseDirectory, "data", "app.db"));
builder.Services.AddSingleton(new UserStore(dbPath));

// We use ASP.NET Core sessions to hold the app's OWN session after the
// single-use CAS token has been validated. The CasAuthMiddleware reads the
// "cas_user" session key to decide whether a request is authenticated.
// AddSession() needs an IDistributedCache backing store; the in-memory one is
// fine for a single-instance demo.
builder.Services.AddDistributedMemoryCache();
builder.Services.AddSession(options =>
{
    options.Cookie.Name = ".CasDemo.Session";
    options.Cookie.HttpOnly = true;
    options.IdleTimeout = TimeSpan.FromHours(1);
});

var app = builder.Build();

// Create the SQLite schema and seed demo users (rajan/rajan123, demo/demo123)
// if the table is empty. Runs once at startup, before any request is served.
app.Services.GetRequiredService<UserStore>().InitializeAndSeed();

app.UseSession();

// Protect /dashboard with the package middleware. Unauthenticated browser
// requests are redirected to loginUrl ("/auth/login"); the middleware also
// puts the user dict into context.Items["cas_user"] for protected handlers.
// Pass only protectedPaths + loginUrl. The optional requiredRoles param defaults
// to null; passing an explicit (string[]?)null breaks ActivatorUtilities, which
// cannot type-match a null argument to the constructor.
// Redirect unauthenticated browser requests to "/login" — the chooser page that
// offers BOTH a local username/password form and a "sign in with CAS" link — so
// either auth path is reachable from a protected page. CAS SSO is unchanged.
app.UseMiddleware<CasAuthMiddleware>(
    new[] { "/dashboard" }, // protectedPaths
    "/login");              // loginUrl

// ---------------------------------------------------------------------------
// Home page — public. Shows current login state with links.
// ---------------------------------------------------------------------------
app.MapGet("/", (HttpContext ctx) =>
{
    var user = GetSessionUser(ctx);
    string body;
    if (user == null)
    {
        body =
            "<h1>One System CAS — .NET sample</h1>" +
            "<p>You are <b>not</b> signed in.</p>" +
            "<p><a href=\"/login\">Login with username &amp; password</a> (local account)</p>" +
            "<p><a href=\"/auth/login\">Login with CAS</a> (single sign-on)</p>" +
            "<p><a href=\"/dashboard\">Go to protected dashboard</a> (will redirect to login)</p>";
    }
    else
    {
        var username = ValueOf(user, "username");
        var via = ValueOf(user, "auth_source");
        var howSignedIn = string.IsNullOrEmpty(via) ? "" : $" (via {via})";
        body =
            "<h1>One System CAS — .NET sample</h1>" +
            $"<p>Signed in as <b>{username}</b>{howSignedIn}.</p>" +
            "<p><a href=\"/dashboard\">View dashboard</a></p>" +
            "<form method=\"post\" action=\"/auth/logout\"><button type=\"submit\">Logout</button></form>";
    }
    return Html(body);
});

// ---------------------------------------------------------------------------
// LOCAL login — username/password form. Public.
// GET /login renders the form. A ?error / fresh request shows it clean.
// ---------------------------------------------------------------------------
app.MapGet("/login", (HttpContext ctx) =>
{
    // Already signed in? Send them home.
    if (GetSessionUser(ctx) != null)
        return Results.Redirect("/");
    return Html(LoginForm(error: null));
});

// ---------------------------------------------------------------------------
// LOCAL login — POST /login. This single route serves TWO contracts:
//
//   1. Browser form post (application/x-www-form-urlencoded):
//      validate against the SQLite store; on success create the app's OWN
//      session (the same "cas_user" session key the CAS flow uses) and redirect
//      to /dashboard; on failure re-render the form with an error.
//
//   2. CAS link-validation contract (JSON or form body containing
//      "client_validation"): the CAS server POSTs {username,password,
//      client_validation:true} to confirm the account exists locally. Respond
//      with JSON only — 200 {"success":true} for valid, 401 {"success":false}
//      for invalid — and NEVER create a browser session for this call.
// ---------------------------------------------------------------------------
app.MapPost("/login", async (HttpContext ctx, UserStore users) =>
{
    var (username, password, isValidation) = await ReadLoginInputAsync(ctx);
    var ok = users.ValidateCredentials(username, password);

    // --- CAS link-validation contract: JSON only, no session ---
    if (isValidation)
    {
        ctx.Response.StatusCode = ok ? 200 : 401;
        ctx.Response.ContentType = "application/json";
        await ctx.Response.WriteAsync(ok ? "{\"success\":true}" : "{\"success\":false}");
        return;
    }

    // --- Browser form login ---
    if (!ok)
    {
        await new HtmlResult(WrapHtml(LoginForm("Invalid username or password.")), 401)
            .ExecuteAsync(ctx);
        return;
    }

    // Valid local credentials -> establish the app's OWN session, reusing the
    // same "cas_user" session key so the rest of the app (home, dashboard,
    // middleware) treats local and CAS users identically.
    var sessionUser = new Dictionary<string, object>
    {
        ["username"] = username,
        ["auth_source"] = "local",
    };
    ctx.Session.SetString("cas_user", JsonSerializer.Serialize(sessionUser));
    await Results.Redirect("/dashboard").ExecuteAsync(ctx);
});

// ---------------------------------------------------------------------------
// (a) Trigger CAS login.
// Build the CAS login URL via the package and 302-redirect the browser.
// The CAS server authenticates the user, then redirects back to the
// registered callback_url with ?token=<JWT>.
// ---------------------------------------------------------------------------
app.MapGet("/auth/login", (CasClient cas) =>
{
    var loginUrl = cas.GetLoginUrl();
    return Results.Redirect(loginUrl);
});

// ---------------------------------------------------------------------------
// (b) Handle the callback and validate the token.
// Read ?token=..., validate it SERVER-TO-SERVER via the package (it carries
// the client_secret). The token is SINGLE-USE: validate exactly once, then
// create our OWN session (store the user in the session).
// ---------------------------------------------------------------------------
app.MapGet("/cas/callback", async (HttpContext ctx, CasClient cas) =>
{
    var token = ctx.Request.Query["token"].ToString();
    if (string.IsNullOrEmpty(token))
        return Html("<h1>Login failed</h1><p>No token in callback.</p><p><a href=\"/\">Home</a></p>", 400);

    // Validate once against {CAS_BASE}/api/validate-token (handled by the package).
    var user = await cas.ValidateTokenAsync(token);
    if (user == null)
        return Html("<h1>Login failed</h1><p>Token validation rejected by CAS server.</p><p><a href=\"/\">Home</a></p>", 401);

    // Token validated -> create the app's own session. Tag the auth source so
    // the home page can show whether the user arrived via CAS or a local login.
    user["auth_source"] = "cas";
    ctx.Session.SetString("cas_user", JsonSerializer.Serialize(user));
    return Results.Redirect("/dashboard");
});

// ---------------------------------------------------------------------------
// (c) Protected page — shows the authenticated user.
// Reaching this handler means CasAuthMiddleware already authenticated the
// request and populated context.Items["cas_user"].
// ---------------------------------------------------------------------------
app.MapGet("/dashboard", (HttpContext ctx) =>
{
    var user = ctx.Items["cas_user"] as Dictionary<string, object>
               ?? GetSessionUser(ctx)
               ?? new Dictionary<string, object>();

    var rows = string.Join("", user.Select(kv =>
        $"<tr><td><b>{System.Net.WebUtility.HtmlEncode(kv.Key)}</b></td>" +
        $"<td>{System.Net.WebUtility.HtmlEncode(kv.Value?.ToString() ?? "")}</td></tr>"));

    var body =
        "<h1>Dashboard (protected)</h1>" +
        "<p>Authenticated via One System CAS.</p>" +
        $"<table border=\"1\" cellpadding=\"6\">{rows}</table>" +
        "<form method=\"post\" action=\"/auth/logout\"><button type=\"submit\">Logout</button></form>" +
        "<p><a href=\"/\">Home</a></p>";
    return Html(body);
});

// ---------------------------------------------------------------------------
// (d) Logout — clear the app's OWN session and optionally tell the CAS server.
// ---------------------------------------------------------------------------
app.MapPost("/auth/logout", async (HttpContext ctx, CasClient cas) =>
{
    // Best-effort CAS-server logout (optional per protocol).
    try { await cas.LogoutAsync(); } catch { /* non-fatal */ }

    ctx.Session.Remove("cas_user");
    ctx.Session.Clear();
    return Results.Redirect("/");
});

app.Run();

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------
static string Env(string key, string fallback)
    => Environment.GetEnvironmentVariable(key) is { Length: > 0 } v ? v : fallback;

static Dictionary<string, object>? GetSessionUser(HttpContext ctx)
{
    var json = ctx.Session.GetString("cas_user");
    return string.IsNullOrEmpty(json)
        ? null
        : JsonSerializer.Deserialize<Dictionary<string, object>>(json);
}

static string ValueOf(Dictionary<string, object> user, string key)
    => user.TryGetValue(key, out var v) ? System.Net.WebUtility.HtmlEncode(v?.ToString() ?? "") : "";

// Read the login inputs from EITHER a JSON body or a form post, and detect the
// CAS link-validation call. Validation is signalled by a truthy "client_validation"
// field in the body and/or an Accept: application/json request header.
static async Task<(string username, string password, bool isValidation)> ReadLoginInputAsync(HttpContext ctx)
{
    string username = "", password = "";
    var clientValidation = false;

    var contentType = ctx.Request.ContentType ?? "";
    if (contentType.Contains("application/json", StringComparison.OrdinalIgnoreCase))
    {
        using var doc = await JsonDocument.ParseAsync(ctx.Request.Body);
        var root = doc.RootElement;
        if (root.TryGetProperty("username", out var u)) username = u.GetString() ?? "";
        if (root.TryGetProperty("password", out var p)) password = p.GetString() ?? "";
        if (root.TryGetProperty("client_validation", out var cv))
            clientValidation = cv.ValueKind == JsonValueKind.True
                || (cv.ValueKind == JsonValueKind.String && cv.GetString()?.ToLowerInvariant() is "1" or "true")
                || (cv.ValueKind == JsonValueKind.Number && cv.GetDouble() != 0);
    }
    else if (ctx.Request.HasFormContentType)
    {
        var form = await ctx.Request.ReadFormAsync();
        username = form["username"].ToString();
        password = form["password"].ToString();
        var cv = form["client_validation"].ToString();
        clientValidation = cv is "1" or "true" or "True" or "on";
    }

    // A JSON Accept header also marks this as a programmatic validation call.
    var acceptsJson = ctx.Request.Headers["Accept"]
        .Any(a => a?.Contains("application/json", StringComparison.OrdinalIgnoreCase) == true);

    return (username, password, clientValidation || acceptsJson);
}

// Render the local username/password login form. `error` (if any) is shown inline.
static string LoginForm(string? error)
{
    var err = string.IsNullOrEmpty(error)
        ? ""
        : $"<p style=\"color:#b00020;margin:0 0 1rem\">{System.Net.WebUtility.HtmlEncode(error)}</p>";
    return
        "<h1>Sign in</h1>" +
        "<p>Use a local account, or <a href=\"/auth/login\">sign in with CAS</a>.</p>" +
        err +
        "<form method=\"post\" action=\"/login\" style=\"display:grid;gap:.75rem;max-width:320px\">" +
        "  <label>Username<br><input name=\"username\" autocomplete=\"username\" required " +
        "    style=\"width:100%;padding:.5rem;box-sizing:border-box\"></label>" +
        "  <label>Password<br><input name=\"password\" type=\"password\" autocomplete=\"current-password\" required " +
        "    style=\"width:100%;padding:.5rem;box-sizing:border-box\"></label>" +
        "  <button type=\"submit\">Sign in</button>" +
        "</form>" +
        "<p style=\"color:#666;font-size:.9rem;margin-top:1.5rem\">Demo accounts: " +
        "<code>rajan / rajan123</code> &middot; <code>demo / demo123</code></p>" +
        "<p><a href=\"/\">Home</a></p>";
}

// Wrap a body fragment in the shared HTML chrome (doctype, head, styles).
static string WrapHtml(string body)
    => "<!doctype html><html><head><meta charset=\"utf-8\"><title>CAS .NET sample</title>" +
       "<style>body{font-family:system-ui,sans-serif;max-width:640px;margin:3rem auto;padding:0 1rem}" +
       "button{padding:.5rem 1rem;cursor:pointer}table{border-collapse:collapse}" +
       "input{font:inherit}label{font-size:.95rem}</style></head>" +
       $"<body>{body}</body></html>";

// Build an HTML IResult using a tiny self-contained IResult that sets both the
// content type and the HTTP status code itself — keeps the sample dependency-free
// and independent of any specific Results.* overload.
static IResult Html(string body, int status = 200)
    => new HtmlResult(WrapHtml(body), status);

// Minimal IResult that writes UTF-8 HTML with a chosen status code.
sealed class HtmlResult : IResult
{
    private readonly string _html;
    private readonly int _status;
    public HtmlResult(string html, int status) { _html = html; _status = status; }

    public async Task ExecuteAsync(HttpContext httpContext)
    {
        httpContext.Response.StatusCode = _status;
        httpContext.Response.ContentType = "text/html; charset=utf-8";
        await httpContext.Response.WriteAsync(_html, System.Text.Encoding.UTF8);
    }
}

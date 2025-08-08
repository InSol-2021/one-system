@extends('public.documentation.layout')

@section('title', '.NET MVC CAS SSO Integration Guide - Updated Architecture')
@section('description', 'Complete guide for integrating .NET MVC applications with our modernized CAS Single Sign-On system featuring Admin/User/Public separation.')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                <i class="fab fa-microsoft text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $dotnetGuide['title'] }}</h1>
                <p class="text-gray-600 mt-1">{{ $dotnetGuide['description'] }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <span><i class="fas fa-clock mr-1"></i>Setup time: 10 minutes</span>
            <span><i class="fas fa-code mr-1"></i>Difficulty: Intermediate</span>
            <span><i class="fas fa-tag mr-1"></i>.NET 6+</span>
        </div>
    </div>

    <!-- Installation -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">1. Project Setup</h2>
        
        <h3 class="text-xl font-semibold mb-3">NuGet Packages</h3>
        <div class="code-block mb-6">
            <pre class="language-xml"><code>&lt;PackageReference Include="System.IdentityModel.Tokens.Jwt" Version="7.0.0" /&gt;
&lt;PackageReference Include="Microsoft.AspNetCore.Authentication.JwtBearer" Version="7.0.0" /&gt;
&lt;PackageReference Include="Newtonsoft.Json" Version="13.0.3" /&gt;</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Package Manager Console</h3>
        <div class="code-block mb-6">
            <pre class="language-bash"><code>Install-Package System.IdentityModel.Tokens.Jwt
Install-Package Microsoft.AspNetCore.Authentication.JwtBearer
Install-Package Newtonsoft.Json</code></pre>
        </div>
    </section>

    <!-- Configuration -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">2. Configuration</h2>
        
        <h3 class="text-xl font-semibold mb-3">appsettings.json</h3>
        <div class="code-block mb-6">
            <pre class="language-json"><code>{
  "CasSettings": {
    "ServerUrl": "http://localhost:5000",
    "ClientId": "your_client_id",
    "ClientUsername": "your_client_username",
    "ClientPassword": "your_client_password",
    "SignatureSecret": "your_signature_secret",
    "CallbackUrl": "http://yourapp.com/cas/callback",
    "TokenExpiration": 120
  },
  "Logging": {
    "LogLevel": {
      "Default": "Information",
      "Microsoft.AspNetCore": "Warning"
    }
  }
}</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Configuration Model</h3>
        <div class="code-block mb-6">
            <pre class="language-csharp"><code>// Models/CasSettings.cs
public class CasSettings
{
    public string ServerUrl { get; set; }
    public string ClientId { get; set; }
    public string ClientUsername { get; set; }
    public string ClientPassword { get; set; }
    public string SignatureSecret { get; set; }
    public string CallbackUrl { get; set; }
    public int TokenExpiration { get; set; } = 120;
}</code></pre>
        </div>
    </section>

    <!-- CAS Client Service -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">3. CAS Client Service</h2>
        
        <div class="code-block mb-6">
            <pre class="language-csharp"><code>// Services/CasClient.cs
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;
using Microsoft.IdentityModel.Tokens;
using Newtonsoft.Json;

public class CasClient
{
    private readonly CasSettings _settings;
    private readonly HttpClient _httpClient;
    private readonly ILogger&lt;CasClient&gt; _logger;

    public CasClient(CasSettings settings, HttpClient httpClient, ILogger&lt;CasClient&gt; logger)
    {
        _settings = settings;
        _httpClient = httpClient;
        _logger = logger;
    }

    public string GetLoginUrl(string returnUrl)
    {
        var loginUrl = $"{_settings.ServerUrl}/auth/login";
        var callbackUrl = $"{_settings.CallbackUrl}?return_url={Uri.EscapeDataString(returnUrl)}";
        
        return $"{loginUrl}?callback_url={Uri.EscapeDataString(callbackUrl)}";
    }

    public async Task&lt;CasUser&gt; ValidateTokenAsync(string token)
    {
        try
        {
            var tokenHandler = new JwtSecurityTokenHandler();
            var key = Encoding.ASCII.GetBytes(_settings.SignatureSecret);
            
            var validationParameters = new TokenValidationParameters
            {
                ValidateIssuerSigningKey = true,
                IssuerSigningKey = new SymmetricSecurityKey(key),
                ValidateIssuer = false,
                ValidateAudience = false,
                ValidateLifetime = true,
                ClockSkew = TimeSpan.Zero
            };

            var principal = tokenHandler.ValidateToken(token, validationParameters, out var validatedToken);
            
            // Extract user information from claims
            var user = new CasUser
            {
                Username = principal.FindFirst(ClaimTypes.Name)?.Value,
                Email = principal.FindFirst(ClaimTypes.Email)?.Value,
                Role = principal.FindFirst(ClaimTypes.Role)?.Value,
                FirstName = principal.FindFirst("first_name")?.Value,
                LastName = principal.FindFirst("last_name")?.Value
            };

            return user;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Token validation failed");
            throw new UnauthorizedAccessException("Invalid token");
        }
    }

    public async Task&lt;AuthResult&gt; AuthenticateAsync(string username, string password)
    {
        var loginData = new
        {
            username,
            password,
            client_id = _settings.ClientId,
            client_username = _settings.ClientUsername,
            client_password = _settings.ClientPassword
        };

        var json = JsonConvert.SerializeObject(loginData);
        var content = new StringContent(json, Encoding.UTF8, "application/json");

        var response = await _httpClient.PostAsync($"{_settings.ServerUrl}/api/sso/token", content);
        
        if (response.IsSuccessStatusCode)
        {
            var responseContent = await response.Content.ReadAsStringAsync();
            var result = JsonConvert.DeserializeObject&lt;AuthResult&gt;(responseContent);
            return result;
        }
        
        throw new UnauthorizedAccessException("Authentication failed");
    }
}</code></pre>
        </div>
    </section>

    <!-- Models -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">4. Models</h2>
        
        <div class="code-block mb-6">
            <pre class="language-csharp"><code>// Models/CasUser.cs
public class CasUser
{
    public string Username { get; set; }
    public string Email { get; set; }
    public string Role { get; set; }
    public string FirstName { get; set; }
    public string LastName { get; set; }
    
    public string FullName => $"{FirstName} {LastName}";
    
    public bool IsAdmin => Role == "admin";
    
    public bool HasRole(string role) => Role == role;
}

// Models/AuthResult.cs
public class AuthResult
{
    public string Token { get; set; }
    public CasUser User { get; set; }
    public DateTime ExpiresAt { get; set; }
}</code></pre>
        </div>
    </section>

    <!-- Authentication Attribute -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">5. Authentication Attribute</h2>
        
        <div class="code-block mb-6">
            <pre class="language-csharp"><code>// Attributes/CasAuthAttribute.cs
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.Filters;

public class CasAuthAttribute : ActionFilterAttribute
{
    public override void OnActionExecuting(ActionExecutingContext context)
    {
        var casUser = context.HttpContext.Session.GetString("CasUser");
        var casToken = context.HttpContext.Session.GetString("CasToken");
        
        if (string.IsNullOrEmpty(casUser) || string.IsNullOrEmpty(casToken))
        {
            var returnUrl = context.HttpContext.Request.Path + context.HttpContext.Request.QueryString;
            var casClient = context.HttpContext.RequestServices.GetService&lt;CasClient&gt;();
            var loginUrl = casClient.GetLoginUrl(returnUrl);
            
            context.Result = new RedirectResult(loginUrl);
            return;
        }
        
        // Validate token if needed
        var user = Newtonsoft.Json.JsonConvert.DeserializeObject&lt;CasUser&gt;(casUser);
        context.HttpContext.Items["CasUser"] = user;
        
        base.OnActionExecuting(context);
    }
}

// Attributes/CasRoleAttribute.cs
public class CasRoleAttribute : CasAuthAttribute
{
    private readonly string[] _roles;
    
    public CasRoleAttribute(params string[] roles)
    {
        _roles = roles;
    }
    
    public override void OnActionExecuting(ActionExecutingContext context)
    {
        base.OnActionExecuting(context);
        
        if (context.Result != null) return; // Already redirected
        
        var user = context.HttpContext.Items["CasUser"] as CasUser;
        
        if (user == null || !_roles.Contains(user.Role))
        {
            context.Result = new ForbidResult();
        }
    }
}</code></pre>
        </div>
    </section>

    <!-- Controller Examples -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">6. Controller Examples</h2>
        
        <h3 class="text-xl font-semibold mb-3">Home Controller</h3>
        <div class="code-block mb-6">
            <pre class="language-csharp"><code>// Controllers/HomeController.cs
using Microsoft.AspNetCore.Mvc;

public class HomeController : Controller
{
    private readonly CasClient _casClient;
    
    public HomeController(CasClient casClient)
    {
        _casClient = casClient;
    }
    
    [CasAuth]
    public IActionResult Index()
    {
        var user = HttpContext.Items["CasUser"] as CasUser;
        return View(user);
    }
    
    [CasRole("admin")]
    public IActionResult Admin()
    {
        var user = HttpContext.Items["CasUser"] as CasUser;
        return View(user);
    }
    
    public IActionResult Login(string returnUrl = "/")
    {
        var loginUrl = _casClient.GetLoginUrl(returnUrl);
        return Redirect(loginUrl);
    }
    
    public async Task&lt;IActionResult&gt; CasCallback(string token, string return_url = "/")
    {
        try
        {
            var user = await _casClient.ValidateTokenAsync(token);
            
            HttpContext.Session.SetString("CasUser", JsonConvert.SerializeObject(user));
            HttpContext.Session.SetString("CasToken", token);
            
            return Redirect(return_url);
        }
        catch
        {
            return RedirectToAction("Login");
        }
    }
    
    public IActionResult Logout()
    {
        HttpContext.Session.Clear();
        return RedirectToAction("Index");
    }
}</code></pre>
        </div>
    </section>

    <!-- Startup Configuration -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">7. Startup Configuration</h2>
        
        <div class="code-block mb-6">
            <pre class="language-csharp"><code>// Program.cs (.NET 6+)
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.IdentityModel.Tokens;
using System.Text;

var builder = WebApplication.CreateBuilder(args);

// Add services
builder.Services.AddControllersWithViews();

// Configure CAS settings
var casSettings = new CasSettings();
builder.Configuration.GetSection("CasSettings").Bind(casSettings);
builder.Services.AddSingleton(casSettings);

// Add HTTP client
builder.Services.AddHttpClient&lt;CasClient&gt;();

// Add session
builder.Services.AddSession(options =>
{
    options.IdleTimeout = TimeSpan.FromMinutes(120);
    options.Cookie.HttpOnly = true;
    options.Cookie.IsEssential = true;
});

// Add JWT authentication (optional, for API endpoints)
builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(options =>
    {
        options.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuerSigningKey = true,
            IssuerSigningKey = new SymmetricSecurityKey(Encoding.ASCII.GetBytes(casSettings.SignatureSecret)),
            ValidateIssuer = false,
            ValidateAudience = false,
            ValidateLifetime = true,
            ClockSkew = TimeSpan.Zero
        };
    });

var app = builder.Build();

// Configure pipeline
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Home/Error");
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseStaticFiles();
app.UseRouting();

app.UseSession();
app.UseAuthentication();
app.UseAuthorization();

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Home}/{action=Index}/{id?}");

app.Run();</code></pre>
        </div>
    </section>

    <!-- Views -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-4">8. View Examples</h2>
        
        <h3 class="text-xl font-semibold mb-3">Dashboard View</h3>
        <div class="code-block mb-6">
            <pre class="language-html"><code>@@* Views/Home/Index.cshtml *@@
@@model CasUser

@@{
    ViewData["Title"] = "Dashboard";
}

&lt;div class="container"&gt;
    &lt;div class="row"&gt;
        &lt;div class="col-md-12"&gt;
            &lt;h1&gt;Welcome, @@Model.FullName!&lt;/h1&gt;
            
            &lt;div class="card"&gt;
                &lt;div class="card-header"&gt;
                    &lt;h3&gt;User Information&lt;/h3&gt;
                &lt;/div&gt;
                &lt;div class="card-body"&gt;
                    &lt;p&gt;&lt;strong&gt;Username:&lt;/strong&gt; @@Model.Username&lt;/p&gt;
                    &lt;p&gt;&lt;strong&gt;Email:&lt;/strong&gt; @@Model.Email&lt;/p&gt;
                    &lt;p&gt;&lt;strong&gt;Role:&lt;/strong&gt; @@Model.Role&lt;/p&gt;
                    &lt;p&gt;&lt;strong&gt;First Name:&lt;/strong&gt; @@Model.FirstName&lt;/p&gt;
                    &lt;p&gt;&lt;strong&gt;Last Name:&lt;/strong&gt; @@Model.LastName&lt;/p&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            
            &lt;div class="mt-4"&gt;
                @@if (Model.IsAdmin)
                {
                    &lt;a href="@@Url.Action("Admin")" class="btn btn-primary"&gt;Admin Panel&lt;/a&gt;
                }
                &lt;a href="@@Url.Action("Logout")" class="btn btn-secondary"&gt;Logout&lt;/a&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>

        <h3 class="text-xl font-semibold mb-3">Layout with Authentication</h3>
        <div class="code-block mb-6">
            <pre class="language-html"><code>@@* Views/Shared/_Layout.cshtml *@@
&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;meta charset="utf-8" /&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0" /&gt;
    &lt;title&gt;@@ViewData["Title"] - CAS Demo&lt;/title&gt;
    &lt;link rel="stylesheet" href="~/lib/bootstrap/dist/css/bootstrap.min.css" /&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;header&gt;
        &lt;nav class="navbar navbar-expand-sm navbar-toggleable-sm navbar-light bg-white border-bottom box-shadow mb-3"&gt;
            &lt;div class="container-fluid"&gt;
                &lt;a class="navbar-brand" asp-area="" asp-controller="Home" asp-action="Index"&gt;CAS Demo&lt;/a&gt;
                &lt;button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".navbar-collapse"&gt;
                    &lt;span class="navbar-toggler-icon"&gt;&lt;/span&gt;
                &lt;/button&gt;
                &lt;div class="navbar-collapse collapse d-sm-inline-flex justify-content-between"&gt;
                    &lt;ul class="navbar-nav flex-grow-1"&gt;
                        &lt;li class="nav-item"&gt;
                            &lt;a class="nav-link text-dark" asp-area="" asp-controller="Home" asp-action="Index"&gt;Home&lt;/a&gt;
                        &lt;/li&gt;
                    &lt;/ul&gt;
                    &lt;ul class="navbar-nav"&gt;
                        @@{
                            var casUser = Context.Session.GetString("CasUser");
                            if (!string.IsNullOrEmpty(casUser))
                            {
                                var user = Newtonsoft.Json.JsonConvert.DeserializeObject&lt;CasUser&gt;(casUser);
                                &lt;li class="nav-item"&gt;
                                    &lt;span class="nav-link"&gt;Welcome, @@user.FirstName!&lt;/span&gt;
                                &lt;/li&gt;
                                &lt;li class="nav-item"&gt;
                                    &lt;a class="nav-link text-dark" asp-area="" asp-controller="Home" asp-action="Logout"&gt;Logout&lt;/a&gt;
                                &lt;/li&gt;
                            }
                            else
                            {
                                &lt;li class="nav-item"&gt;
                                    &lt;a class="nav-link text-dark" asp-area="" asp-controller="Home" asp-action="Login"&gt;Login&lt;/a&gt;
                                &lt;/li&gt;
                            }
                        }
                    &lt;/ul&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/nav&gt;
    &lt;/header&gt;
    
    &lt;div class="container"&gt;
        &lt;main role="main" class="pb-3"&gt;
            @@RenderBody()
        &lt;/main&gt;
    &lt;/div&gt;
    
    &lt;script src="~/lib/jquery/dist/jquery.min.js"&gt;&lt;/script&gt;
    &lt;script src="~/lib/bootstrap/dist/js/bootstrap.bundle.min.js"&gt;&lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
        </div>
    </section>

    <!-- Next Steps -->
    <div class="bg-blue-50 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Next Steps</h2>
        <ul class="space-y-2">
            <li>• <a href="/docs/api" class="text-blue-600 hover:text-blue-800">Explore the API Reference</a></li>
            <li>• <a href="/docs/examples" class="text-blue-600 hover:text-blue-800">View More Examples</a></li>
            <li>• <a href="/" class="text-blue-600 hover:text-blue-800">Test with CAS Dashboard</a></li>
        </ul>
    </div>
</div>
@endsection
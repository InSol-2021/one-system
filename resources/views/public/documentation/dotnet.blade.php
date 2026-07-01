@extends('public.documentation.layout')

@section('title', '.NET / C# integration — CAS SSO')
@section('description', 'Integrate ASP.NET Core applications with CAS single sign-on using the CasSystem.Client package.')

@section('content')
@php
    // Highlight palette tuned to the ONE codeblock surface (dark ink background).
    $kw  = 'color:#c7d2fe';        // keywords / accent-tinted
    $str = 'color:#a5b4fc';        // strings
    $fn  = 'color:#e2e8f0';        // functions / identifiers
    $var = 'color:#cbd5e1';        // variables
    $com = 'color:#64748b';        // comments
    $num = 'color:#93c5fd';        // numbers / literals
@endphp
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <div class="flex items-center gap-3 mb-4">
            <span class="os-icon-tile os-icon-tile-ink">
                <i class="fab fa-microsoft"></i>
            </span>
            <div>
                <p class="os-eyebrow">Integration guide</p>
                <h1 class="text-3xl font-bold text-[var(--color-ink)] tracking-tight leading-tight">.NET / C#</h1>
            </div>
        </div>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed mb-4">{{ $dotnetGuide['description'] }}</p>
        <div class="flex flex-wrap gap-2">
            <span class="os-badge"><i class="fas fa-clock"></i>10 min setup</span>
            <span class="os-badge"><i class="fas fa-signal"></i>Intermediate</span>
            <span class="os-badge os-badge-accent"><i class="fas fa-tag"></i>CasSystem.Client 2.0 · .NET 8</span>
        </div>
    </div>
</section>

<nav class="mb-12 os-card os-card-pad">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">On this page</h2>
    <ol class="space-y-1.5 text-sm">
        <li><a href="#installation" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. NuGet installation</a></li>
        <li><a href="#configuration" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Register the client</a></li>
        <li><a href="#flow" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. SSO login &amp; callback</a></li>
        <li><a href="#middleware" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. Protect routes with middleware</a></li>
        <li><a href="#roles" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. Role checks &amp; logout</a></li>
    </ol>
</nav>

<section id="installation" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">1. NuGet installation</h2>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-4">
        Add the <code class="os-code-inline">CasSystem.Client</code> package. It targets .NET 8 and ships the
        <code class="os-code-inline">CasClient</code>, <code class="os-code-inline">CasConfig</code>, and
        <code class="os-code-inline">CasAuthMiddleware</code> types. It references the
        <code class="os-code-inline">Microsoft.AspNetCore.App</code> shared framework, so add it to an ASP.NET Core (web) project.
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>.NET CLI</span></div>
        <pre><code>dotnet add package CasSystem.Client</code></pre>
    </div>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed mt-4 mb-2">Or via the Package Manager console:</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Package Manager</span></div>
        <pre><code>Install-Package CasSystem.Client</code></pre>
    </div>
</section>

<section id="configuration" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">2. Register the client</h2>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-4">
        Configure CAS with a <code class="os-code-inline">CasConfig</code> object and register it in the DI container
        with <code class="os-code-inline">AddCasClient(...)</code>. The client secret is sent server-to-server only, so
        keep it out of any browser-facing code. Pull values from configuration or environment in production.
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Program.cs</span></div>
        <pre><code><span style="{{ $kw }}">using</span> CasSystem.Client;

<span style="{{ $kw }}">var</span> builder = WebApplication.<span style="{{ $fn }}">CreateBuilder</span>(args);

<span style="{{ $com }}">// Register the CAS SSO client</span>
builder.Services.<span style="{{ $fn }}">AddCasClient</span>(<span style="{{ $kw }}">new</span> <span style="{{ $fn }}">CasConfig</span>
{
    ServerUrl    = <span style="{{ $str }}">"https://your-cas-server.com"</span>,
    ClientId     = <span style="{{ $str }}">"your_client_id"</span>,
    ClientSecret = <span style="{{ $str }}">"your_client_secret"</span>,
    CallbackUrl  = <span style="{{ $str }}">"https://your-app.com/cas/callback"</span>,
});

builder.Services.<span style="{{ $fn }}">AddSession</span>();

<span style="{{ $kw }}">var</span> app = builder.<span style="{{ $fn }}">Build</span>();

app.<span style="{{ $fn }}">UseSession</span>();</code></pre>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info text-[var(--color-accent)] mt-0.5"></i>
        <div>
            The <code class="os-code-inline">CallbackUrl</code> must match the value registered for this client on the
            CAS server — that is where the browser is redirected with the token appended.
        </div>
    </div>
</section>

<section id="flow" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">3. SSO login &amp; callback</h2>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-4">
        Send the browser to the CAS login URL. After authentication the server 302-redirects back to your registered
        callback with <code class="os-code-inline">?token={JWT}</code>. Read that token and validate it
        server-to-server with <code class="os-code-inline">ValidateTokenAsync</code>, which calls
        <code class="os-code-inline">POST /api/validate-token</code>. The token is single-use — validate once, then
        create your own session.
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Controllers/AuthController.cs</span></div>
        <pre><code><span style="{{ $kw }}">using</span> CasSystem.Client;
<span style="{{ $kw }}">using</span> Microsoft.AspNetCore.Mvc;
<span style="{{ $kw }}">using</span> System.Text.Json;

<span style="{{ $kw }}">public class</span> <span style="{{ $fn }}">AuthController</span> : <span style="{{ $fn }}">Controller</span>
{
    <span style="{{ $kw }}">private readonly</span> <span style="{{ $fn }}">CasClient</span> _cas;
    <span style="{{ $kw }}">public</span> <span style="{{ $fn }}">AuthController</span>(<span style="{{ $fn }}">CasClient</span> cas) => _cas = cas;

    <span style="{{ $com }}">// GET /cas/login — bounce the user to the CAS server</span>
    [<span style="{{ $fn }}">HttpGet</span>(<span style="{{ $str }}">"/cas/login"</span>)]
    <span style="{{ $kw }}">public</span> <span style="{{ $fn }}">IActionResult</span> <span style="{{ $fn }}">Login</span>() =>
        <span style="{{ $fn }}">Redirect</span>(_cas.<span style="{{ $fn }}">GetLoginUrl</span>());

    <span style="{{ $com }}">// GET /cas/callback?token=... — validate and start a session</span>
    [<span style="{{ $fn }}">HttpGet</span>(<span style="{{ $str }}">"/cas/callback"</span>)]
    <span style="{{ $kw }}">public async</span> Task&lt;<span style="{{ $fn }}">IActionResult</span>&gt; <span style="{{ $fn }}">Callback</span>([<span style="{{ $fn }}">FromQuery</span>] <span style="{{ $kw }}">string</span> token)
    {
        <span style="{{ $kw }}">var</span> user = <span style="{{ $kw }}">await</span> _cas.<span style="{{ $fn }}">ValidateTokenAsync</span>(token);
        <span style="{{ $kw }}">if</span> (user == <span style="{{ $kw }}">null</span>) <span style="{{ $kw }}">return</span> <span style="{{ $fn }}">Unauthorized</span>();

        HttpContext.Session.<span style="{{ $fn }}">SetString</span>(<span style="{{ $str }}">"cas_user"</span>, JsonSerializer.<span style="{{ $fn }}">Serialize</span>(user));
        <span style="{{ $kw }}">return</span> <span style="{{ $fn }}">Redirect</span>(<span style="{{ $str }}">"/dashboard"</span>);
    }
}</code></pre>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info text-[var(--color-accent)] mt-0.5"></i>
        <div>
            <code class="os-code-inline">ValidateTokenAsync</code> returns a
            <code class="os-code-inline">Dictionary&lt;string, object&gt;?</code> of the user (<code class="os-code-inline">id</code>,
            <code class="os-code-inline">username</code>, <code class="os-code-inline">email</code>, <code class="os-code-inline">roles</code>),
            or <code class="os-code-inline">null</code> when the CAS server responds with
            <code class="os-code-inline">valid: false</code> or a non-200 status.
        </div>
    </div>
</section>

<section id="middleware" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">4. Protect routes with middleware</h2>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-4">
        Register <code class="os-code-inline">CasAuthMiddleware</code> to guard a set of paths. It reads the user from
        the session or a <code class="os-code-inline">Bearer</code> token, validating against the CAS server when needed,
        and exposes the user on <code class="os-code-inline">HttpContext.Items["cas_user"]</code>.
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Program.cs (continued)</span></div>
        <pre><code><span style="{{ $com }}">// Guard /dashboard and /admin; redirect anonymous users to /cas/login</span>
app.<span style="{{ $fn }}">UseMiddleware</span>&lt;<span style="{{ $fn }}">CasAuthMiddleware</span>&gt;(
    <span style="{{ $kw }}">new</span>[] { <span style="{{ $str }}">"/dashboard"</span>, <span style="{{ $str }}">"/admin"</span> },
    <span style="{{ $str }}">"/cas/login"</span>);

app.<span style="{{ $fn }}">MapGet</span>(<span style="{{ $str }}">"/dashboard"</span>, (<span style="{{ $fn }}">HttpContext</span> ctx) =>
{
    <span style="{{ $kw }}">var</span> user = ctx.Items[<span style="{{ $str }}">"cas_user"</span>] <span style="{{ $kw }}">as</span> <span style="{{ $fn }}">Dictionary</span>&lt;<span style="{{ $kw }}">string</span>, <span style="{{ $kw }}">object</span>&gt;;
    <span style="{{ $kw }}">return</span> Results.<span style="{{ $fn }}">Json</span>(<span style="{{ $kw }}">new</span> { user });
});

app.<span style="{{ $fn }}">Run</span>();</code></pre>
    </div>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed mt-4 mb-2">
        In an MVC controller, read the same item from <code class="os-code-inline">HttpContext.Items</code>:
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Controllers/DashboardController.cs</span></div>
        <pre><code><span style="{{ $kw }}">public class</span> <span style="{{ $fn }}">DashboardController</span> : <span style="{{ $fn }}">Controller</span>
{
    <span style="{{ $kw }}">public</span> <span style="{{ $fn }}">IActionResult</span> <span style="{{ $fn }}">Index</span>()
    {
        <span style="{{ $kw }}">var</span> user = HttpContext.Items[<span style="{{ $str }}">"cas_user"</span>] <span style="{{ $kw }}">as</span> <span style="{{ $fn }}">Dictionary</span>&lt;<span style="{{ $kw }}">string</span>, <span style="{{ $kw }}">object</span>&gt;;
        <span style="{{ $kw }}">if</span> (user == <span style="{{ $kw }}">null</span>) <span style="{{ $kw }}">return</span> <span style="{{ $fn }}">Unauthorized</span>();
        <span style="{{ $kw }}">return</span> <span style="{{ $fn }}">View</span>(user);
    }
}</code></pre>
    </div>
</section>

<section id="roles" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">5. Role checks &amp; logout</h2>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-4">
        <code class="os-code-inline">CasClient</code> exposes static helpers for role-based access and an async
        <code class="os-code-inline">LogoutAsync</code> that clears the cache and calls
        <code class="os-code-inline">POST /api/logout</code>.
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Role checks &amp; logout</span></div>
        <pre><code><span style="{{ $com }}">// Role helpers (static)</span>
<span style="{{ $kw }}">bool</span> isAdmin = <span style="{{ $fn }}">CasClient</span>.<span style="{{ $fn }}">UserHasRole</span>(user, <span style="{{ $str }}">"admin"</span>);
<span style="{{ $kw }}">bool</span> canEdit = <span style="{{ $fn }}">CasClient</span>.<span style="{{ $fn }}">UserHasAnyRole</span>(user, <span style="{{ $str }}">"admin"</span>, <span style="{{ $str }}">"editor"</span>);
<span style="{{ $kw }}">bool</span> full    = <span style="{{ $fn }}">CasClient</span>.<span style="{{ $fn }}">UserHasAllRoles</span>(user, <span style="{{ $str }}">"admin"</span>, <span style="{{ $str }}">"billing"</span>);

<span style="{{ $com }}">// Logout — clears the token cache and notifies the CAS server</span>
<span style="{{ $kw }}">await</span> _cas.<span style="{{ $fn }}">LogoutAsync</span>(token);
HttpContext.Session.<span style="{{ $fn }}">Clear</span>();</code></pre>
    </div>

    <p class="text-sm text-[var(--color-muted)] leading-relaxed mt-6 mb-3">
        For service-to-service flows you can mint a token for a user with
        <code class="os-code-inline">GenerateSSOTokenAsync</code> (the CAS server must IP-whitelist the caller):
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Service-to-service issuance</span></div>
        <pre><code><span style="{{ $com }}">// POST /api/sso/token { client_id, client_secret, username }</span>
<span style="{{ $kw }}">var</span> result = <span style="{{ $kw }}">await</span> _cas.<span style="{{ $fn }}">GenerateSSOTokenAsync</span>(<span style="{{ $str }}">"jdoe"</span>);
<span style="{{ $kw }}">var</span> token = result?[<span style="{{ $str }}">"token"</span>]?.<span style="{{ $fn }}">ToString</span>();</code></pre>
    </div>

    <div class="os-alert os-alert-success mt-6">
        <i class="fas fa-check-circle mt-0.5"></i>
        <div><strong>Done.</strong> Requests to protected paths now validate CAS tokens, hydrate the user on
            <code class="os-code-inline">HttpContext.Items["cas_user"]</code>, and enforce roles. Serve over HTTPS in production.</div>
    </div>
</section>

<section class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-4">CasClient API reference</h2>
    <div class="os-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--color-line)] bg-[var(--color-surface-2)] text-left">
                    <th class="px-4 py-2.5 font-semibold text-[var(--color-ink-2)]">Method</th>
                    <th class="px-4 py-2.5 font-semibold text-[var(--color-ink-2)]">Description</th>
                </tr>
            </thead>
            <tbody class="text-[var(--color-muted)]">
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">GetLoginUrl(returnUrl?)</code></td><td class="px-4 py-2.5">Build the CAS <code class="os-code-inline">/sso/login</code> URL for this client.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">ValidateTokenAsync(token)</code></td><td class="px-4 py-2.5">Validate a token via <code class="os-code-inline">/api/validate-token</code>; returns the user dictionary or <code class="os-code-inline">null</code>.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">GenerateSSOTokenAsync(username)</code></td><td class="px-4 py-2.5">Mint an SSO token for a user (service-to-service, IP-whitelisted).</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">GetUserFromToken(token)</code></td><td class="px-4 py-2.5">Return the cached user for a token, if still valid.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">LogoutAsync(token?)</code></td><td class="px-4 py-2.5">Clear the cache and call <code class="os-code-inline">/api/logout</code>.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">UserHasRole(user, role)</code></td><td class="px-4 py-2.5">Static — check a single role.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">UserHasAnyRole(user, ...roles)</code></td><td class="px-4 py-2.5">Static — check any of the given roles.</td></tr>
                <tr><td class="px-4 py-2.5"><code class="os-code-inline">UserHasAllRoles(user, ...roles)</code></td><td class="px-4 py-2.5">Static — check all of the given roles.</td></tr>
            </tbody>
        </table>
    </div>
</section>

<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Next steps</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('docs.api.overview') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <i class="fas fa-code text-[var(--color-muted)] text-sm"></i><span class="text-sm font-medium text-[var(--color-ink-2)]">API reference</span>
        </a>
        <a href="{{ route('docs.security') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <i class="fas fa-shield-halved text-[var(--color-muted)] text-sm"></i><span class="text-sm font-medium text-[var(--color-ink-2)]">Security guide</span>
        </a>
        <a href="{{ route('docs.deployment') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <i class="fas fa-server text-[var(--color-muted)] text-sm"></i><span class="text-sm font-medium text-[var(--color-ink-2)]">Deployment</span>
        </a>
    </div>
</section>
@endsection

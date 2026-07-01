@extends('public.documentation.layout')

@section('title', 'API reference — One System SSO')
@section('description', 'Complete REST API reference for the One System single sign-on platform: SSO token issuance, server-to-server token validation, and the browser login flow.')

@section('content')
<div class="os-container !px-0 !max-w-none">

{{-- Hero --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <p class="os-eyebrow mb-3">Reference</p>
    <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">API reference</h1>
    <p class="text-lg text-[var(--color-muted)] leading-relaxed">The complete REST API for One System single sign-on. Browser flows authenticate the user and hand your callback a single-use token; your server then exchanges that token for the user over a back channel.</p>
    <div class="flex flex-wrap gap-2.5 mt-6">
        <span class="os-badge"><i class="fas fa-network-wired text-xs"></i>IP whitelisted</span>
        <span class="os-badge"><i class="fas fa-ban text-xs"></i>Account lockout</span>
        <span class="os-badge"><i class="fas fa-gauge-high text-xs"></i>Rate limited</span>
    </div>
</section>

{{-- Base URL & security --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Base URL &amp; security</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">Every endpoint below is relative to your One System origin. Serve it over HTTPS in production so tokens are never sent in clear text.</p>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>base url</span>
            <span>HTTPS</span>
        </div>
        <pre><code>https://your-one-system.com</code></pre>
    </div>
    <div class="os-alert os-alert-warning">
        <i class="fas fa-triangle-exclamation mt-0.5"></i>
        <div>
            <strong class="block mb-2">Security requirements</strong>
            <ul class="space-y-1.5">
                <li>IP whitelisting — service-to-service IPs must be registered in the admin panel.</li>
                <li>Rate limiting — login endpoints are throttled per IP address.</li>
                <li>Account lockout — five failed login attempts triggers a 30-minute cooldown.</li>
                <li>Server-to-server calls require a valid <code class="os-code-inline">client_id</code> and <code class="os-code-inline">client_secret</code>. Secrets are stored hashed and shown only once on creation or regeneration.</li>
            </ul>
        </div>
    </div>
</section>

{{-- Browser SSO flow --}}
<section class="mb-12" id="web-sso">
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="os-badge os-badge-accent">GET</span>
        <code class="text-base font-mono font-semibold text-[var(--color-ink)]">/sso/login</code>
    </div>
    <p class="text-sm text-[var(--color-muted)] mb-6">Start a browser-based SSO login. Redirect the user here; One System authenticates them and 302-redirects the browser back to your registered <code class="os-code-inline">callback_url</code> with the token appended as a query parameter.</p>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Query parameters</h3>
    <div class="os-card overflow-hidden !p-0 mb-6">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Parameter</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-accent)]">client_id</td><td class="px-5 py-3 text-[var(--color-muted)]">Your registered client application ID. The callback destination is the <code class="os-code-inline">callback_url</code> registered against this client.</td></tr>
            </tbody>
        </table>
    </div>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Callback</h3>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>browser redirect</span>
            <span>HTTP</span>
        </div>
        <pre><code># After a successful login the browser is 302-redirected to your registered callback:
{callback_url}?token=eyJhbGciOiJIUzI1NiIs...

# At the callback, read the "token" query param and validate it server-to-server
# via POST /api/validate-token (never decode or trust the token in the browser).</code></pre>
    </div>
</section>

{{-- POST /api/validate-token --}}
<section class="mb-12" id="validate-token">
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="os-badge os-badge-accent">POST</span>
        <code class="text-base font-mono font-semibold text-[var(--color-ink)]">/api/validate-token</code>
    </div>
    <p class="text-sm text-[var(--color-muted)] mb-3">Validate the token your callback received and retrieve the authenticated user. This is a server-to-server call that requires the <code class="os-code-inline">client_secret</code> — never run it in a browser. Tokens are single-use: once validated they cannot be reused, so validate once and then create your application's own session.</p>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Request body</h3>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>request</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "client_id": "your_client_id",
  "client_secret": "your_client_secret"
}</code></pre>
    </div>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Success response <span class="text-[var(--color-success)]">200</span></h3>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>200 OK</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "valid": true,
  "user": {
    "id": 1,
    "username": "john_doe",
    "email": "john@example.com"
  },
  "expires_at": "2026-03-10 22:30:00"
}</code></pre>
    </div>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Error response</h3>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>401 Unauthorized</span>
            <span>JSON</span>
        </div>
        <pre><code>// Invalid, expired, or already-consumed token
{ "error": "Invalid or expired token" }</code></pre>
    </div>
</section>

{{-- POST /api/sso/token --}}
<section class="mb-12" id="sso-token">
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="os-badge os-badge-accent">POST</span>
        <code class="text-base font-mono font-semibold text-[var(--color-ink)]">/api/sso/token</code>
    </div>
    <p class="text-sm text-[var(--color-muted)] mb-6">Issue an SSO token for a known user without a browser round trip. This is a service-to-service call authenticated with client credentials, restricted to whitelisted IPs. Use it to seamlessly log a user into a client app from a trusted backend.</p>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Request body</h3>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>request</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "client_id": "your_client_id",
  "client_secret": "your_client_secret",
  "username": "john_doe"
}</code></pre>
    </div>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Success response <span class="text-[var(--color-success)]">200</span></h3>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>200 OK</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "redirect_url": "https://your-app.com/cas/callback?token=eyJhbG...",
  "token": "eyJhbGciOiJIUzI1NiIs..."
}</code></pre>
    </div>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Error responses</h3>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>4xx</span>
            <span>JSON</span>
        </div>
        <pre><code>// 401 — Invalid client credentials
{ "error": "Invalid client credentials" }

// 403 — Calling IP is not whitelisted
{ "error": "IP address not authorized" }

// 404 — User not found or inactive
{ "error": "User not found or inactive" }</code></pre>
    </div>
</section>

{{-- POST /api/login --}}
<section class="mb-12" id="api-login">
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="os-badge os-badge-accent">POST</span>
        <code class="text-base font-mono font-semibold text-[var(--color-ink)]">/api/login</code>
    </div>
    <p class="text-sm text-[var(--color-muted)] mb-6">Authenticate a user directly with their credentials. Returns the user record on success. Subject to rate limiting and account lockout.</p>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Request body</h3>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>request</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "login": "john_doe",
  "password": "your_password"
}</code></pre>
    </div>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Success response <span class="text-[var(--color-success)]">200</span></h3>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head">
            <span>200 OK</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "success": true,
  "user": {
    "id": 1,
    "username": "john_doe",
    "email": "john@example.com",
    "role": "user",
    "full_name": "John Doe"
  }
}</code></pre>
    </div>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Error responses</h3>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>4xx</span>
            <span>JSON</span>
        </div>
        <pre><code>// 401 — Invalid credentials
{ "error": "Invalid credentials" }

// 423 — Account locked (too many failed attempts)
{ "error": "Account locked", "remaining_minutes": 25 }

// 429 — Rate limited
{ "error": "Too many attempts", "retry_after": 45 }</code></pre>
    </div>
</section>

{{-- GET /api/user --}}
<section class="mb-12" id="get-user">
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="os-badge os-badge-accent">GET</span>
        <code class="text-base font-mono font-semibold text-[var(--color-ink)]">/api/user</code>
    </div>
    <p class="text-sm text-[var(--color-muted)] mb-6">Retrieve the currently authenticated user's profile from the active session.</p>

    <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">Response <span class="text-[var(--color-success)]">200</span></h3>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>200 OK</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "id": 1,
  "username": "john_doe",
  "email": "john@example.com",
  "role": "user",
  "full_name": "John Doe"
}</code></pre>
    </div>
</section>

{{-- POST /api/logout --}}
<section class="mb-12" id="api-logout">
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="os-badge os-badge-accent">POST</span>
        <code class="text-base font-mono font-semibold text-[var(--color-ink)]">/api/logout</code>
    </div>
    <p class="text-sm text-[var(--color-muted)] mb-6">End the current session. Returns <code class="os-code-inline">200</code> on success.</p>
    <div class="os-alert">
        <i class="fas fa-circle-info text-[var(--color-accent)] mt-0.5"></i>
        <div>Admin and user management routes now require an authenticated session. Unauthenticated requests are rejected before reaching the handler.</div>
    </div>
</section>

{{-- Status codes --}}
<section class="mb-12" id="status-codes">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">HTTP status codes</h2>
    <div class="os-card overflow-hidden !p-0">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Code</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Meaning</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-success)]">200</td><td class="px-5 py-3 text-[var(--color-muted)]">Success</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-success)]">201</td><td class="px-5 py-3 text-[var(--color-muted)]">Created</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-warning)]">400</td><td class="px-5 py-3 text-[var(--color-muted)]">Bad request — validation error</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-danger)]">401</td><td class="px-5 py-3 text-[var(--color-muted)]">Unauthorized — invalid credentials or token</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-danger)]">403</td><td class="px-5 py-3 text-[var(--color-muted)]">Forbidden — IP not whitelisted</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-danger)]">404</td><td class="px-5 py-3 text-[var(--color-muted)]">Not found — user or resource not found</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-danger)]">423</td><td class="px-5 py-3 text-[var(--color-muted)]">Locked — account lockout active</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-danger)]">429</td><td class="px-5 py-3 text-[var(--color-muted)]">Too many requests — rate limited</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-danger)]">500</td><td class="px-5 py-3 text-[var(--color-muted)]">Internal server error</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Rate limits --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Rate limits</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="os-card os-card-pad">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Authentication</h3>
            <p class="text-2xl font-semibold text-[var(--color-ink)]">10<span class="text-sm font-normal text-[var(--color-faint)]"> /min</span></p>
            <p class="text-xs text-[var(--color-muted)] mt-1">Per IP address</p>
        </div>
        <div class="os-card os-card-pad">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Token validation</h3>
            <p class="text-2xl font-semibold text-[var(--color-ink)]">100<span class="text-sm font-normal text-[var(--color-faint)]"> /min</span></p>
            <p class="text-xs text-[var(--color-muted)] mt-1">Per client application</p>
        </div>
        <div class="os-card os-card-pad">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">User management</h3>
            <p class="text-2xl font-semibold text-[var(--color-ink)]">50<span class="text-sm font-normal text-[var(--color-faint)]"> /min</span></p>
            <p class="text-xs text-[var(--color-muted)] mt-1">Per authenticated user</p>
        </div>
    </div>
</section>

</div>
@endsection

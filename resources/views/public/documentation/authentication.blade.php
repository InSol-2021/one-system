@extends('public.documentation.layout')

@section('title', 'Authentication flows — One System')
@section('description', 'How One System issues, validates, and manages SSO tokens across client applications.')

@section('content')
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <p class="os-eyebrow mb-3">Advanced topics</p>
        <h1 class="text-4xl font-bold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Authentication flows</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">How tokens are issued, validated, and managed across client applications using the One System SSO protocol.</p>
    </div>
</section>

{{-- SSO Flow --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-5">Browser SSO login flow</h2>
    <div class="space-y-0">
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold">1</div>
                <div class="w-px h-full bg-[var(--color-line)]"></div>
            </div>
            <div class="pb-8">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Redirect the browser to One System</h3>
                <p class="text-sm text-[var(--color-muted)]">Send the user to <code class="os-code-inline">GET {CAS_BASE}/sso/login?client_id={CLIENT_ID}</code>. One System authenticates the user, checking credentials, reCAPTCHA, lockout rules, and 2FA where enabled.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold">2</div>
                <div class="w-px h-full bg-[var(--color-line)]"></div>
            </div>
            <div class="pb-8">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Server redirects back with a token</h3>
                <p class="text-sm text-[var(--color-muted)]">On success, the server 302-redirects the browser to the client's registered callback URL with a signed JWT appended: <code class="os-code-inline">{callback_url}?token={JWT}</code>.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold">3</div>
                <div class="w-px h-full bg-[var(--color-line)]"></div>
            </div>
            <div class="pb-8">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Read the token at the callback</h3>
                <p class="text-sm text-[var(--color-muted)]">Your callback route reads the <code class="os-code-inline">token</code> query parameter from the incoming request.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold">4</div>
                <div class="w-px h-full bg-[var(--color-line)]"></div>
            </div>
            <div class="pb-8">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Validate server-to-server</h3>
                <p class="text-sm text-[var(--color-muted)]">From your backend, call <code class="os-code-inline">POST {CAS_BASE}/api/validate-token</code> with the token plus your <code class="os-code-inline">client_id</code> and <code class="os-code-inline">client_secret</code>. The secret must never touch the browser. The token is single-use.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 bg-[var(--color-accent)] text-white rounded-full flex items-center justify-center text-xs font-semibold">5</div>
            </div>
            <div class="pb-4">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Establish the app's own session</h3>
                <p class="text-sm text-[var(--color-muted)]">A <code class="os-code-inline">200</code> response returns the authenticated user. Create your application's own session from that data. The user is now signed in.</p>
            </div>
        </div>
    </div>
</section>

{{-- Validate endpoint --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-5">Validate token (server-to-server)</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Validation requires the client secret, so it must run from your backend — never from browser-side code.</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> {CAS_BASE}/api/validate-token</span>
            <span>json</span>
        </div>
        <pre><code>{
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "client_id": "YOUR_CLIENT_ID",
  "client_secret": "YOUR_CLIENT_SECRET"
}</code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>200 OK — valid token</span>
            <span>json</span>
        </div>
        <pre><code>{
  "valid": true,
  "user": {
    "id": 42,
    "username": "jdoe",
    "email": "jdoe@example.com"
  },
  "expires_at": "2026-06-18T12:00:00Z"
}</code></pre>
    </div>
    <p class="text-sm text-[var(--color-muted)] mt-3">An invalid, expired, or already-used token returns <code class="os-code-inline">401</code> with <code class="os-code-inline">{ "error": "..." }</code>.</p>
</section>

{{-- Service-to-service issuance --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-5">Service-to-service token issuance</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">For trusted back-channel sign-in, a whitelisted server can request a token directly. This endpoint is IP-whitelisted.</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> {CAS_BASE}/api/sso/token</span>
            <span>json</span>
        </div>
        <pre><code>{
  "client_id": "YOUR_CLIENT_ID",
  "client_secret": "YOUR_CLIENT_SECRET",
  "username": "jdoe"
}</code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>200 OK</span>
            <span>json</span>
        </div>
        <pre><code>{
  "redirect_url": "https://portal.company.com/cas/callback?token=eyJhbGci...",
  "token": "eyJhbGciOiJIUzI1NiIs..."
}</code></pre>
    </div>
</section>

{{-- Token Lifecycle --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-5">Token lifecycle</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="os-card p-5">
            <div class="os-icon-tile os-icon-tile-ink mb-3"><i class="fas fa-circle-plus"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Issued</h3>
            <p class="text-xs text-[var(--color-muted)]">A signed JWT (HS256, server secret) is created after successful authentication and carries the user's claims.</p>
        </div>
        <div class="os-card p-5">
            <div class="os-icon-tile os-icon-tile-ink mb-3"><i class="fas fa-circle-check"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Validated once</h3>
            <p class="text-xs text-[var(--color-muted)]">Clients verify the token at <code class="os-code-inline">/api/validate-token</code> before granting access. Tokens are single-use — validate once, then create your own session.</p>
        </div>
        <div class="os-card p-5">
            <div class="os-icon-tile os-icon-tile-ink mb-3"><i class="fas fa-circle-xmark"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Expired / consumed</h3>
            <p class="text-xs text-[var(--color-muted)]">Tokens expire at <code class="os-code-inline">expires_at</code> or once consumed. Reuse or expiry returns <code class="os-code-inline">401</code>.</p>
        </div>
    </div>
</section>

{{-- 2FA Flow --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-5">Two-factor authentication</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">When 2FA is enabled, the login flow inserts an extra verification step before a full token is issued:</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>2FA challenge flow</span>
            <span>http</span>
        </div>
        <pre><code><span class="text-[var(--color-faint)]">// Step 1: Initial login returns a 2FA challenge</span>
{
  "requires_2fa": true,
  "temp_token": "temp_eyJhbGci..."
}

<span class="text-[var(--color-faint)]">// Step 2: Submit the TOTP code with the temp token</span>
POST {CAS_BASE}/api/sso/verify-2fa
{
  "temp_token": "temp_eyJhbGci...",
  "totp_code": "123456"
}

<span class="text-[var(--color-faint)]">// Step 3: Receive the full JWT token</span>
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIs..."
}</code></pre>
    </div>
</section>

{{-- Other endpoints --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-5">Other authentication endpoints</h2>
    <div class="os-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Method</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Endpoint</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Purpose</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr><td class="px-5 py-3"><span class="os-badge">POST</span></td><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/login</td><td class="px-5 py-3 text-[var(--color-muted)]">Direct credential login with <code class="os-code-inline">login</code> and <code class="os-code-inline">password</code>.</td></tr>
                <tr><td class="px-5 py-3"><span class="os-badge">POST</span></td><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/logout</td><td class="px-5 py-3 text-[var(--color-muted)]">End the session and invalidate the active token.</td></tr>
                <tr><td class="px-5 py-3"><span class="os-badge">GET</span></td><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/user</td><td class="px-5 py-3 text-[var(--color-muted)]">Return the authenticated user's profile.</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Logout --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-5">Logout &amp; session termination</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Logging out invalidates the user's token so it can no longer be validated by any client.</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> {CAS_BASE}/api/logout</span>
            <span>json</span>
        </div>
        <pre><code>{
  "token": "eyJhbGciOiJIUzI1NiIs..."
}</code></pre>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info mt-0.5"></i>
        <span>Serve every endpoint over HTTPS in production and configure <code class="os-code-inline">JWT_SECRET</code> and <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> via environment variables.</span>
    </div>
</section>
@endsection

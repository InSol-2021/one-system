@extends('public.documentation.layout')

@section('title', 'Security — One System SSO')
@section('description', 'How One System protects authentication: HS256 JWTs, single-use SSO tokens, server-to-server validation, 2FA, reCAPTCHA v3, and audit logging.')

@section('content')
<div class="os-container !px-0 !max-w-none">

{{-- Hero --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <p class="os-eyebrow mb-3">Reference</p>
    <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Security</h1>
    <p class="text-lg text-[var(--color-muted)] leading-relaxed">Multiple layers of defense protect your authentication infrastructure. Tokens are short-lived and single-use, validation happens server to server, and every sensitive event is recorded.</p>
</section>

{{-- Defense layers --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-6">Defense layers</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="os-card os-card-pad">
            <div class="os-icon-tile mb-4"><i class="fas fa-key text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Single-use SSO tokens</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">The token returned to a client callback is validated exactly once. After a successful <code class="os-code-inline">POST /api/validate-token</code> the token is consumed, and the client creates its own session.</p>
        </div>
        <div class="os-card os-card-pad">
            <div class="os-icon-tile mb-4"><i class="fas fa-server text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Server-to-server validation</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Token validation requires the <code class="os-code-inline">client_secret</code> and must never run in a browser. One System verifies the token and returns the authenticated user only over a back-channel request.</p>
        </div>
        <div class="os-card os-card-pad">
            <div class="os-icon-tile mb-4"><i class="fas fa-mobile-alt text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Two-factor authentication</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">TOTP-based 2FA with QR-code setup and backup recovery codes. Compatible with Google Authenticator, Authy, and similar apps.</p>
        </div>
        <div class="os-card os-card-pad">
            <div class="os-icon-tile mb-4"><i class="fas fa-gauge-high text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Rate limiting</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Authentication and validation endpoints are throttled per client to blunt brute-force and credential-stuffing attempts. Throttled requests return <code class="os-code-inline">429</code> with a retry header.</p>
        </div>
        <div class="os-card os-card-pad">
            <div class="os-icon-tile mb-4"><i class="fas fa-robot text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">reCAPTCHA v3</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Google reCAPTCHA v3 on the login form blocks automated credential-stuffing without user friction. Configure keys with the <code class="os-code-inline">RECAPTCHAV3_*</code> environment variables.</p>
        </div>
        <div class="os-card os-card-pad">
            <div class="os-icon-tile mb-4"><i class="fas fa-network-wired text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">IP whitelisting</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Service-to-service token issuance (<code class="os-code-inline">POST /api/sso/token</code>) is restricted to pre-registered IP addresses. Requests from unknown IPs are rejected before authentication.</p>
        </div>
    </div>
</section>

{{-- Recent hardening --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Recent hardening</h2>
    <div class="os-card os-card-pad">
        <ul class="space-y-3 text-sm text-[var(--color-ink-2)]">
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">Authentication required on admin and user routes.</strong> Management routes are no longer publicly reachable — a valid authenticated session is required.</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">Client secrets are stored hashed.</strong> A secret is shown in full only once, at creation or regeneration. Store it then — it cannot be retrieved later.</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">HTTPS in production.</strong> Serve One System and every client callback over TLS so tokens are never transmitted in clear text.</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">Secrets via environment.</strong> Set <code class="os-code-inline">JWT_SECRET</code>, <code class="os-code-inline">RECAPTCHAV3_*</code>, and <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> through the environment, and run the new migrations.</div>
            </li>
        </ul>
    </div>
</section>

{{-- JWT structure --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">JWT token structure</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">SSO tokens are JSON Web Tokens signed with <strong class="text-[var(--color-ink-2)]">HS256</strong> using the server secret (<code class="os-code-inline">JWT_SECRET</code>). A decoded payload looks like this:</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>token payload</span>
            <span>JSON</span>
        </div>
        <pre><code>{
  "sub": 42,
  "username": "john",
  "email": "john@example.com",
  "client_id": "customer-portal",
  "iat": 1710072000,
  "exp": 1710075600
}</code></pre>
    </div>
    <p class="text-sm text-[var(--color-muted)] mt-4">Always validate tokens server side via the validation endpoint rather than decoding claims in the browser. A token is single-use and short-lived: validate it once, read the returned user, then create your application's own session.</p>
</section>

{{-- Validating a token --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Validating a token</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">At your registered callback, read the <code class="os-code-inline">token</code> query parameter, then validate it from your server with the client secret:</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>php · server-to-server</span>
            <span>PHP</span>
        </div>
        <pre><code>// POST {CAS_BASE}/api/validate-token  (never from the browser)
$response = Http::post($casBase.'/api/validate-token', [
    'token'         =&gt; $request-&gt;query('token'),
    'client_id'     =&gt; config('one_system.client_id'),
    'client_secret' =&gt; config('one_system.client_secret'),
]);

if ($response-&gt;status() === 200 &amp;&amp; $response['valid']) {
    $user = $response['user']; // { id, username, email }
    // create the app's own session, then redirect
}</code></pre>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info text-[var(--color-accent)] mt-0.5"></i>
        <div>A successful response returns <code class="os-code-inline">200 &#123; valid:true, user:&#123;id,username,email&#125;, expires_at &#125;</code>. An invalid or already-consumed token returns <code class="os-code-inline">401 &#123; error &#125;</code>.</div>
    </div>
</section>

{{-- Audit logging --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Audit logging</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">Every authentication event is logged with full context:</p>
    <div class="os-card overflow-hidden !p-0">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Field</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">user_id</td><td class="px-5 py-3 text-[var(--color-muted)]">Authenticated user identifier</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">ip_address</td><td class="px-5 py-3 text-[var(--color-muted)]">Client IP address</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">user_agent</td><td class="px-5 py-3 text-[var(--color-muted)]">Browser / device identifier</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">action</td><td class="px-5 py-3 text-[var(--color-muted)]">login, logout, failed_login, token_issued</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">client_id</td><td class="px-5 py-3 text-[var(--color-muted)]">Originating client application</td></tr>
                <tr><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">timestamp</td><td class="px-5 py-3 text-[var(--color-muted)]">ISO 8601 event timestamp</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Best practices --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Security best practices</h2>
    <div class="space-y-3">
        <div class="os-card flex items-start gap-3 p-4">
            <i class="fas fa-check-circle text-[var(--color-accent)] mt-0.5"></i>
            <div class="text-sm text-[var(--color-muted)]"><strong class="text-[var(--color-ink)]">Enforce HTTPS</strong> — never transmit tokens over plain HTTP in production.</div>
        </div>
        <div class="os-card flex items-start gap-3 p-4">
            <i class="fas fa-check-circle text-[var(--color-accent)] mt-0.5"></i>
            <div class="text-sm text-[var(--color-muted)]"><strong class="text-[var(--color-ink)]">Keep secrets server side</strong> — the <code class="os-code-inline">client_secret</code> and <code class="os-code-inline">JWT_SECRET</code> belong in environment variables, never in client-side code.</div>
        </div>
        <div class="os-card flex items-start gap-3 p-4">
            <i class="fas fa-check-circle text-[var(--color-accent)] mt-0.5"></i>
            <div class="text-sm text-[var(--color-muted)]"><strong class="text-[var(--color-ink)]">Rotate secrets</strong> — regenerate client secrets periodically; the new value is shown only once.</div>
        </div>
        <div class="os-card flex items-start gap-3 p-4">
            <i class="fas fa-check-circle text-[var(--color-accent)] mt-0.5"></i>
            <div class="text-sm text-[var(--color-muted)]"><strong class="text-[var(--color-ink)]">Enable 2FA</strong> — require two-factor authentication for all admin accounts.</div>
        </div>
        <div class="os-card flex items-start gap-3 p-4">
            <i class="fas fa-check-circle text-[var(--color-accent)] mt-0.5"></i>
            <div class="text-sm text-[var(--color-muted)]"><strong class="text-[var(--color-ink)]">Validate once, server side</strong> — treat every SSO token as single-use and never trust browser-side claims.</div>
        </div>
        <div class="os-card flex items-start gap-3 p-4">
            <i class="fas fa-check-circle text-[var(--color-accent)] mt-0.5"></i>
            <div class="text-sm text-[var(--color-muted)]"><strong class="text-[var(--color-ink)]">Monitor audit logs</strong> — set up alerts for unusual login patterns or repeated failures.</div>
        </div>
    </div>
</section>

</div>
@endsection

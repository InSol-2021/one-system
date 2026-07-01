@extends('public.documentation.layout')

@section('title', 'Webhooks — One System')
@section('description', 'Configure webhook endpoints to receive real-time notifications for One System authentication events.')

@section('content')
<div class="">

    {{-- Page header --}}
    <section class="border-b border-[var(--color-line)] pb-10 mb-12">
        <div class="flex items-center gap-2 text-sm text-[var(--color-muted)] mb-4">
            <a href="{{ route('docs') }}" class="hover:text-[var(--color-accent)]">Docs</a>
            <i class="fas fa-chevron-right text-[0.65rem] text-[var(--color-faint)]"></i>
            <span>Webhooks</span>
        </div>
        <p class="os-eyebrow mb-3">Technical reference</p>
        <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Webhooks</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">Receive real-time HTTP callbacks when authentication events occur in One System.</p>
    </section>

    {{-- Overview --}}
    <section class="mb-12">
        <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">How it works</h2>
        <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-6">When an event fires — a user sign-in, sign-out, or failed authentication attempt — One System sends a <code class="os-code-inline">POST</code> request to your registered webhook URL with a JSON payload describing the event. Every payload carries an HMAC-SHA256 signature in the <code class="os-code-inline">X-One-System-Signature</code> header so you can verify it came from One System.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="os-card os-card-pad">
                <span class="os-icon-tile os-icon-tile-ink mb-3"><i class="fas fa-bolt"></i></span>
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Real-time</h3>
                <p class="text-xs text-[var(--color-muted)]">Events fire within milliseconds of the triggering action.</p>
            </div>
            <div class="os-card os-card-pad">
                <span class="os-icon-tile os-icon-tile-ink mb-3"><i class="fas fa-rotate-right"></i></span>
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Auto retry</h3>
                <p class="text-xs text-[var(--color-muted)]">Failed deliveries are retried 3 times with exponential backoff.</p>
            </div>
            <div class="os-card os-card-pad">
                <span class="os-icon-tile os-icon-tile-ink mb-3"><i class="fas fa-shield-halved"></i></span>
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Signed</h3>
                <p class="text-xs text-[var(--color-muted)]">HMAC-SHA256 signatures prevent spoofing and tampering.</p>
            </div>
        </div>
    </section>

    {{-- Event Types --}}
    <section class="mb-12">
        <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Event types</h2>
        <div class="os-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Event</th>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Description</th>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Trigger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-line)] text-[var(--color-ink-2)]">
                    <tr>
                        <td class="px-5 py-3"><code class="os-code-inline">user.login</code></td>
                        <td class="px-5 py-3">Successful authentication</td>
                        <td class="px-5 py-3 text-[var(--color-muted)]">SSO token issued</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3"><code class="os-code-inline">user.logout</code></td>
                        <td class="px-5 py-3">User session ended</td>
                        <td class="px-5 py-3 text-[var(--color-muted)]">Token invalidated</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3"><code class="os-code-inline">user.login_failed</code></td>
                        <td class="px-5 py-3">Failed sign-in attempt</td>
                        <td class="px-5 py-3 text-[var(--color-muted)]">Invalid credentials</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3"><code class="os-code-inline">user.locked</code></td>
                        <td class="px-5 py-3">Account locked out</td>
                        <td class="px-5 py-3 text-[var(--color-muted)]">5 failed attempts</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3"><code class="os-code-inline">user.2fa_enabled</code></td>
                        <td class="px-5 py-3">2FA activated</td>
                        <td class="px-5 py-3 text-[var(--color-muted)]">User enabled TOTP</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3"><code class="os-code-inline">token.expired</code></td>
                        <td class="px-5 py-3">Token reached expiry</td>
                        <td class="px-5 py-3 text-[var(--color-muted)]">JWT TTL elapsed</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3"><code class="os-code-inline">client.registered</code></td>
                        <td class="px-5 py-3">New client system added</td>
                        <td class="px-5 py-3 text-[var(--color-muted)]">Admin action</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- Payload Example --}}
    <section class="mb-12">
        <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Payload format</h2>
        <div class="os-codeblock">
            <div class="os-codeblock-head">
                <span>user.login event payload</span>
                <span>application/json</span>
            </div>
            <pre><code>{
  "event": "user.login",
  "timestamp": "2026-03-10T12:00:00Z",
  "data": {
    "user_id": 42,
    "email": "john@example.com",
    "ip_address": "192.168.1.10",
    "user_agent": "Mozilla/5.0 ...",
    "client_system": "customer-portal",
    "2fa_used": true
  }
}</code></pre>
        </div>
    </section>

    {{-- Signature Verification --}}
    <section class="mb-12">
        <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Signature verification</h2>
        <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">Every webhook request includes an <code class="os-code-inline">X-One-System-Signature</code> header. Verify it against your signing secret before processing the payload.</p>
        <div class="os-codeblock">
            <div class="os-codeblock-head">
                <span><i class="fab fa-php mr-1.5"></i>PHP verification example</span>
            </div>
            <pre><code>$payload   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_ONE_SYSTEM_SIGNATURE'];
$expected  = hash_hmac('sha256', $payload, $webhookSecret);

if (hash_equals($expected, $signature)) {
    // Safe to process
    $event = json_decode($payload, true);
}</code></pre>
        </div>
        <div class="os-alert mt-4">
            <i class="fas fa-circle-info mt-0.5 text-[var(--color-accent)]"></i>
            <span>Store the signing secret in your client's environment (for example <code class="os-code-inline">ONE_SYSTEM_WEBHOOK_SECRET</code>) — never hard-code it in source. One System holds only a hashed copy server-side.</span>
        </div>
    </section>

    {{-- Configuration --}}
    <section class="border-t border-[var(--color-line)] pt-10">
        <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Registering a webhook</h2>
        <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-6">Register webhook endpoints from the admin panel under <strong>Settings &rarr; Webhooks</strong>, or via the API. The API call is service-to-service, so it must come from a whitelisted IP and authenticate with your client credentials:</p>
        <div class="os-codeblock mb-6">
            <div class="os-codeblock-head">
                <span><span class="os-badge os-badge-accent mr-2">POST</span>/api/webhooks</span>
            </div>
            <pre><code>{
  "url": "https://your-app.com/webhooks/one-system",
  "events": ["user.login", "user.logout", "user.locked"],
  "secret": "whsec_your_signing_secret"
}</code></pre>
        </div>
        <div class="os-alert os-alert-warning">
            <i class="fas fa-triangle-exclamation mt-0.5"></i>
            <div>
                <strong>Security</strong> — always verify the <code class="os-code-inline">X-One-System-Signature</code> header before processing events, serve your webhook endpoint over HTTPS, and never trust unverified payloads.
            </div>
        </div>
    </section>

    {{-- Navigation --}}
    <div class="flex justify-between items-center border-t border-[var(--color-line)] pt-6 mt-12">
        <a href="{{ route('docs.api.overview') }}" class="os-btn os-btn-ghost">
            <i class="fas fa-arrow-left"></i>API reference
        </a>
        <a href="{{ route('docs.security-guide') }}" class="os-btn os-btn-secondary">
            Security guide<i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endsection

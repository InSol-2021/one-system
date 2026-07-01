@extends('public.documentation.layout')

@section('title', 'Changelog — One System SSO')
@section('description', 'Version history and release notes for the One System single sign-on platform.')

@section('content')
<div class="os-container !px-0 !max-w-none">

{{-- Hero --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <p class="os-eyebrow mb-3">Reference</p>
    <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Changelog</h1>
    <p class="text-lg text-[var(--color-muted)] leading-relaxed">All notable changes to the One System platform, newest first.</p>
</section>

{{-- v2.1.0 --}}
<section class="mb-12">
    <div class="flex items-start gap-4 mb-6">
        <span class="os-badge os-badge-accent flex-shrink-0">v2.1.0</span>
        <div>
            <h2 class="text-xl font-semibold text-[var(--color-ink)]">Security hardening</h2>
            <p class="text-sm text-[var(--color-faint)] mt-1">March 2026</p>
        </div>
    </div>
    <div class="md:ml-16 space-y-6">
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Added</h3>
            <ul class="space-y-1.5 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>reCAPTCHA v3 on login endpoints, configured via <code class="os-code-inline">RECAPTCHAV3_SITEKEY</code> / <code class="os-code-inline">RECAPTCHAV3_SECRET</code>.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Account lockout — five failed attempts triggers a 30-minute cooldown.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i><code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> environment variable to control cross-origin access.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>SDK download page with versioned package management.</li>
            </ul>
        </div>
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Improved</h3>
            <ul class="space-y-1.5 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2"><i class="fas fa-arrow-up text-[var(--color-ink-2)] text-xs mt-1"></i>Authentication is now required on all admin and user management routes — they are no longer publicly reachable.</li>
                <li class="flex items-start gap-2"><i class="fas fa-arrow-up text-[var(--color-ink-2)] text-xs mt-1"></i>Client secrets are stored hashed and shown in full only once, at creation or regeneration.</li>
                <li class="flex items-start gap-2"><i class="fas fa-arrow-up text-[var(--color-ink-2)] text-xs mt-1"></i>Per-endpoint rate-limit granularity for authentication, validation, and management.</li>
                <li class="flex items-start gap-2"><i class="fas fa-arrow-up text-[var(--color-ink-2)] text-xs mt-1"></i>Documentation site rebuilt on the One System design system.</li>
            </ul>
        </div>
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Upgrade notes</h3>
            <div class="os-alert os-alert-warning">
                <i class="fas fa-triangle-exclamation mt-0.5"></i>
                <div>Run the new migrations (<code class="os-code-inline">php artisan migrate</code>), set <code class="os-code-inline">JWT_SECRET</code>, <code class="os-code-inline">RECAPTCHAV3_*</code>, and <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> in your environment, and serve over HTTPS in production. Existing client secrets must be regenerated, since plaintext secrets can no longer be read back.</div>
            </div>
        </div>
    </div>
</section>

{{-- v2.0.0 --}}
<section class="mb-12">
    <div class="flex items-start gap-4 mb-6">
        <span class="os-badge flex-shrink-0">v2.0.0</span>
        <div>
            <h2 class="text-xl font-semibold text-[var(--color-ink)]">Enterprise release</h2>
            <p class="text-sm text-[var(--color-faint)] mt-1">January 2026</p>
        </div>
    </div>
    <div class="md:ml-16 space-y-6">
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Added</h3>
            <ul class="space-y-1.5 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Multi-platform SSO with Laravel, .NET, Node.js, Java, Python, and JavaScript integrations.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Admin dashboard with real-time user monitoring and audit logs.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>User self-service portal for profile management and 2FA setup.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Client application registration with IP whitelisting for service-to-service token issuance.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>PostgreSQL multi-schema database architecture.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Docker-based deployment with Kubernetes support.</li>
            </ul>
        </div>
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Breaking changes</h3>
            <ul class="space-y-1.5 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2"><i class="fas fa-circle-exclamation text-[var(--color-danger)] text-xs mt-1"></i>Service-to-service token issuance moved to <code class="os-code-inline">POST /api/sso/token</code> and is now IP-whitelisted.</li>
                <li class="flex items-start gap-2"><i class="fas fa-circle-exclamation text-[var(--color-danger)] text-xs mt-1"></i>Token validation requires both <code class="os-code-inline">client_id</code> and <code class="os-code-inline">client_secret</code> in the request body of <code class="os-code-inline">POST /api/validate-token</code>.</li>
                <li class="flex items-start gap-2"><i class="fas fa-circle-exclamation text-[var(--color-danger)] text-xs mt-1"></i>Browser logins now flow through <code class="os-code-inline">GET /sso/login?client_id=...</code> and redirect to the registered <code class="os-code-inline">callback_url</code> with a single-use token.</li>
            </ul>
        </div>
    </div>
</section>

{{-- v1.5.0 --}}
<section class="mb-12">
    <div class="flex items-start gap-4 mb-6">
        <span class="os-badge flex-shrink-0">v1.5.0</span>
        <div>
            <h2 class="text-xl font-semibold text-[var(--color-ink)]">Two-factor authentication</h2>
            <p class="text-sm text-[var(--color-faint)] mt-1">October 2025</p>
        </div>
    </div>
    <div class="md:ml-16 space-y-6">
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Added</h3>
            <ul class="space-y-1.5 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>TOTP-based two-factor authentication with QR-code setup.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Backup recovery codes for 2FA.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Session management with device-level tracking.</li>
            </ul>
        </div>
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Improved</h3>
            <ul class="space-y-1.5 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2"><i class="fas fa-arrow-up text-[var(--color-ink-2)] text-xs mt-1"></i>Password hashing upgraded to bcrypt with 12 rounds.</li>
                <li class="flex items-start gap-2"><i class="fas fa-arrow-up text-[var(--color-ink-2)] text-xs mt-1"></i>Login audit log now captures user agent and geo-IP data.</li>
            </ul>
        </div>
    </div>
</section>

{{-- v1.0.0 --}}
<section class="mb-12">
    <div class="flex items-start gap-4 mb-6">
        <span class="os-badge flex-shrink-0">v1.0.0</span>
        <div>
            <h2 class="text-xl font-semibold text-[var(--color-ink)]">Initial release</h2>
            <p class="text-sm text-[var(--color-faint)] mt-1">August 2025</p>
        </div>
    </div>
    <div class="md:ml-16 space-y-6">
        <div>
            <h3 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-2">Added</h3>
            <ul class="space-y-1.5 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Core SSO authentication via HS256 JWT tokens.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>User registration and login.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Laravel client package.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Basic admin panel for user management.</li>
                <li class="flex items-start gap-2"><i class="fas fa-plus text-[var(--color-accent)] text-xs mt-1"></i>Token issuance and validation endpoints.</li>
            </ul>
        </div>
    </div>
</section>

</div>
@endsection

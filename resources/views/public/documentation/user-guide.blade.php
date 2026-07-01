@extends('public.documentation.layout')

@section('title', 'User guide — One System')
@section('description', 'Learn how to use One System single sign-on as an end user — sign in, profile, connected apps, and security settings.')

@section('content')
<div class="">
    {{-- Page header --}}
    <section class="border-b border-[var(--color-line)] pb-10 mb-12">
        <div class="flex items-center gap-2 text-sm text-[var(--color-muted)] mb-4">
            <a href="{{ route('docs') }}" class="hover:text-[var(--color-accent)]">Docs</a>
            <i class="fas fa-chevron-right text-[0.65rem] text-[var(--color-faint)]"></i>
            <span>User guide</span>
        </div>
        <p class="os-eyebrow mb-3">For end users</p>
        <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">User guide</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">A complete guide for end users of One System single sign-on.</p>
    </section>

    {{-- Getting Started --}}
    <section class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2">
            <span class="os-icon-tile os-icon-tile-ink" style="width:2.25rem;height:2.25rem;font-size:0.95rem;"><i class="fas fa-circle-play"></i></span>
            Getting started
        </h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">
            One System gives you a single set of credentials to access every connected application. Once an administrator creates your account, you can sign in and manage your profile.
        </p>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3">Signing in</h3>
        <ol class="space-y-2 text-sm text-[var(--color-ink-2)] mb-4">
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">1.</span> Open the One System sign-in page at <code class="os-code-inline">/auth/login</code></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">2.</span> Enter your <strong>email address</strong> and <strong>password</strong></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">3.</span> If two-factor authentication (2FA) is enabled, enter the code from your authenticator app</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">4.</span> You are redirected to your <strong>user dashboard</strong></li>
        </ol>

        <div class="os-alert">
            <i class="fas fa-circle-info mt-0.5 text-[var(--color-accent)]"></i>
            <span><strong>Tip:</strong> If you forget your password, use the "Forgot password" link on the sign-in page, or contact your system administrator to have it reset.</span>
        </div>
    </section>

    {{-- User Dashboard --}}
    <section class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2">
            <span class="os-icon-tile os-icon-tile-ink" style="width:2.25rem;height:2.25rem;font-size:0.95rem;"><i class="fas fa-gauge-high"></i></span>
            User dashboard
        </h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">
            After signing in, the dashboard gives you an overview of your connected applications and account status.
        </p>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3">Dashboard features</h3>
        <ul class="space-y-3 text-sm text-[var(--color-ink-2)]">
            <li class="flex items-start gap-3">
                <i class="fas fa-circle-check text-[var(--color-accent)] mt-1"></i>
                <div><strong>Connected applications</strong> — view every client system you have access to, with its SSO status.</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-circle-check text-[var(--color-accent)] mt-1"></i>
                <div><strong>Launch applications</strong> — click "Launch application" on any connected system to sign in to it without re-entering credentials.</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-circle-check text-[var(--color-accent)] mt-1"></i>
                <div><strong>Quick actions</strong> — reach profile settings and refresh your dashboard data.</div>
            </li>
        </ul>
    </section>

    {{-- Profile & Security --}}
    <section class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2">
            <span class="os-icon-tile os-icon-tile-ink" style="width:2.25rem;height:2.25rem;font-size:0.95rem;"><i class="fas fa-user-gear"></i></span>
            Profile &amp; security
        </h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">
            Manage your personal information and security settings from the profile page.
        </p>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3">Personal information</h3>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">Update your name, contact details, and other account information. Changes are saved immediately and reflected across all connected applications.</p>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3">Changing your password</h3>
        <ol class="space-y-2 text-sm text-[var(--color-ink-2)] mb-4">
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">1.</span> Open <strong>Profile &amp; security</strong> from the navigation bar</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">2.</span> Click the <strong>Security</strong> tab</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">3.</span> Enter your <strong>current password</strong></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">4.</span> Enter and confirm your <strong>new password</strong></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">5.</span> Click <strong>Update password</strong></li>
        </ol>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3">Two-factor authentication (2FA)</h3>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">When 2FA is enabled, you need an authenticator app (Google Authenticator, Authy, and similar) to generate time-based one-time passwords (TOTP) during sign-in.</p>

        <div class="os-alert os-alert-warning">
            <i class="fas fa-triangle-exclamation mt-0.5"></i>
            <span><strong>Important:</strong> Keep your 2FA recovery codes somewhere safe. If you lose access to your authenticator app, you will need to contact your administrator.</span>
        </div>
    </section>

    {{-- SSO Login Flow --}}
    <section class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2">
            <span class="os-icon-tile os-icon-tile-ink" style="width:2.25rem;height:2.25rem;font-size:0.95rem;"><i class="fas fa-right-left"></i></span>
            How SSO sign-in works
        </h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">
            Single sign-on lets you reach many applications after one sign-in. Behind the scenes:
        </p>
        <ol class="space-y-3 text-sm text-[var(--color-ink-2)] mb-6">
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">1.</span> <strong>Sign in once</strong> — authenticate with One System using your credentials.</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">2.</span> <strong>Click "Launch application"</strong> — your browser is sent to <code class="os-code-inline">/sso/login?client_id=&hellip;</code> for the app you chose.</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">3.</span> <strong>Token hand-off</strong> — One System redirects your browser to the app's registered callback URL with a single-use token appended (<code class="os-code-inline">?token=&hellip;</code>).</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">4.</span> <strong>Server-side validation</strong> — the app validates that token with One System server-to-server, then creates its own session and logs you in.</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">5.</span> <strong>Seamless access</strong> — for the rest of your session you can launch other connected apps without re-entering credentials.</li>
        </ol>
        <div class="os-alert">
            <i class="fas fa-circle-info mt-0.5 text-[var(--color-accent)]"></i>
            <span>The hand-off token is single-use and short-lived. The application validates it once, then relies on its own session — so the token never needs to be reused or stored.</span>
        </div>
    </section>

    {{-- Troubleshooting --}}
    <section class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2">
            <span class="os-icon-tile os-icon-tile-ink" style="width:2.25rem;height:2.25rem;font-size:0.95rem;"><i class="fas fa-circle-question"></i></span>
            Common issues
        </h2>

        <div class="space-y-4">
            <div class="border-l-2 border-[var(--color-accent)] pl-4">
                <h4 class="text-sm font-semibold text-[var(--color-ink)]">Can't sign in?</h4>
                <p class="text-sm text-[var(--color-muted)]">Double-check your email and password. If you've forgotten your password, use the "Forgot password" link or contact your administrator to reset it.</p>
            </div>
            <div class="border-l-2 border-[var(--color-accent)] pl-4">
                <h4 class="text-sm font-semibold text-[var(--color-ink)]">2FA code not working?</h4>
                <p class="text-sm text-[var(--color-muted)]">Make sure the time on your phone is synced correctly. TOTP codes are time-sensitive and expire every 30 seconds.</p>
            </div>
            <div class="border-l-2 border-[var(--color-accent)] pl-4">
                <h4 class="text-sm font-semibold text-[var(--color-ink)]">Application not launching?</h4>
                <p class="text-sm text-[var(--color-muted)]">The client system may be offline, or your access may not be configured. Contact your administrator.</p>
            </div>
            <div class="border-l-2 border-[var(--color-accent)] pl-4">
                <h4 class="text-sm font-semibold text-[var(--color-ink)]">Need help?</h4>
                <p class="text-sm text-[var(--color-muted)]">Reach out to your system administrator or contact the One System development team at <a href="https://innovativesolution.com.np/" target="_blank" rel="noopener" class="font-semibold text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">innovativesolution.com.np</a>.</p>
            </div>
        </div>
    </section>

    {{-- Navigation --}}
    <div class="flex justify-between items-center border-t border-[var(--color-line)] pt-6">
        <a href="{{ route('docs.admin-panel') }}" class="os-btn os-btn-ghost">
            <i class="fas fa-arrow-left"></i>Admin guide
        </a>
        <a href="{{ route('docs.security-guide') }}" class="os-btn os-btn-secondary">
            Security guide<i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endsection

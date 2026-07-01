@extends('public.documentation.layout')

@section('title', 'Two-factor authentication setup — One System')
@section('description', 'How to enable and use TOTP-based two-factor authentication in One System.')

@section('content')
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <p class="os-eyebrow mb-3">How to use</p>
        <h1 class="text-4xl font-bold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Two-factor authentication</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">Enable TOTP-based 2FA to add an extra layer of security to your One System accounts.</p>
    </div>
</section>

<nav class="os-card os-card-pad mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">On this page</h2>
    <ol class="space-y-1.5 text-sm">
        <li><a href="#overview" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. How 2FA works</a></li>
        <li><a href="#enable" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Enabling 2FA</a></li>
        <li><a href="#login" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. Logging in with 2FA</a></li>
        <li><a href="#recovery" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. Recovery codes</a></li>
        <li><a href="#api" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. API integration</a></li>
    </ol>
</nav>

<section id="overview" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">1. How 2FA works</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">One System uses time-based one-time passwords (TOTP) for two-factor authentication. After entering your password, you provide a 6-digit code from your authenticator app.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
        <div class="os-card p-5 text-center">
            <div class="os-icon-tile mx-auto mb-3"><i class="fas fa-lock"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Password</h3>
            <p class="text-xs text-[var(--color-muted)]">Something you know</p>
        </div>
        <div class="p-5 text-center">
            <i class="fas fa-plus text-[var(--color-faint)] text-xl mb-2"></i>
            <p class="text-xs text-[var(--color-faint)] font-semibold uppercase tracking-widest">Combined with</p>
        </div>
        <div class="os-card p-5 text-center">
            <div class="os-icon-tile mx-auto mb-3"><i class="fas fa-mobile-screen"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">TOTP code</h3>
            <p class="text-xs text-[var(--color-muted)]">Something you have</p>
        </div>
    </div>
    <p class="text-xs text-[var(--color-muted)] mt-4"><strong>Compatible apps:</strong> Google Authenticator, Authy, Microsoft Authenticator, 1Password.</p>
</section>

<section id="enable" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">2. Enabling 2FA</h2>
    <div class="space-y-0 mb-6">
        <div class="flex gap-4">
            <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold">1</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
            <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Open security settings</h3><p class="text-xs text-[var(--color-muted)]">Go to <strong>User portal &rarr; Profile &rarr; Security</strong>, or <strong>Admin &rarr; Users &rarr; Edit &rarr; 2FA</strong>.</p></div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold">2</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
            <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Scan the QR code</h3><p class="text-xs text-[var(--color-muted)]">Open your authenticator app and scan the displayed QR code. If you can't scan, use the manual secret key.</p></div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold">3</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
            <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Enter the verification code</h3><p class="text-xs text-[var(--color-muted)]">Enter the 6-digit code from your authenticator app to confirm setup.</p></div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-accent)] text-white rounded-full flex items-center justify-center text-xs font-semibold">4</div></div>
            <div class="pb-4"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Save recovery codes</h3><p class="text-xs text-[var(--color-muted)]">Download or copy the backup recovery codes. Store them securely &mdash; they are your fallback if you lose your device.</p></div>
        </div>
    </div>
    <div class="os-alert os-alert-warning">
        <i class="fas fa-triangle-exclamation mt-0.5"></i>
        <span><strong>Important:</strong> recovery codes are shown only once during setup. Store them in a secure location such as a password manager.</span>
    </div>
</section>

<section id="login" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">3. Logging in with 2FA</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">When 2FA is enabled, the login flow adds an extra step:</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="os-card p-4">
            <div class="os-eyebrow mb-2">Step 1</div>
            <p class="text-sm font-semibold text-[var(--color-ink)] mb-1">Enter credentials</p>
            <p class="text-xs text-[var(--color-muted)]">Email and password as usual.</p>
        </div>
        <div class="os-card p-4 border-[var(--color-accent-line)] bg-[var(--color-accent-soft)]">
            <div class="os-eyebrow mb-2">Step 2</div>
            <p class="text-sm font-semibold text-[var(--color-ink)] mb-1">2FA challenge</p>
            <p class="text-xs text-[var(--color-muted)]">Enter the 6-digit TOTP code.</p>
        </div>
        <div class="os-card p-4">
            <div class="os-eyebrow mb-2">Step 3</div>
            <p class="text-sm font-semibold text-[var(--color-ink)] mb-1">Access granted</p>
            <p class="text-xs text-[var(--color-muted)]">A full JWT token is issued.</p>
        </div>
    </div>
</section>

<section id="recovery" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">4. Recovery codes</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Each recovery code is single-use. If you have lost your authenticator device:</p>
    <ol class="space-y-2 text-sm text-[var(--color-ink-2)] mb-4">
        <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-accent)]">1.</span> On the 2FA challenge screen, select &ldquo;Use recovery code&rdquo;.</li>
        <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-accent)]">2.</span> Enter one of your saved recovery codes.</li>
        <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-accent)]">3.</span> After signing in, immediately set up 2FA again with a new device.</li>
    </ol>
    <p class="text-sm text-[var(--color-muted)]">If you have lost both your device and your recovery codes, contact an admin to disable 2FA on your account.</p>
</section>

<section id="api" class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">5. API integration</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">When a 2FA-enabled user authenticates via API, the flow uses a temporary token:</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span>Step 1 — initial login returns the 2FA requirement</span>
            <span>json</span>
        </div>
        <pre><code><span class="text-[var(--color-faint)]">// Response from POST {CAS_BASE}/api/sso/token</span>
{
  "requires_2fa": true,
  "temp_token": "temp_eyJhbGci..."
}</code></pre>
    </div>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> {CAS_BASE}/api/sso/verify-2fa</span>
            <span>json</span>
        </div>
        <pre><code>{
  "temp_token": "temp_eyJhbGci...",
  "totp_code": "123456"
}</code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>Step 3 — full token received</span>
            <span>json</span>
        </div>
        <pre><code>{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIs..."
}</code></pre>
    </div>
</section>
@endsection

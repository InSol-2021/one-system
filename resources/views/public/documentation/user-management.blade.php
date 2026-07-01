@extends('public.documentation.layout')

@section('title', 'User management — One System')
@section('description', 'How to create, manage, and configure user accounts in One System.')

@section('content')
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <p class="os-eyebrow mb-3">How to use</p>
        <h1 class="text-4xl font-bold text-[var(--color-ink)] tracking-tight leading-tight mb-4">User management</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">Create, manage, and configure user accounts, roles, and permissions.</p>
    </div>
</section>

<nav class="os-card os-card-pad mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">On this page</h2>
    <ol class="space-y-1.5 text-sm">
        <li><a href="#roles" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. User roles</a></li>
        <li><a href="#create" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Creating users</a></li>
        <li><a href="#api-create" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. Creating users via API</a></li>
        <li><a href="#profile" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. User self-service portal</a></li>
        <li><a href="#lockout" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. Account lockout &amp; recovery</a></li>
    </ol>
</nav>

<section id="roles" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">1. User roles</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">One System supports two user roles with different access levels:</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="os-card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="os-icon-tile"><i class="fas fa-user-shield"></i></div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">Admin</h3>
            </div>
            <ul class="space-y-1.5 text-xs text-[var(--color-ink-2)]">
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Access the admin dashboard</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Manage client systems</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Create, edit, and delete users</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>View audit logs</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Manage IP whitelists</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Regenerate client credentials</li>
            </ul>
        </div>
        <div class="os-card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-user"></i></div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">User</h3>
            </div>
            <ul class="space-y-1.5 text-xs text-[var(--color-ink-2)]">
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Access the user portal</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>View their own profile</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Update personal information</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Enable or disable 2FA</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Change their password</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-[var(--color-accent)]"></i>Single sign-on into client apps</li>
            </ul>
        </div>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info mt-0.5"></i>
        <span>Admin and user routes now require authentication. Unauthenticated requests are redirected to sign in.</span>
    </div>
</section>

<section id="create" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">2. Creating users (admin panel)</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Go to <strong>Admin &rarr; Users &rarr; Add user</strong>.</p>
    <div class="os-card overflow-hidden mb-4">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Field</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Required</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Validation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">Name</td><td class="px-5 py-3"><i class="fas fa-check text-[var(--color-accent)]"></i></td><td class="px-5 py-3 text-[var(--color-muted)]">2&ndash;255 characters</td></tr>
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">Email</td><td class="px-5 py-3"><i class="fas fa-check text-[var(--color-accent)]"></i></td><td class="px-5 py-3 text-[var(--color-muted)]">Valid email, unique</td></tr>
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">Password</td><td class="px-5 py-3"><i class="fas fa-check text-[var(--color-accent)]"></i></td><td class="px-5 py-3 text-[var(--color-muted)]">Minimum 8 characters</td></tr>
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">Role</td><td class="px-5 py-3"><i class="fas fa-check text-[var(--color-accent)]"></i></td><td class="px-5 py-3 text-[var(--color-muted)]">admin or user</td></tr>
            </tbody>
        </table>
    </div>
</section>

<section id="api-create" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">3. Creating users via API</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Register users programmatically through the REST API:</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> {CAS_BASE}/api/register</span>
            <span>bash</span>
        </div>
        <pre><code>curl -X POST https://one-system.company.com/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secure_password_123",
    "password_confirmation": "secure_password_123"
  }'</code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>200 OK — success response</span>
            <span>json</span>
        </div>
        <pre><code>{
  "success": true,
  "user": {
    "id": 42,
    "name": "John Doe",
    "username": "jdoe",
    "email": "john@example.com",
    "role": "user"
  }
}</code></pre>
    </div>
</section>

<section id="profile" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">4. User self-service portal</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Regular users can manage their own accounts at <code class="os-code-inline">/user/dashboard</code>:</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="os-card p-4">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1 flex items-center gap-2"><i class="fas fa-id-card text-[var(--color-muted)]"></i>Profile</h3>
            <p class="text-xs text-[var(--color-muted)]">Update name, email, and profile picture.</p>
        </div>
        <div class="os-card p-4">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1 flex items-center gap-2"><i class="fas fa-lock text-[var(--color-muted)]"></i>Password</h3>
            <p class="text-xs text-[var(--color-muted)]">Change password with current-password confirmation.</p>
        </div>
        <div class="os-card p-4">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1 flex items-center gap-2"><i class="fas fa-shield-halved text-[var(--color-muted)]"></i>Two-factor auth</h3>
            <p class="text-xs text-[var(--color-muted)]">Enable or disable TOTP-based 2FA with QR-code setup.</p>
        </div>
        <div class="os-card p-4">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1 flex items-center gap-2"><i class="fas fa-clock-rotate-left text-[var(--color-muted)]"></i>Login history</h3>
            <p class="text-xs text-[var(--color-muted)]">Review recent login activity, IP addresses, and devices.</p>
        </div>
    </div>
</section>

<section id="lockout" class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">5. Account lockout &amp; recovery</h2>
    <div class="space-y-3">
        <div class="os-card flex items-start gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-ban"></i></div>
            <div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">Auto-lock</h3>
                <p class="text-xs text-[var(--color-muted)]">After 5 failed login attempts, the account is locked for 30 minutes and the user sees the time remaining.</p>
            </div>
        </div>
        <div class="os-card flex items-start gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-unlock"></i></div>
            <div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">Admin unlock</h3>
                <p class="text-xs text-[var(--color-muted)]">Admins can manually unlock accounts from <strong>Admin &rarr; Users &rarr; Unlock</strong>.</p>
            </div>
        </div>
        <div class="os-card flex items-start gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-envelope"></i></div>
            <div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">Password reset</h3>
                <p class="text-xs text-[var(--color-muted)]">Users can reset their password by email at <a href="{{ route('password.request') }}" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">the forgot-password page</a>.</p>
            </div>
        </div>
    </div>
</section>
@endsection

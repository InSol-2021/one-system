@extends('public.documentation.layout')

@section('title', 'Admin guide — One System')
@section('description', 'How to use the One System admin dashboard to manage users, client systems, and monitor authentication activity.')

@section('content')
<div class="">

    {{-- Page header --}}
    <section class="border-b border-[var(--color-line)] pb-10 mb-12">
        <div class="flex items-center gap-2 text-sm text-[var(--color-muted)] mb-4">
            <a href="{{ route('docs') }}" class="hover:text-[var(--color-accent)]">Docs</a>
            <i class="fas fa-chevron-right text-[0.65rem] text-[var(--color-faint)]"></i>
            <span>Admin guide</span>
        </div>
        <p class="os-eyebrow mb-3">How to use</p>
        <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Admin guide</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">A complete walkthrough of the One System admin dashboard — manage users, client systems, and monitor activity from one place.</p>
    </section>

    {{-- On this page --}}
    <nav class="os-card os-card-pad mb-12 bg-[var(--color-surface-2)]">
        <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">On this page</h2>
        <ol class="space-y-1.5 text-sm">
            <li><a href="#access" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. Accessing the admin panel</a></li>
            <li><a href="#dashboard" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Dashboard overview</a></li>
            <li><a href="#clients" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. Managing client systems</a></li>
            <li><a href="#users" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. User management</a></li>
            <li><a href="#audit" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. Audit logs</a></li>
            <li><a href="#ip" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">6. IP whitelist</a></li>
            <li><a href="#setup" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">7. Initial setup checklist</a></li>
            <li><a href="#monitoring" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">8. Routine monitoring</a></li>
            <li><a href="#incidents" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">9. Warning signs &amp; incident response</a></li>
        </ol>
    </nav>

    {{-- Access --}}
    <section id="access" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">1. Accessing the admin panel</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">Sign in with an admin account at:</p>
        <div class="os-codeblock mb-4">
            <div class="os-codeblock-head">
                <span>Admin sign-in URL</span>
            </div>
            <pre><code>https://your-one-system.com/auth/login</code></pre>
        </div>
        <p class="text-sm text-[var(--color-ink-2)]">After sign-in, admin users are redirected to <code class="os-code-inline">/admin/dashboard</code>. Regular users go to the user portal instead.</p>
        <div class="os-alert mt-4">
            <i class="fas fa-circle-info mt-0.5 text-[var(--color-accent)]"></i>
            <span>Only users with the <code class="os-code-inline">admin</code> role can reach the admin panel. The role-based redirect happens automatically in the <code class="os-code-inline">AuthController</code>. As of the latest release, every admin and user route now requires authentication — there are no unauthenticated admin pages.</span>
        </div>
    </section>

    {{-- Dashboard --}}
    <section id="dashboard" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">2. Dashboard overview</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">The admin dashboard shows key metrics at a glance:</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="os-card os-card-pad text-center">
                <span class="os-icon-tile os-icon-tile-ink mx-auto mb-3"><i class="fas fa-users"></i></span>
                <p class="text-sm font-semibold text-[var(--color-ink)]">Total users</p>
                <p class="text-xs text-[var(--color-muted)]">Active accounts</p>
            </div>
            <div class="os-card os-card-pad text-center">
                <span class="os-icon-tile os-icon-tile-ink mx-auto mb-3"><i class="fas fa-server"></i></span>
                <p class="text-sm font-semibold text-[var(--color-ink)]">Client systems</p>
                <p class="text-xs text-[var(--color-muted)]">Registered apps</p>
            </div>
            <div class="os-card os-card-pad text-center">
                <span class="os-icon-tile os-icon-tile-ink mx-auto mb-3"><i class="fas fa-key"></i></span>
                <p class="text-sm font-semibold text-[var(--color-ink)]">Active tokens</p>
                <p class="text-xs text-[var(--color-muted)]">Current sessions</p>
            </div>
            <div class="os-card os-card-pad text-center">
                <span class="os-icon-tile os-icon-tile-ink mx-auto mb-3"><i class="fas fa-clipboard-list"></i></span>
                <p class="text-sm font-semibold text-[var(--color-ink)]">Audit events</p>
                <p class="text-xs text-[var(--color-muted)]">Login activity</p>
            </div>
        </div>
        <p class="text-sm text-[var(--color-ink-2)]">Use the sidebar navigation to reach each admin module. Every section provides full create, read, update, and delete operations.</p>
    </section>

    {{-- Client Systems --}}
    <section id="clients" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">3. Managing client systems</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">Client systems represent the applications that use One System for authentication. Navigate to <strong>Admin &rarr; Client systems</strong>.</p>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3">Adding a new client</h3>
        <ol class="space-y-2 text-sm text-[var(--color-ink-2)] mb-4">
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">1.</span> Click "Add new client system"</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">2.</span> Fill in the application name and base URL</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">3.</span> Set the registered callback URL for SSO redirects</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">4.</span> Save — credentials are generated automatically</li>
        </ol>
        <div class="os-card overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Credential</th>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Purpose</th>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Visibility</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-line)] text-[var(--color-ink-2)]">
                    <tr><td class="px-5 py-3"><code class="os-code-inline">client_id</code></td><td class="px-5 py-3">Unique app identifier</td><td class="px-5 py-3">Always visible</td></tr>
                    <tr><td class="px-5 py-3"><code class="os-code-inline">client_secret</code></td><td class="px-5 py-3">Signs and validates SSO tokens server-to-server</td><td class="px-5 py-3 text-[var(--color-warning)] font-medium">Hashed at rest, shown once</td></tr>
                    <tr><td class="px-5 py-3"><code class="os-code-inline">client_username</code></td><td class="px-5 py-3">Service-to-service API authentication</td><td class="px-5 py-3">Always visible</td></tr>
                    <tr><td class="px-5 py-3"><code class="os-code-inline">client_password</code></td><td class="px-5 py-3">Service-to-service API authentication</td><td class="px-5 py-3 text-[var(--color-warning)] font-medium">Hashed at rest, shown once</td></tr>
                </tbody>
            </table>
        </div>
        <div class="os-alert mb-4">
            <i class="fas fa-circle-info mt-0.5 text-[var(--color-accent)]"></i>
            <span>Client secrets are stored <strong>hashed</strong> and displayed only once, at creation or regeneration. Copy them immediately into the client application's environment — One System cannot show them again.</span>
        </div>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3">Regenerating credentials</h3>
        <p class="text-sm text-[var(--color-ink-2)]">If credentials are compromised, use the <strong>Regenerate credentials</strong> action. This invalidates all existing tokens for that client and issues a fresh secret. The action is recorded in the audit trail.</p>
        <div class="os-alert os-alert-danger mt-3">
            <i class="fas fa-triangle-exclamation mt-0.5"></i>
            <span><strong>Warning:</strong> Regenerating credentials breaks all active sessions for that client application. Update the client's <code class="os-code-inline">.env</code> (for example <code class="os-code-inline">ONE_SYSTEM_CLIENT_SECRET</code>) immediately.</span>
        </div>
    </section>

    {{-- Users --}}
    <section id="users" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">4. User management</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">Navigate to <strong>Admin &rarr; Users</strong> to manage every account.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-2 flex items-center gap-2"><i class="fas fa-user-plus text-[var(--color-accent)]"></i>Create users</h3>
                <p class="text-xs text-[var(--color-muted)]">Add users directly from the admin panel. Set name, email, password, and role assignment.</p>
            </div>
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-2 flex items-center gap-2"><i class="fas fa-user-pen text-[var(--color-accent)]"></i>Edit profiles</h3>
                <p class="text-xs text-[var(--color-muted)]">Update user information, reset passwords, enable or disable 2FA, and change role assignments.</p>
            </div>
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-2 flex items-center gap-2"><i class="fas fa-user-lock text-[var(--color-accent)]"></i>Lock / unlock</h3>
                <p class="text-xs text-[var(--color-muted)]">Manually lock suspicious accounts, or unlock accounts that were auto-locked after 5 failed sign-in attempts.</p>
            </div>
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-2 flex items-center gap-2"><i class="fas fa-link text-[var(--color-accent)]"></i>Client links</h3>
                <p class="text-xs text-[var(--color-muted)]">See which client applications each user has authenticated with, and manage user-client associations.</p>
            </div>
        </div>
    </section>

    {{-- Audit --}}
    <section id="audit" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">5. Audit logs</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">Navigate to <strong>Admin &rarr; Audit logs</strong>. Every authentication event is recorded:</p>
        <div class="os-card overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Event</th>
                        <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Details captured</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-line)] text-[var(--color-ink-2)]">
                    <tr><td class="px-5 py-3"><code class="os-code-inline">login</code></td><td class="px-5 py-3">User email, IP, user agent, client system, timestamp</td></tr>
                    <tr><td class="px-5 py-3"><code class="os-code-inline">logout</code></td><td class="px-5 py-3">Session duration, client system</td></tr>
                    <tr><td class="px-5 py-3"><code class="os-code-inline">failed_login</code></td><td class="px-5 py-3">Attempted email, IP, failure reason</td></tr>
                    <tr><td class="px-5 py-3"><code class="os-code-inline">account_locked</code></td><td class="px-5 py-3">User ID, lock duration, failed attempt count</td></tr>
                    <tr><td class="px-5 py-3"><code class="os-code-inline">credentials_regenerated</code></td><td class="px-5 py-3">Admin user, client system, timestamp</td></tr>
                </tbody>
            </table>
        </div>
        <p class="text-sm text-[var(--color-ink-2)]">Use the <strong>filter</strong> and <strong>search</strong> tools to narrow results by date range, event type, user, or IP address. Logs can be exported for compliance reporting.</p>
    </section>

    {{-- IP Whitelist --}}
    <section id="ip" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">6. IP whitelist</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">Navigate to <strong>Admin &rarr; IP whitelist</strong>. When entries exist, only whitelisted IPs can make service-to-service API requests to One System — including SSO token issuance.</p>
        <ol class="space-y-2 text-sm text-[var(--color-ink-2)] mb-4">
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">1.</span> Click "Add IP address"</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">2.</span> Enter the IP address of your client server</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">3.</span> Assign it to a specific client system</li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">4.</span> Add a description for reference</li>
        </ol>
        <div class="os-alert">
            <i class="fas fa-lightbulb mt-0.5 text-[var(--color-accent)]"></i>
            <span><strong>Tip:</strong> For development, whitelist <code class="os-code-inline">127.0.0.1</code> and <code class="os-code-inline">::1</code> (IPv6 localhost). For production, use your server's public IP.</span>
        </div>
    </section>

    {{-- Initial Setup Checklist --}}
    <section id="setup" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">7. Initial setup checklist</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">After deploying One System for the first time, complete these one-time setup tasks:</p>

        <div class="space-y-3">
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">1</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Set environment secrets &amp; run migrations</h4>
                    <p class="text-xs text-[var(--color-muted)]">Configure <code class="os-code-inline">JWT_SECRET</code>, <code class="os-code-inline">RECAPTCHAV3_SITE_KEY</code> / <code class="os-code-inline">RECAPTCHAV3_SECRET_KEY</code>, and <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> in <code class="os-code-inline">.env</code>, then run the new migrations with <code class="os-code-inline">php artisan migrate</code>. Always serve over HTTPS in production.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">2</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Change the default admin password</h4>
                    <p class="text-xs text-[var(--color-muted)]">Change the initial admin password immediately after first sign-in. Go to <strong>Profile &rarr; Security &rarr; Change password</strong>.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">3</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Enable 2FA for admin accounts</h4>
                    <p class="text-xs text-[var(--color-muted)]">Enable two-factor authentication on all admin accounts. Go to <strong>Profile &rarr; Security &rarr; Enable 2FA</strong> and scan the QR code with Google Authenticator or Authy.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">4</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Register client systems</h4>
                    <p class="text-xs text-[var(--color-muted)]">Add every application that will use One System for authentication. Go to <strong>Admin &rarr; Client systems &rarr; Add new</strong>. Copy the generated credentials (shown once) and configure each client.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">5</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Whitelist production IPs</h4>
                    <p class="text-xs text-[var(--color-muted)]">Add the IP addresses of all production client servers to the IP whitelist. Go to <strong>Admin &rarr; IP whitelist &rarr; Add IP address</strong>.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">6</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Create user accounts &amp; verify the SSO flow</h4>
                    <p class="text-xs text-[var(--color-muted)]">Add accounts under <strong>Admin &rarr; Users &rarr; Add user</strong> with the right roles, then test the full flow: sign in to One System, click "Launch application" for each client, and confirm the SSO redirect and token validation succeed.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Routine Monitoring --}}
    <section id="monitoring" class="mb-12">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">8. Routine monitoring</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">To keep One System healthy and secure, perform these checks regularly:</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3 flex items-center gap-2"><i class="fas fa-calendar-day text-[var(--color-accent)]"></i>Daily checks</h3>
                <ul class="space-y-2 text-xs text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Review <strong>audit logs</strong> for failed sign-in spikes</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Check the <strong>dashboard</strong> for unusual active token counts</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Verify all <strong>client systems</strong> show as connected</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Check for <strong>locked accounts</strong> that may need unlocking</li>
                </ul>
            </div>
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3 flex items-center gap-2"><i class="fas fa-calendar-week text-[var(--color-accent)]"></i>Weekly checks</h3>
                <ul class="space-y-2 text-xs text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Review <strong>audit log trends</strong> against previous weeks</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Check for <strong>unknown IPs</strong> in sign-in attempts</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Verify <strong>server disk space</strong> and <strong>database size</strong></li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> Review <strong>user accounts</strong> for any that should be deactivated</li>
                </ul>
            </div>
        </div>

        <div class="os-card os-card-pad">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3 flex items-center gap-2"><i class="fas fa-calendar-alt text-[var(--color-accent)]"></i>Monthly maintenance</h3>
            <ul class="space-y-2 text-xs text-[var(--color-ink-2)]">
                <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Rotate client secrets</strong> for high-security applications</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Update One System</strong> — pull the latest code, run migrations, clear caches</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Review the IP whitelist</strong> — remove IPs that are no longer needed</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Test backup restore</strong> — verify database backups can be restored</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Check SSL certificates</strong> — ensure renewal is working</li>
            </ul>
        </div>
    </section>

    {{-- Warning Signs & Incident Response --}}
    <section id="incidents" class="border-t border-[var(--color-line)] pt-10">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">9. Warning signs &amp; incident response</h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">What to look for and how to respond to potential security issues.</p>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3 flex items-center gap-2"><i class="fas fa-triangle-exclamation text-[var(--color-warning)]"></i>Red flags to watch for</h3>
        <div class="space-y-3 mb-6">
            <div class="os-card os-card-pad flex items-start gap-3 border-[#fecaca] bg-[var(--color-danger-soft)]">
                <i class="fas fa-circle-exclamation text-[var(--color-danger)] mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Spike in failed sign-in attempts</h4>
                    <p class="text-xs text-[var(--color-ink-2)]">Many failed sign-ins from the same or different IPs may indicate a brute-force attack or credential stuffing.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3 border-[#fecaca] bg-[var(--color-danger-soft)]">
                <i class="fas fa-circle-exclamation text-[var(--color-danger)] mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Sign-in from unusual IPs or locations</h4>
                    <p class="text-xs text-[var(--color-ink-2)]">If audit logs show sign-ins from IPs not associated with your organization, the account may be compromised.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3 border-[#fecaca] bg-[var(--color-danger-soft)]">
                <i class="fas fa-circle-exclamation text-[var(--color-danger)] mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Abnormal token generation volume</h4>
                    <p class="text-xs text-[var(--color-ink-2)]">A sudden surge in SSO tokens may indicate automated abuse or compromised client credentials.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3 border-[#fecaca] bg-[var(--color-danger-soft)]">
                <i class="fas fa-circle-exclamation text-[var(--color-danger)] mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Unauthorized admin actions</h4>
                    <p class="text-xs text-[var(--color-ink-2)]">User creation, role changes, or credential regeneration by unknown admin accounts needs immediate investigation.</p>
                </div>
            </div>
        </div>

        <h3 class="text-sm font-semibold text-[var(--color-ink)] mt-6 mb-3 flex items-center gap-2"><i class="fas fa-shield-halved text-[var(--color-accent)]"></i>Incident response steps</h3>
        <p class="text-sm text-[var(--color-ink-2)] mb-4">If you suspect a breach, follow these steps immediately:</p>
        <div class="space-y-3">
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-danger)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">1</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Lock compromised accounts</h4>
                    <p class="text-xs text-[var(--color-muted)]">Go to <strong>Admin &rarr; Users</strong> and lock any accounts that may be compromised.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-danger)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">2</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Regenerate compromised credentials</h4>
                    <p class="text-xs text-[var(--color-muted)]">Go to <strong>Admin &rarr; Client systems</strong> and regenerate credentials for affected clients. Update each client's <code class="os-code-inline">.env</code> immediately.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-danger)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">3</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Review audit logs</h4>
                    <p class="text-xs text-[var(--color-muted)]">In <strong>Admin &rarr; Audit logs</strong>, filter by the affected time period and user to identify the scope of unauthorized access.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-danger)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">4</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Restrict IP access</h4>
                    <p class="text-xs text-[var(--color-muted)]">Update the <strong>IP whitelist</strong> to block suspicious IPs and remove any unauthorized entries.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-danger)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">5</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Force password resets</h4>
                    <p class="text-xs text-[var(--color-muted)]">Reset passwords for all affected users. If the breach is severe, consider resetting all user passwords and rotating <code class="os-code-inline">JWT_SECRET</code>.</p>
                </div>
            </div>
            <div class="os-card os-card-pad flex items-start gap-3">
                <div class="w-7 h-7 bg-[var(--color-danger)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">6</div>
                <div>
                    <h4 class="text-sm font-semibold text-[var(--color-ink)]">Contact the development team</h4>
                    <p class="text-xs text-[var(--color-muted)]">Report the incident to the One System development team at <a href="https://innovativesolution.com.np/" target="_blank" rel="noopener" class="font-semibold text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">innovativesolution.com.np</a> for further investigation and assistance.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Navigation --}}
    <div class="flex justify-between items-center border-t border-[var(--color-line)] pt-6 mt-12">
        <a href="{{ route('docs.user-guide') }}" class="os-btn os-btn-ghost">
            <i class="fas fa-arrow-left"></i>User guide
        </a>
        <a href="{{ route('docs.security-guide') }}" class="os-btn os-btn-secondary">
            Security guide<i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endsection

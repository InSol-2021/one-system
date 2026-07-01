@extends('public.documentation.layout')

@section('title', 'Security guide — One System SSO')
@section('description', 'Hardening best practices for One System SSO at the application and server level: HTTPS, env secrets, 2FA, rate limiting, and incident response.')

@section('content')
<div class="os-container !px-0 !max-w-none">

    {{-- Page header --}}
    <div class="mb-10 border-b border-[var(--color-line)] pb-8">
        <div class="flex items-center gap-2 text-sm text-[var(--color-muted)] mb-4">
            <a href="{{ route('docs') }}" class="hover:text-[var(--color-accent)]">Docs</a>
            <i class="fas fa-chevron-right text-xs text-[var(--color-faint)]"></i>
            <span>Security guide</span>
        </div>
        <p class="os-eyebrow mb-3">Reference</p>
        <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-3">Security guide</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">Must-have and recommended steps for hardening your One System deployment at the application and server level.</p>
    </div>

    {{-- Application-level security --}}
    <div class="os-card os-card-pad mb-8">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-5 flex items-center gap-2.5">
            <i class="fas fa-shield-halved text-[var(--color-accent)]"></i>Application-level security (must have)
        </h2>

        <div class="space-y-7">
            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">1. HTTPS enforcement</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">All One System traffic must use HTTPS. Configure your web server to redirect HTTP to HTTPS — SSO tokens are passed as query parameters on the callback redirect and must never travel over an unencrypted connection.</p>
                <div class="os-codeblock">
                    <div class="os-codeblock-head">
                        <span>nginx · force https redirect</span>
                        <span>conf</span>
                    </div>
                    <pre><code>server {
    listen 80;
    server_name sso.yourdomain.com;
    return 301 https://$host$request_uri;
}</code></pre>
                </div>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">2. CORS and CSRF</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Browser-facing form submissions use Laravel's built-in CSRF protection. For cross-origin client apps, restrict which origins may call the API by setting <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> to your registered client domains.</p>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">3. Two-factor authentication (2FA)</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Enable 2FA for all admin accounts at minimum. One System supports TOTP-based 2FA via Google Authenticator, Authy, and similar apps.</p>
                <div class="os-alert os-alert-danger">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <div><strong>Critical:</strong> admin accounts without 2FA are a high-risk vulnerability. Always enforce 2FA for administrators.</div>
                </div>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">4. Authentication required on admin and user routes</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Admin and user management routes now require an authenticated session — they are no longer publicly reachable. Confirm you have applied the latest migrations so the route guards and supporting tables are in place.</p>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">5. Hashed client secrets</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Client secrets are stored hashed and shown in full only once, at creation or regeneration. Store the secret securely when it is displayed; it cannot be recovered afterward. Use it only for server-to-server calls such as token validation.</p>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">6. JWT token security</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">SSO tokens are JWTs signed with HS256 using the server's <code class="os-code-inline">JWT_SECRET</code>. Best practices:</p>
                <ul class="space-y-1.5 text-sm text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Tokens are short-lived and single-use — validate once, then create your own session</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Set a strong <code class="os-code-inline">JWT_SECRET</code> via the environment, never in source control</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Always validate tokens server side against <code class="os-code-inline">/api/validate-token</code>; never trust browser-side claims</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Rotate the JWT secret periodically</li>
                </ul>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">7. IP whitelisting for token issuance</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Service-to-service token issuance (<code class="os-code-inline">POST /api/sso/token</code>) is restricted to pre-registered IP addresses. Add the IPs of any backend that issues tokens on a user's behalf, and supply <code class="os-code-inline">client_id</code> and <code class="os-code-inline">client_secret</code> with each call.</p>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">8. Rate limiting</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Authentication and validation endpoints are rate-limited to blunt brute-force and denial-of-service attacks. Tune the limits in Laravel's throttle middleware to match your traffic.</p>
            </div>
        </div>
    </div>

    {{-- Server-level security --}}
    <div class="os-card os-card-pad mb-8">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-5 flex items-center gap-2.5">
            <i class="fas fa-server text-[var(--color-accent)]"></i>Server-level security (recommended)
        </h2>

        <div class="space-y-7">
            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">1. Firewall configuration</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Configure UFW or iptables to allow only the ports you need:</p>
                <div class="os-codeblock">
                    <div class="os-codeblock-head">
                        <span>ufw · allow ssh, http, https</span>
                        <span>bash</span>
                    </div>
                    <pre><code>sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable</code></pre>
                </div>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">2. SSH hardening</h3>
                <ul class="space-y-1.5 text-sm text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Disable root login over SSH</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Use key-based authentication instead of passwords</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Change the default SSH port (optional)</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Use fail2ban to block brute-force SSH attempts</li>
                </ul>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">3. Docker security</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">If running One System in Docker (the recommended deployment):</p>
                <ul class="space-y-1.5 text-sm text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Run non-root users inside containers</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Keep Docker and base images updated</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Limit container resource usage (CPU, memory)</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Do not expose database ports to the public network</li>
                </ul>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">4. Database security</h3>
                <ul class="space-y-1.5 text-sm text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Use strong, unique passwords for database users</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Keep PostgreSQL on an internal network; do not expose it publicly</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Enable SSL for database connections in production</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Run regular database backups</li>
                </ul>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">5. SSL / TLS configuration</h3>
                <ul class="space-y-1.5 text-sm text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Use TLS 1.2 or newer; disable TLS 1.0 and 1.1</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Use Let's Encrypt or a trusted CA for certificates</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Enable HSTS headers</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Automate certificate renewal</li>
                </ul>
            </div>

            <div>
                <h3 class="text-base font-semibold text-[var(--color-ink)] mb-2">6. Regular updates</h3>
                <p class="text-sm text-[var(--color-ink-2)] mb-3 leading-relaxed">Keep every component current:</p>
                <ul class="space-y-1.5 text-sm text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Operating system packages (<code class="os-code-inline">apt update &amp;&amp; apt upgrade</code>)</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Docker images</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>PHP and Laravel dependencies (<code class="os-code-inline">composer update</code>)</li>
                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[3px] text-[var(--color-faint)] mt-2"></i>Node.js packages where applicable</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Audit & monitoring --}}
    <div class="os-card os-card-pad mb-8">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2.5">
            <i class="fas fa-eye text-[var(--color-accent)]"></i>Audit and monitoring
        </h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4 leading-relaxed">One System includes a comprehensive audit log. Administrators should regularly review:</p>
        <ul class="space-y-3 text-sm text-[var(--color-ink-2)]">
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">Login attempts</strong> — watch for unusual patterns or failed-login spikes</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">Token events</strong> — track SSO token issuance and validation</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">IP whitelist rejections</strong> — check for unauthorized service-to-service attempts</div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fas fa-check text-[var(--color-accent)] mt-1 text-xs"></i>
                <div><strong class="text-[var(--color-ink)]">Admin actions</strong> — user creation, role changes, configuration updates</div>
            </li>
        </ul>

        <div class="os-alert mt-4">
            <i class="fas fa-circle-info text-[var(--color-accent)] mt-0.5"></i>
            <div>Access audit logs from the admin panel under <strong>Audit logs</strong>. Schedule a routine weekly review as a security best practice.</div>
        </div>
    </div>

    {{-- Incident response --}}
    <div class="os-card os-card-pad mb-8">
        <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2.5">
            <i class="fas fa-triangle-exclamation text-[var(--color-accent)]"></i>Suspected breach and incident response
        </h2>
        <p class="text-sm text-[var(--color-ink-2)] mb-4 leading-relaxed">If you suspect a security breach:</p>
        <ol class="space-y-2.5 text-sm text-[var(--color-ink-2)]">
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent shrink-0">1</span><div><strong class="text-[var(--color-ink)]">Rotate secrets immediately</strong> — regenerate <code class="os-code-inline">JWT_SECRET</code> and every client secret</div></li>
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent shrink-0">2</span><div><strong class="text-[var(--color-ink)]">Review audit logs</strong> to identify the scope and source</div></li>
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent shrink-0">3</span><div><strong class="text-[var(--color-ink)]">Revoke active sessions</strong> by clearing the session store</div></li>
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent shrink-0">4</span><div><strong class="text-[var(--color-ink)]">Tighten IP whitelisting</strong> for token issuance to known hosts only</div></li>
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent shrink-0">5</span><div><strong class="text-[var(--color-ink)]">Force password resets</strong> for all affected accounts</div></li>
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent shrink-0">6</span><div><strong class="text-[var(--color-ink)]">Contact support</strong> at <a href="https://innovativesolution.com.np/" target="_blank" rel="noopener" class="font-medium text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">innovativesolution.com.np</a> for assistance</div></li>
        </ol>
    </div>

    {{-- Navigation --}}
    <div class="flex justify-between items-center border-t border-[var(--color-line)] pt-6">
        <a href="{{ route('docs.user-guide') }}" class="os-btn os-btn-ghost">
            <i class="fas fa-arrow-left"></i>User guide
        </a>
        <a href="{{ route('docs.troubleshooting') }}" class="os-btn os-btn-secondary">
            Troubleshooting<i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endsection

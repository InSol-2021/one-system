@extends('public.documentation.layout')

@section('title', 'Troubleshooting — One System SSO')
@section('description', 'Common issues and solutions for the One System single sign-on platform.')

@section('content')
<div class="os-container !px-0 !max-w-none">

{{-- Hero --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <p class="os-eyebrow mb-3">Reference</p>
    <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Troubleshooting</h1>
    <p class="text-lg text-[var(--color-muted)] leading-relaxed">Solutions for the integration issues and error responses you are most likely to hit when wiring up One System.</p>
</section>

{{-- Authentication issues --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Authentication issues</h2>
    <div class="space-y-3">
        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Token validation returns 401</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <p class="mb-2">A <code class="os-code-inline">POST /api/validate-token</code> call fails when the token, client ID, or client secret does not match.</p>
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Confirm your <code class="os-code-inline">client_id</code> and <code class="os-code-inline">client_secret</code> match the values shown when the client was created or last regenerated. Secrets are stored hashed and shown only once.</li>
                    <li>Remember tokens are <strong class="text-[var(--color-ink-2)]">single-use</strong> — validating the same token twice returns 401 the second time. Validate once, then create your own session.</li>
                    <li>Validate server to server only. The secret must never appear in a browser request.</li>
                    <li>Pass the exact <code class="os-code-inline">token</code> value from the callback query string, with no extra encoding.</li>
                </ul>
            </div>
        </details>

        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Account locked — 423 response</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <p class="mb-2">Accounts are locked after five failed login attempts.</p>
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Wait 30 minutes for the automatic unlock.</li>
                    <li>Ask an admin to unlock manually from <strong class="text-[var(--color-ink-2)]">Admin panel &rarr; Users</strong>.</li>
                    <li>The response includes <code class="os-code-inline">remaining_minutes</code> until the auto-unlock.</li>
                </ul>
            </div>
        </details>

        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Token expired immediately after issue</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <p class="mb-2">SSO tokens are HS256 JWTs with a short lifetime. Premature expiry is usually clock skew between One System and your application.</p>
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Sync both server clocks with NTP.</li>
                    <li>Allow a small tolerance when reading the <code class="os-code-inline">exp</code> claim during validation.</li>
                    <li>Confirm the same <code class="os-code-inline">JWT_SECRET</code> is configured on the server issuing and verifying the token.</li>
                </ul>
            </div>
        </details>

        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Callback never receives a token</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <p class="mb-2">After <code class="os-code-inline">GET /sso/login?client_id=...</code> the browser is redirected to the client's registered callback with <code class="os-code-inline">?token=...</code> appended.</p>
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Check that the <code class="os-code-inline">callback_url</code> registered for the client exactly matches the route you serve.</li>
                    <li>Read the token from the <code class="os-code-inline">token</code> query parameter at the callback.</li>
                    <li>Make sure <code class="os-code-inline">client_id</code> is present on the <code class="os-code-inline">/sso/login</code> request.</li>
                </ul>
            </div>
        </details>
    </div>
</section>

{{-- Connection issues --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Connection issues</h2>
    <div class="space-y-3">
        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Connection refused to One System</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Verify the One System base URL configured in your client.</li>
                    <li>Check firewall rules — the configured port must be open between your app and One System.</li>
                    <li>If using Docker, ensure the containers share a network.</li>
                    <li>Test connectivity: <code class="os-code-inline">curl https://your-one-system.com/api/health</code></li>
                </ul>
            </div>
        </details>

        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">SSL certificate errors</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Serve One System over HTTPS in production and keep the certificate valid and unexpired.</li>
                    <li>For local development, add the self-signed CA to your trust store.</li>
                    <li>Verify the certificate covers your domain (check the SANs).</li>
                    <li>In PHP, set <code class="os-code-inline">CURLOPT_CAINFO</code> to your CA bundle path.</li>
                </ul>
            </div>
        </details>

        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">CORS error in the browser</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Add your client origin to the <code class="os-code-inline">CORS_ALLOWED_ORIGINS</code> environment variable on the server.</li>
                    <li>Remember token validation is a server-to-server call — it should not run from the browser, so most CORS errors point to a misplaced validation request.</li>
                    <li>Restart the server after changing environment variables.</li>
                </ul>
            </div>
        </details>

        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Database connection timeout</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Check the <code class="os-code-inline">DB_HOST</code> and <code class="os-code-inline">DB_PORT</code> values.</li>
                    <li>Verify PostgreSQL is running: <code class="os-code-inline">pg_isready</code></li>
                    <li>Check the connection ceiling: <code class="os-code-inline">SHOW max_connections;</code></li>
                    <li>Consider connection pooling with PgBouncer for high traffic.</li>
                </ul>
            </div>
        </details>
    </div>
</section>

{{-- Setup & migrations --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Setup &amp; migrations</h2>
    <div class="space-y-3">
        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Missing column or table after upgrade</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <p class="mb-2">Recent security hardening ships new migrations (hashed client secrets, audit fields). Run them before serving traffic.</p>
                <div class="os-codeblock mt-3">
                    <div class="os-codeblock-head">
                        <span>terminal</span>
                        <span>Bash</span>
                    </div>
                    <pre><code>php artisan migrate
php artisan config:clear</code></pre>
                </div>
            </div>
        </details>

        <details class="group os-card overflow-hidden">
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">reCAPTCHA always fails on login</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <ul class="space-y-1.5 ml-4 list-disc">
                    <li>Set the <code class="os-code-inline">RECAPTCHAV3_SITEKEY</code> and <code class="os-code-inline">RECAPTCHAV3_SECRET</code> environment variables, then clear config cache.</li>
                    <li>Confirm the registered reCAPTCHA domain matches the host serving the login page.</li>
                    <li>Check the server can reach Google's verification endpoint.</li>
                </ul>
            </div>
        </details>
    </div>
</section>

{{-- Rate limiting --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Rate limiting</h2>
    <div class="space-y-3">
        <details class="group os-card overflow-hidden" open>
            <summary class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-[var(--color-surface-2)] transition-colors">
                <span class="text-sm font-semibold text-[var(--color-ink)]">429 Too many requests</span>
                <i class="fas fa-chevron-down text-[var(--color-faint)] text-xs group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-5 pb-4 text-sm text-[var(--color-muted)] leading-relaxed border-t border-[var(--color-line)] pt-4">
                <p class="mb-2">Your application is exceeding the rate limit. Read the <code class="os-code-inline">Retry-After</code> response header and back off before retrying.</p>
                <div class="os-codeblock mt-3">
                    <div class="os-codeblock-head">
                        <span>exponential backoff</span>
                        <span>JS</span>
                    </div>
                    <pre><code>// Wait progressively longer between retries
const delay = Math.pow(2, retryCount) * 1000;
await new Promise(r => setTimeout(r, delay));</code></pre>
                </div>
            </div>
        </details>
    </div>
</section>

{{-- Debug commands --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Debug commands</h2>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>terminal</span>
            <span>Bash</span>
        </div>
        <pre><code># Check One System health
curl https://your-one-system.com/api/health

# Tail the application log
tail -f storage/logs/laravel.log

# Clear caches after an env change
php artisan cache:clear && php artisan config:clear

# Inspect the database connection
php artisan db:show</code></pre>
    </div>
</section>

{{-- Contact & support --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Contact &amp; support</h2>
    <p class="text-sm text-[var(--color-muted)] mb-6">If the steps above don't resolve your issue, reach out to the One System team.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
        <div class="os-card os-card-pad">
            <div class="flex items-center gap-3 mb-3">
                <span class="os-icon-tile"><i class="fas fa-envelope text-sm"></i></span>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">Technical support</h3>
            </div>
            <p class="text-sm text-[var(--color-muted)] mb-2">For integration help, error reports, or questions about the API:</p>
            <a href="https://innovativesolution.com.np/" target="_blank" rel="noopener" class="text-sm font-semibold text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">innovativesolution.com.np</a>
            <p class="text-xs text-[var(--color-faint)] mt-2">Expected response time: <strong class="text-[var(--color-muted)]">within 24 hours</strong> on business days.</p>
        </div>
        <div class="os-card os-card-pad">
            <div class="flex items-center gap-3 mb-3">
                <span class="os-icon-tile os-icon-tile-ink"><i class="fas fa-building text-sm"></i></span>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">Development team</h3>
            </div>
            <p class="text-sm text-[var(--color-muted)] mb-2">One System is developed and maintained by:</p>
            <p class="text-sm font-semibold text-[var(--color-ink)]">Innovative Solution Pvt. Ltd.</p>
            <p class="text-xs text-[var(--color-faint)] mt-1">Lalitpur, Nepal</p>
        </div>
    </div>

    <div class="os-card os-card-pad">
        <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3 flex items-center gap-2"><i class="fas fa-bug text-[var(--color-accent)]"></i>How to report an issue</h3>
        <p class="text-sm text-[var(--color-muted)] mb-3">Include the following for the fastest resolution:</p>
        <ol class="space-y-2 text-sm text-[var(--color-muted)]">
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">1.</span> <span><strong class="text-[var(--color-ink-2)]">Description</strong> — what happened versus what you expected.</span></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">2.</span> <span><strong class="text-[var(--color-ink-2)]">Steps to reproduce</strong> — how to trigger the issue.</span></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">3.</span> <span><strong class="text-[var(--color-ink-2)]">Error messages</strong> — full error text or HTTP status codes.</span></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">4.</span> <span><strong class="text-[var(--color-ink-2)]">Environment</strong> — language and framework, SDK version, One System version.</span></li>
            <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">5.</span> <span><strong class="text-[var(--color-ink-2)]">Logs</strong> — relevant entries from <code class="os-code-inline">storage/logs/laravel.log</code> (server) or your application logs (client).</span></li>
        </ol>
    </div>
</section>

</div>
@endsection

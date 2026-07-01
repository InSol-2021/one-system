@extends('public.documentation.layout')

@section('title', 'System architecture — One System SSO')
@section('description', 'Technical architecture overview of the One System single sign-on authentication platform.')

@section('content')
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <p class="os-eyebrow mb-3">Advanced topics</p>
        <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">System architecture</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">Technical overview of the One System SSO platform components and data flow.</p>
    </div>
</section>

<section class="mb-12">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-6">Architecture overview</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="os-card os-card-pad">
            <div class="os-icon-tile os-icon-tile-ink mb-4"><i class="fas fa-server text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">One System server</h3>
            <p class="text-sm text-[var(--color-muted)]">Laravel-based authentication server. Handles user management, token issuance, 2FA, and the admin panel.</p>
            <div class="mt-3 flex flex-wrap gap-1.5">
                <span class="os-badge">Laravel 11</span>
                <span class="os-badge">PHP 8.2</span>
            </div>
        </div>
        <div class="os-card os-card-pad">
            <div class="os-icon-tile os-icon-tile-ink mb-4"><i class="fas fa-database text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Data layer</h3>
            <p class="text-sm text-[var(--color-muted)]">PostgreSQL for persistent data. Redis for session management, caching, and rate-limit counters.</p>
            <div class="mt-3 flex flex-wrap gap-1.5">
                <span class="os-badge">PostgreSQL 16</span>
                <span class="os-badge">Redis 7</span>
            </div>
        </div>
        <div class="os-card os-card-pad">
            <div class="os-icon-tile os-icon-tile-ink mb-4"><i class="fas fa-plug text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Client SDKs</h3>
            <p class="text-sm text-[var(--color-muted)]">Six official client libraries. Each handles the SSO redirect, token validation, and session management for its platform.</p>
            <div class="mt-3 flex flex-wrap gap-1.5">
                <span class="os-badge">Laravel</span>
                <span class="os-badge">Node.js</span>
                <span class="os-badge">Python</span>
            </div>
        </div>
    </div>
</section>

<section class="mb-12">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-4">SSO request flow</h2>
    <div class="os-card os-card-pad">
        <div class="space-y-0">
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">1</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Browser redirect to login</h3><p class="text-xs text-[var(--color-muted)]">The client app redirects the browser to <code class="os-code-inline">GET /sso/login?client_id=…</code></p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">2</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Security middleware</h3><p class="text-xs text-[var(--color-muted)]">Rate-limit check &rarr; reCAPTCHA v3 validation &rarr; CORS origin check on the login form</p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">3</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Authentication</h3><p class="text-xs text-[var(--color-muted)]">Credential verification against PostgreSQL &rarr; 2FA challenge (if enabled) &rarr; lockout check</p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">4</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Token issuance &amp; redirect</h3><p class="text-xs text-[var(--color-muted)]">A single-use HS256 JWT is signed with the server secret; the browser is 302-redirected to the registered <code class="os-code-inline">callback_url?token=…</code></p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">5</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-6"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Server-to-server validation</h3><p class="text-xs text-[var(--color-muted)]">The client backend posts the token, <code class="os-code-inline">client_id</code>, and <code class="os-code-inline">client_secret</code> to <code class="os-code-inline">/api/validate-token</code>. Single-use: it can be validated only once.</p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-8 h-8 bg-[var(--color-accent)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">6</div></div>
                <div class="pb-2"><h3 class="text-sm font-semibold text-[var(--color-ink)]">Audit &amp; session</h3><p class="text-xs text-[var(--color-muted)]">Event logged to the audit table; the client app creates its own local session for the user</p></div>
            </div>
        </div>
    </div>
</section>

<section id="database-schema" class="mb-12">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-4">Database schema</h2>
    <div class="os-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Table</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Purpose</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Key fields</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">users</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">User accounts and profiles</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">username, email, password_hash, role, is_2fa_enabled</td>
                </tr>
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">client_systems</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Registered applications</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">name, callback_url, client_id, client_secret_hash</td>
                </tr>
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">sso_tokens</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Issued single-use JWTs</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">user_id, token_hash, expires_at, used_at, client_system_id</td>
                </tr>
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">login_attempts</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Failed login tracking</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">username, ip, attempts, locked_until</td>
                </tr>
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">audit_logs</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">All auth events</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">user_id, action, ip, user_agent, timestamp</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Technical documentation — how the code functions --}}
<section class="mb-12">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-4">Technical documentation</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-6">How the One System codebase is structured and how each layer functions.</p>

    <div class="space-y-5">
        <div class="os-card os-card-pad">
            <div class="flex items-center gap-3 mb-3">
                <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-folder-open text-sm"></i></div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]">Directory structure</h3>
            </div>
            <div class="os-codeblock">
                <pre class="text-xs"><code>app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          <span class="text-[#94a3b8]"># Admin dashboard, users, clients, audit logs</span>
│   │   ├── Auth/           <span class="text-[#94a3b8]"># Login, logout, 2FA verification</span>
│   │   ├── Api/            <span class="text-[#94a3b8]"># Token issuance &amp; validation, health check</span>
│   │   └── Public/         <span class="text-[#94a3b8]"># Documentation, downloads</span>
│   ├── Middleware/
│   │   ├── AdminMiddleware.php        <span class="text-[#94a3b8]"># Role-based admin access (auth required)</span>
│   │   ├── IpWhitelistMiddleware.php  <span class="text-[#94a3b8]"># IP filtering for service-to-service routes</span>
│   │   └── AuthenticateMiddleware.php <span class="text-[#94a3b8]"># Session auth for admin &amp; user areas</span>
│   └── Livewire/           <span class="text-[#94a3b8]"># Real-time admin components</span>
├── Models/
│   ├── User.php            <span class="text-[#94a3b8]"># User accounts with role &amp; 2FA</span>
│   ├── ClientSystem.php    <span class="text-[#94a3b8]"># Registered applications (hashed secret)</span>
│   ├── SsoToken.php        <span class="text-[#94a3b8]"># Single-use JWT records</span>
│   ├── AuditLog.php        <span class="text-[#94a3b8]"># Authentication event log</span>
│   └── IpWhitelist.php     <span class="text-[#94a3b8]"># Allowed IP addresses</span>
└── Services/
    └── SsoService.php      <span class="text-[#94a3b8]"># Core SSO logic (token issuance/validation)</span></code></pre>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3"><i class="fas fa-gears text-[var(--color-accent)] mr-2"></i>Middleware pipeline</h3>
                <p class="text-xs text-[var(--color-muted)] mb-3">Web requests to the admin and user areas pass through these layers (in order):</p>
                <ol class="space-y-2 text-xs text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">1.</span> Authentication — admin and user routes now require a signed-in session</li>
                    <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">2.</span> CSRF protection — validates form tokens</li>
                    <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">3.</span> Admin middleware — restricts admin routes to <code class="os-code-inline">role=admin</code></li>
                    <li class="flex items-start gap-2"><span class="font-semibold text-[var(--color-ink)]">4.</span> IP whitelist — applied to the service-to-service token issuance route</li>
                </ol>
                <p class="text-xs text-[var(--color-muted)] mt-3">Token validation authenticates the caller with the <code class="os-code-inline">client_secret</code> instead of a session.</p>
            </div>
            <div class="os-card os-card-pad">
                <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3"><i class="fas fa-bolt text-[var(--color-accent)] mr-2"></i>Livewire components</h3>
                <p class="text-xs text-[var(--color-muted)] mb-3">The admin panel uses Livewire for real-time updates:</p>
                <ul class="space-y-2 text-xs text-[var(--color-ink-2)]">
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Dashboard stats</strong> — live counters for users, tokens, clients</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>User management</strong> — inline create/edit/delete with instant feedback</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Client systems</strong> — credential generation and copy-to-clipboard</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>Audit log viewer</strong> — paginated, filterable event log</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-[var(--color-accent)] mt-0.5"></i> <strong>IP whitelist</strong> — add/remove with instant validation</li>
                </ul>
            </div>
        </div>

        <div class="os-card os-card-pad">
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3"><i class="fas fa-database text-[var(--color-accent)] mr-2"></i>Models &amp; relationships</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-[var(--color-ink-2)]">
                <div>
                    <p class="font-semibold text-[var(--color-ink)] mb-1">User</p>
                    <ul class="space-y-1 ml-3">
                        <li>&rarr; hasMany <code class="os-code-inline">SsoToken</code></li>
                        <li>&rarr; hasMany <code class="os-code-inline">AuditLog</code></li>
                        <li>&rarr; belongsToMany <code class="os-code-inline">ClientSystem</code></li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-[var(--color-ink)] mb-1">ClientSystem</p>
                    <ul class="space-y-1 ml-3">
                        <li>&rarr; hasMany <code class="os-code-inline">SsoToken</code></li>
                        <li>&rarr; belongsToMany <code class="os-code-inline">User</code></li>
                        <li>&rarr; hasMany <code class="os-code-inline">IpWhitelist</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Package documentation — how each SDK functions --}}
<section class="mb-12">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-4">Package documentation</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-6">How each client SDK package works internally to integrate with One System.</p>

    <div class="os-card os-card-pad mb-5">
        <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-3"><i class="fas fa-right-left text-[var(--color-accent)] mr-2"></i>Common SDK flow</h3>
        <p class="text-xs text-[var(--color-muted)] mb-4">All SDK packages follow the same core authentication pattern:</p>
        <div class="space-y-0">
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">1</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-4"><h4 class="text-xs font-semibold text-[var(--color-ink)]">User visits a protected route</h4><p class="text-xs text-[var(--color-muted)]">SDK middleware intercepts the request and checks for a valid local session</p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">2</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-4"><h4 class="text-xs font-semibold text-[var(--color-ink)]">Redirect to One System login</h4><p class="text-xs text-[var(--color-muted)]">If no session exists, the browser is redirected to <code class="os-code-inline">/sso/login?client_id=…</code></p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">3</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-4"><h4 class="text-xs font-semibold text-[var(--color-ink)]">One System authenticates &amp; redirects back</h4><p class="text-xs text-[var(--color-muted)]">After login, a single-use JWT is appended to the registered <code class="os-code-inline">callback_url</code></p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-7 h-7 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">4</div><div class="w-px h-full bg-[var(--color-line)]"></div></div>
                <div class="pb-4"><h4 class="text-xs font-semibold text-[var(--color-ink)]">SDK validates the token via API</h4><p class="text-xs text-[var(--color-muted)]">The SDK backend posts the token, <code class="os-code-inline">client_id</code>, and <code class="os-code-inline">client_secret</code> to <code class="os-code-inline">/api/validate-token</code></p></div>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-center"><div class="w-7 h-7 bg-[var(--color-accent)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">5</div></div>
                <div class="pb-2"><h4 class="text-xs font-semibold text-[var(--color-ink)]">Session created</h4><p class="text-xs text-[var(--color-muted)]">The SDK stores user data in the local session. The user is now authenticated.</p></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('docs.laravel') }}" class="os-card os-card-pad os-card-hover group">
            <div class="flex items-center gap-2 mb-2"><i class="fab fa-laravel text-[var(--color-ink-2)]"></i><span class="text-sm font-semibold text-[var(--color-ink)]">Laravel</span></div>
            <p class="text-xs text-[var(--color-muted)]">Composer package with auto-discovery. Provides <code class="os-code-inline">sso.auth</code> middleware, service provider, and config publishing.</p>
        </a>
        <a href="{{ route('docs.nodejs') }}" class="os-card os-card-pad os-card-hover group">
            <div class="flex items-center gap-2 mb-2"><i class="fab fa-node-js text-[var(--color-ink-2)]"></i><span class="text-sm font-semibold text-[var(--color-ink)]">Node.js</span></div>
            <p class="text-xs text-[var(--color-muted)]">Express middleware via npm. Provides <code class="os-code-inline">ssoAuth()</code> middleware and an <code class="os-code-inline">SsoClient</code> class.</p>
        </a>
        <a href="{{ route('docs.python') }}" class="os-card os-card-pad os-card-hover group">
            <div class="flex items-center gap-2 mb-2"><i class="fab fa-python text-[var(--color-ink-2)]"></i><span class="text-sm font-semibold text-[var(--color-ink)]">Python</span></div>
            <p class="text-xs text-[var(--color-muted)]">Django middleware via pip. Provides an <code class="os-code-inline">SsoAuthMiddleware</code> class and management commands.</p>
        </a>
        <a href="{{ route('docs.java') }}" class="os-card os-card-pad os-card-hover group">
            <div class="flex items-center gap-2 mb-2"><i class="fab fa-java text-[var(--color-ink-2)]"></i><span class="text-sm font-semibold text-[var(--color-ink)]">Java</span></div>
            <p class="text-xs text-[var(--color-muted)]">Spring Security filter via Maven/Gradle. <code class="os-code-inline">SsoAuthFilter</code> integrates with Spring's filter chain.</p>
        </a>
        <a href="{{ route('docs.dotnet') }}" class="os-card os-card-pad os-card-hover group">
            <div class="flex items-center gap-2 mb-2"><i class="fab fa-microsoft text-[var(--color-ink-2)]"></i><span class="text-sm font-semibold text-[var(--color-ink)]">.NET / C#</span></div>
            <p class="text-xs text-[var(--color-muted)]">NuGet package with an <code class="os-code-inline">SsoAuthFilter</code> action filter and DI-based configuration.</p>
        </a>
        <a href="{{ route('docs.javascript') }}" class="os-card os-card-pad os-card-hover group">
            <div class="flex items-center gap-2 mb-2"><i class="fab fa-js text-[var(--color-ink-2)]"></i><span class="text-sm font-semibold text-[var(--color-ink)]">JavaScript</span></div>
            <p class="text-xs text-[var(--color-muted)]">Browser SDK via CDN or npm. Provides an <code class="os-code-inline">SsoClient</code> class that starts the redirect flow; token validation stays on your backend.</p>
        </a>
    </div>
</section>

{{-- API documentation — how the API functions --}}
<section class="mb-12">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-4">API documentation</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-6">Summary of the One System API endpoints. <a href="{{ route('docs.api.overview') }}" class="text-[var(--color-accent)] font-medium hover:text-[var(--color-accent-strong)]">View full API reference &rarr;</a></p>

    <div class="os-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Method</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Endpoint</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Purpose</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Auth</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr>
                    <td class="px-5 py-3"><span class="os-badge os-badge-accent">GET</span></td>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/sso/login</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Browser SSO entry; redirects to callback with token</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">client_id (public)</td>
                </tr>
                <tr>
                    <td class="px-5 py-3"><span class="os-badge os-badge-accent">POST</span></td>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/validate-token</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Validate a single-use token (server to server)</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">client_id + client_secret</td>
                </tr>
                <tr>
                    <td class="px-5 py-3"><span class="os-badge os-badge-accent">POST</span></td>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/sso/token</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Service-to-service token issuance</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">client_id + client_secret, IP-whitelisted</td>
                </tr>
                <tr>
                    <td class="px-5 py-3"><span class="os-badge os-badge-accent">POST</span></td>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/login</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Direct login with username/password</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">Credentials</td>
                </tr>
                <tr>
                    <td class="px-5 py-3"><span class="os-badge os-badge-accent">POST</span></td>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/logout</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Invalidate the current session/token</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">Bearer token</td>
                </tr>
                <tr>
                    <td class="px-5 py-3"><span class="os-badge">GET</span></td>
                    <td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">/api/user</td>
                    <td class="px-5 py-3 text-[var(--color-ink-2)]">Get authenticated user details</td>
                    <td class="px-5 py-3 text-xs text-[var(--color-muted)]">Bearer token</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="os-alert mt-4">
        <i class="fas fa-info-circle text-[var(--color-accent)] mt-0.5"></i>
        <span>Tokens are HS256 JWTs signed with the server's <code class="os-code-inline">JWT_SECRET</code> and are single-use &mdash; validate once via <code class="os-code-inline">/api/validate-token</code>, then create your app's own session. See the <a href="{{ route('docs.security') }}" class="font-semibold underline">security features</a> page for details.</span>
    </div>
</section>

<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-4">Technology stack</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="os-card os-card-pad text-center">
            <i class="fab fa-laravel text-[var(--color-ink-2)] text-2xl mb-2"></i>
            <p class="text-xs font-semibold text-[var(--color-ink)]">Laravel 11</p>
            <p class="text-xs text-[var(--color-faint)]">Backend</p>
        </div>
        <div class="os-card os-card-pad text-center">
            <i class="fas fa-database text-[var(--color-ink-2)] text-2xl mb-2"></i>
            <p class="text-xs font-semibold text-[var(--color-ink)]">PostgreSQL</p>
            <p class="text-xs text-[var(--color-faint)]">Database</p>
        </div>
        <div class="os-card os-card-pad text-center">
            <i class="fas fa-bolt text-[var(--color-ink-2)] text-2xl mb-2"></i>
            <p class="text-xs font-semibold text-[var(--color-ink)]">Redis</p>
            <p class="text-xs text-[var(--color-faint)]">Cache / sessions</p>
        </div>
        <div class="os-card os-card-pad text-center">
            <i class="fab fa-docker text-[var(--color-ink-2)] text-2xl mb-2"></i>
            <p class="text-xs font-semibold text-[var(--color-ink)]">Docker</p>
            <p class="text-xs text-[var(--color-faint)]">Deployment</p>
        </div>
    </div>
</section>
@endsection

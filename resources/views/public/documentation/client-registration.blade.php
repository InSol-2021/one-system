@extends('public.documentation.layout')

@section('title', 'Client registration — One System')
@section('description', 'Step-by-step guide to register client applications for One System SSO.')

@section('content')
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <p class="os-eyebrow mb-3">How to use</p>
        <h1 class="text-4xl font-bold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Client registration</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">How to register your applications with One System and configure SSO integration.</p>
    </div>
</section>

<nav class="os-card os-card-pad mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">On this page</h2>
    <ol class="space-y-1.5 text-sm">
        <li><a href="#what" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. What is a client system?</a></li>
        <li><a href="#register" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Registering a client</a></li>
        <li><a href="#credentials" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. Understanding credentials</a></li>
        <li><a href="#configure" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. Configuring your app</a></li>
        <li><a href="#test" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. Testing the connection</a></li>
    </ol>
</nav>

<section id="what" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">1. What is a client system?</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">A client system is any application that uses One System for user authentication. Each client gets a unique <code class="os-code-inline">client_id</code> and <code class="os-code-inline">client_secret</code> for secure communication with the server.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="os-card p-4">
            <div class="os-icon-tile os-icon-tile-ink mb-2"><i class="fas fa-globe"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Web apps</h3>
            <p class="text-xs text-[var(--color-muted)]">Laravel, Django, Express, Spring Boot, and .NET MVC applications.</p>
        </div>
        <div class="os-card p-4">
            <div class="os-icon-tile os-icon-tile-ink mb-2"><i class="fas fa-mobile-screen"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Mobile apps</h3>
            <p class="text-xs text-[var(--color-muted)]">iOS, Android, React Native, and Flutter applications.</p>
        </div>
        <div class="os-card p-4">
            <div class="os-icon-tile os-icon-tile-ink mb-2"><i class="fas fa-desktop"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Internal tools</h3>
            <p class="text-xs text-[var(--color-muted)]">Admin panels, CRM systems, HR portals, and internal dashboards.</p>
        </div>
    </div>
</section>

<section id="register" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">2. Registering a client</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Sign in to the admin panel, then go to <strong>Client systems &rarr; Add new</strong> and fill in:</p>
    <div class="os-card overflow-hidden mb-6">
        <table class="w-full text-sm">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Field</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Example</th>
                    <th class="text-left px-5 py-3 font-semibold text-[var(--color-ink-2)]">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">System name</td><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">Customer Portal</td><td class="px-5 py-3 text-[var(--color-muted)]">Human-readable application name.</td></tr>
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">System URL</td><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">https://portal.company.com</td><td class="px-5 py-3 text-[var(--color-muted)]">Base URL of the client application.</td></tr>
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">Callback URL</td><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">https://portal.company.com/cas/callback</td><td class="px-5 py-3 text-[var(--color-muted)]">Where One System redirects the browser with the token.</td></tr>
                <tr><td class="px-5 py-3 font-medium text-[var(--color-ink)]">Status</td><td class="px-5 py-3 font-mono text-xs text-[var(--color-ink-2)]">Active</td><td class="px-5 py-3 text-[var(--color-muted)]">Enable or disable the client.</td></tr>
            </tbody>
        </table>
    </div>
    <div class="os-alert">
        <i class="fas fa-circle-info mt-0.5"></i>
        <span>The callback URL must match exactly — One System only redirects to the registered value. Admin routes now require authentication.</span>
    </div>
</section>

<section id="credentials" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">3. Understanding credentials</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">After saving, the system issues a pair of credentials for your client:</p>
    <div class="space-y-3 mb-6">
        <div class="os-card flex items-start gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-fingerprint"></i></div>
            <div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]"><code class="os-code-inline">client_id</code></h3>
                <p class="text-xs text-[var(--color-muted)]">Public identifier for your app. Sent with every request, including the browser SSO redirect. Visible anytime in the admin panel.</p>
            </div>
        </div>
        <div class="os-card flex items-start gap-3 p-4 border-[var(--color-accent-line)] bg-[var(--color-accent-soft)]">
            <div class="os-icon-tile"><i class="fas fa-key"></i></div>
            <div>
                <h3 class="text-sm font-semibold text-[var(--color-ink)]"><code class="os-code-inline">client_secret</code></h3>
                <p class="text-xs text-[var(--color-muted)]">Used only in server-to-server calls (<code class="os-code-inline">/api/validate-token</code> and <code class="os-code-inline">/api/sso/token</code>). Stored <strong>hashed</strong> on the server and <strong class="text-[var(--color-accent-strong)]">shown only once</strong> on creation or regeneration — copy it immediately and keep it out of browser code.</p>
            </div>
        </div>
    </div>
    <div class="os-alert os-alert-warning">
        <i class="fas fa-triangle-exclamation mt-0.5"></i>
        <span>If you lose the secret you must regenerate it from the admin panel. Regenerating shows a new value once and invalidates the old one.</span>
    </div>
</section>

<section id="configure" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">4. Configuring your app</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Add the credentials to your client application's environment. Keep the secret in env, never in committed source:</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>.env — client application</span>
            <span>dotenv</span>
        </div>
        <pre><code>CAS_BASE=https://one-system.company.com
CAS_CLIENT_ID=generated_client_id
CAS_CLIENT_SECRET=generated_client_secret
CAS_CALLBACK_URL=https://your-app.com/cas/callback</code></pre>
    </div>
</section>

<section id="test" class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-4">5. Testing the connection</h2>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">From a whitelisted server, request a token to confirm the credentials work. The <code class="os-code-inline">/api/sso/token</code> endpoint is IP-whitelisted and expects a registered <code class="os-code-inline">username</code>:</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> {CAS_BASE}/api/sso/token</span>
            <span>bash</span>
        </div>
        <pre><code>curl -X POST https://one-system.company.com/api/sso/token \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": "YOUR_CLIENT_ID",
    "client_secret": "YOUR_CLIENT_SECRET",
    "username": "jdoe"
  }'</code></pre>
    </div>
    <div class="os-alert os-alert-success">
        <i class="fas fa-circle-check mt-0.5"></i>
        <span>A response containing a <code class="os-code-inline">redirect_url</code> and <code class="os-code-inline">token</code> means your client is correctly configured and IP-whitelisted.</span>
    </div>
</section>
@endsection

@extends('public.documentation.layout')

@section('title', 'Quick start guide — One System SSO')
@section('description', 'Get One System SSO running in minutes with this step-by-step quick start guide.')

@section('content')
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <p class="os-eyebrow mb-3">How to use</p>
        <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Quick start guide</h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed">Get One System single sign-on running and integrate your first application in under 10 minutes.</p>
        <div class="flex flex-wrap gap-4 mt-4 text-xs text-[var(--color-muted)]">
            <span class="flex items-center gap-1.5"><i class="fas fa-clock text-[var(--color-faint)]"></i> 10 minutes</span>
            <span class="flex items-center gap-1.5"><i class="fas fa-signal text-[var(--color-faint)]"></i> Beginner</span>
            <span class="flex items-center gap-1.5"><i class="fas fa-check-circle text-[var(--color-faint)]"></i> No prerequisites</span>
        </div>
    </div>
</section>

<nav class="os-card os-card-pad mb-12 bg-[var(--color-surface-2)]">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-3">Steps</h2>
    <ol class="space-y-1.5 text-sm">
        <li><a href="#step1" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. Install the One System server</a></li>
        <li><a href="#step2" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Configure environment</a></li>
        <li><a href="#step3" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. Run migrations</a></li>
        <li><a href="#step4" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. Create admin account</a></li>
        <li><a href="#step5" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. Register a client application</a></li>
        <li><a href="#step6" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">6. Test SSO authentication</a></li>
    </ol>
</nav>

{{-- Step 1 --}}
<section id="step1" class="mb-12">
    <div class="flex gap-4 mb-4">
        <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">1</div>
        <h2 class="text-xl font-semibold text-[var(--color-ink)]">Install the One System server</h2>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Clone the repository and install dependencies:</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Terminal</span></div>
        <pre><code><span class="text-[#94a3b8]"># Clone the repository</span>
git clone https://github.com/your-org/one-system.git
cd one-system

<span class="text-[#94a3b8]"># Install PHP dependencies</span>
composer install

<span class="text-[#94a3b8]"># Install frontend dependencies and build assets</span>
npm install && npm run build</code></pre>
    </div>
</section>

{{-- Step 2 --}}
<section id="step2" class="mb-12">
    <div class="flex gap-4 mb-4">
        <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">2</div>
        <h2 class="text-xl font-semibold text-[var(--color-ink)]">Configure environment</h2>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Copy the environment file, generate the app key, and set your configuration:</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head"><span>Terminal</span></div>
        <pre><code>cp .env.example .env
php artisan key:generate</code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>.env</span></div>
        <pre><code><span class="text-[#94a3b8]"># Database</span>
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=one_system
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

<span class="text-[#94a3b8]"># Redis (optional, recommended for sessions and cache)</span>
CACHE_DRIVER=redis
SESSION_DRIVER=redis

<span class="text-[#94a3b8]"># JWT signing secret — required (HS256, keep this private)</span>
JWT_SECRET=your_long_random_secret

<span class="text-[#94a3b8]"># reCAPTCHA v3 (recommended for the public login form)</span>
RECAPTCHAV3_SITEKEY=your_recaptcha_site_key
RECAPTCHAV3_SECRET=your_recaptcha_secret

<span class="text-[#94a3b8]"># Allowed browser origins for SSO redirects / CORS</span>
CORS_ALLOWED_ORIGINS=https://app.example.com,https://admin.example.com</code></pre>
    </div>
</section>

{{-- Step 3 --}}
<section id="step3" class="mb-12">
    <div class="flex gap-4 mb-4">
        <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">3</div>
        <h2 class="text-xl font-semibold text-[var(--color-ink)]">Run migrations</h2>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Create the database tables and seed initial data. Re-run this after every upgrade to apply new migrations:</p>
    <div class="os-codeblock">
        <pre><code>php artisan migrate --seed</code></pre>
    </div>
    <p class="text-xs text-[var(--color-muted)] mt-3">This creates: <code class="os-code-inline">users</code>, <code class="os-code-inline">client_systems</code>, <code class="os-code-inline">sso_tokens</code>, <code class="os-code-inline">audit_logs</code>, <code class="os-code-inline">ip_whitelists</code>, and more.</p>
</section>

{{-- Step 4 --}}
<section id="step4" class="mb-12">
    <div class="flex gap-4 mb-4">
        <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">4</div>
        <h2 class="text-xl font-semibold text-[var(--color-ink)]">Create admin account</h2>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Register your first admin user. Start the server first:</p>
    <div class="os-codeblock mb-4">
        <pre><code>php artisan serve --port=8000</code></pre>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-3">Then navigate to <code class="os-code-inline">http://localhost:8000/auth/register</code> and create your account. The first user registered is automatically assigned the <strong>admin</strong> role.</p>
    <div class="os-alert os-alert-warning">
        <i class="fas fa-exclamation-triangle mt-0.5"></i>
        <span><strong>Important:</strong> Only the first registered user gets admin privileges. Subsequent users are created as regular users. The admin and user areas now require authentication, so sign in before visiting any panel route.</span>
    </div>
</section>

{{-- Step 5 --}}
<section id="step5" class="mb-12">
    <div class="flex gap-4 mb-4">
        <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">5</div>
        <h2 class="text-xl font-semibold text-[var(--color-ink)]">Register a client application</h2>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">Sign in to the admin panel and register your first client application:</p>
    <ol class="space-y-3 text-sm text-[var(--color-ink-2)] mb-6">
        <li class="flex items-start gap-3">
            <span class="w-5 h-5 bg-[var(--color-accent-soft)] text-[var(--color-accent-strong)] border border-[var(--color-accent-line)] rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">a</span>
            <span>Navigate to <strong>Admin panel &rarr; Client systems &rarr; Add new</strong></span>
        </li>
        <li class="flex items-start gap-3">
            <span class="w-5 h-5 bg-[var(--color-accent-soft)] text-[var(--color-accent-strong)] border border-[var(--color-accent-line)] rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">b</span>
            <span>Enter your application name, URL, and the registered <strong>callback URL</strong> One System will redirect back to</span>
        </li>
        <li class="flex items-start gap-3">
            <span class="w-5 h-5 bg-[var(--color-accent-soft)] text-[var(--color-accent-strong)] border border-[var(--color-accent-line)] rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">c</span>
            <span>Save &mdash; the system generates a <code class="os-code-inline">client_id</code> and <code class="os-code-inline">client_secret</code>. The secret is <strong>hashed at rest</strong> and shown in full only once</span>
        </li>
        <li class="flex items-start gap-3">
            <span class="w-5 h-5 bg-[var(--color-accent-soft)] text-[var(--color-accent-strong)] border border-[var(--color-accent-line)] rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">d</span>
            <span><strong>Copy the client secret immediately</strong> &mdash; you can only regenerate it, not view it again</span>
        </li>
    </ol>
    <div class="os-alert">
        <i class="fas fa-info-circle text-[var(--color-accent)] mt-0.5"></i>
        <span>For the service-to-service token endpoint, add your client server's IP to the whitelist from <strong>Admin &rarr; IP whitelist</strong> so One System accepts those requests.</span>
    </div>
</section>

{{-- Step 6 --}}
<section id="step6" class="mb-12">
    <div class="flex gap-4 mb-4">
        <div class="w-8 h-8 bg-[var(--color-ink)] text-white rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0">6</div>
        <h2 class="text-xl font-semibold text-[var(--color-ink)]">Test SSO authentication</h2>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">There are two ways to obtain a token. For most apps, the browser is redirected to the SSO login endpoint:</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">GET</span> /sso/login</span>
        </div>
        <pre><code><span class="text-[#94a3b8]"># Send the browser here; One System authenticates the user and 302-redirects</span>
<span class="text-[#94a3b8]"># back to your registered callback_url with the token appended.</span>
GET http://localhost:8000/sso/login?client_id=YOUR_CLIENT_ID

<span class="text-[#94a3b8]"># Redirect lands at: https://your-app.example.com/callback?token=&lt;JWT&gt;</span></code></pre>
    </div>

    <p class="text-sm text-[var(--color-ink-2)] mb-4">To mint a token server to server (for example, in a back-channel job), call the IP-whitelisted issuance endpoint with your client credentials and a username:</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> /api/sso/token</span>
        </div>
        <pre><code>curl -X POST http://localhost:8000/api/sso/token \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": "YOUR_CLIENT_ID",
    "client_secret": "YOUR_CLIENT_SECRET",
    "username": "alice"
  }'</code></pre>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">On success you receive a single-use JWT plus the redirect URL to hand back to the browser:</p>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head"><span>Response</span></div>
        <pre><code>{
  <span class="text-[#a5b4fc]">"token"</span>: <span class="text-[#e2e8f0]">"eyJhbGciOiJIUzI1NiIs..."</span>,
  <span class="text-[#a5b4fc]">"redirect_url"</span>: <span class="text-[#e2e8f0]">"https://your-app.example.com/callback?token=eyJhbGci..."</span>
}</code></pre>
    </div>

    <p class="text-sm text-[var(--color-ink-2)] mb-4">At your callback, validate the token <strong>server to server</strong> (the client secret must never touch the browser). The token is single-use &mdash; validate it once, then create your app's own session:</p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><span class="os-badge os-badge-accent">POST</span> /api/validate-token</span>
        </div>
        <pre><code>curl -X POST http://localhost:8000/api/validate-token \
  -H "Content-Type: application/json" \
  -d '{
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "client_id": "YOUR_CLIENT_ID",
    "client_secret": "YOUR_CLIENT_SECRET"
  }'</code></pre>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] mb-4">A valid token returns the user and expiry:</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Response</span></div>
        <pre><code>{
  <span class="text-[#a5b4fc]">"valid"</span>: <span class="text-[#e2e8f0]">true</span>,
  <span class="text-[#a5b4fc]">"user"</span>: {
    <span class="text-[#a5b4fc]">"id"</span>: <span class="text-[#e2e8f0]">1</span>,
    <span class="text-[#a5b4fc]">"username"</span>: <span class="text-[#e2e8f0]">"alice"</span>,
    <span class="text-[#a5b4fc]">"email"</span>: <span class="text-[#e2e8f0]">"alice@example.com"</span>
  },
  <span class="text-[#a5b4fc]">"expires_at"</span>: <span class="text-[#e2e8f0]">"2026-06-18T12:30:00Z"</span>
}</code></pre>
    </div>
</section>

{{-- What's next --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-4">What's next?</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('docs.client-registration') }}" class="os-card os-card-pad os-card-hover group">
            <div class="os-icon-tile mb-3"><i class="fas fa-plus-circle"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Register more clients</h3>
            <p class="text-xs text-[var(--color-muted)]">Add your other applications to the SSO ecosystem.</p>
        </a>
        <a href="{{ route('docs.admin-panel') }}" class="os-card os-card-pad os-card-hover group">
            <div class="os-icon-tile mb-3"><i class="fas fa-gauge-high"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Admin panel guide</h3>
            <p class="text-xs text-[var(--color-muted)]">Learn to manage users, audit logs, and system settings.</p>
        </a>
        <a href="{{ route('docs.user-management') }}" class="os-card os-card-pad os-card-hover group">
            <div class="os-icon-tile mb-3"><i class="fas fa-users"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">User management</h3>
            <p class="text-xs text-[var(--color-muted)]">Create, update, and manage user accounts and roles.</p>
        </a>
        <a href="{{ route('docs.two-factor-auth') }}" class="os-card os-card-pad os-card-hover group">
            <div class="os-icon-tile mb-3"><i class="fas fa-shield-halved"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Enable 2FA</h3>
            <p class="text-xs text-[var(--color-muted)]">Set up two-factor authentication for enhanced security.</p>
        </a>
    </div>
</section>
@endsection

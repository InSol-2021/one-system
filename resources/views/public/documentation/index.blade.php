@extends('public.documentation.layout')

@section('title', 'One System SSO integration documentation')
@section('description', 'Complete integration guide for the One System single sign-on service, with SDKs for Laravel, .NET, Node.js, Java, Python, and vanilla JavaScript.')

@section('content')
{{-- Hero —— lightweight header that sits naturally inside the sidebar layout --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <p class="os-eyebrow mb-3">Documentation</p>
        <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">
            One System SSO integration guide
        </h1>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed mb-8">
            Everything you need to connect your applications to One System single sign-on.
            Choose your platform, follow the guide, and go live in minutes.
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="#platforms" class="os-btn os-btn-primary">
                <i class="fas fa-rocket text-xs"></i> Get started
            </a>
            <a href="{{ route('docs.api.overview') }}" class="os-btn os-btn-secondary">
                <i class="fas fa-code text-xs"></i> API reference
            </a>
        </div>
    </div>
</section>

{{-- Why One System —— three compact value props --}}
<section class="mb-16">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-6">Why One System SSO</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="os-card os-card-pad os-card-hover">
            <div class="os-icon-tile mb-4"><i class="fas fa-lock text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Secure authentication</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Single-use HS256 JWTs signed with a server-side secret, validated server to server.</p>
        </div>
        <div class="os-card os-card-pad os-card-hover">
            <div class="os-icon-tile mb-4"><i class="fas fa-cubes text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Multi-platform</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Official client packages for Laravel, .NET, Node.js, Java, Python, and vanilla JavaScript.</p>
        </div>
        <div class="os-card os-card-pad os-card-hover">
            <div class="os-icon-tile mb-4"><i class="fas fa-chart-line text-sm"></i></div>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1.5">Audit &amp; monitoring</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Full audit trail with IP tracking, user agent logging, and real-time dashboards.</p>
        </div>
    </div>
</section>

{{-- Architecture —— two side-by-side cards --}}
<section class="mb-16">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-6">Architecture</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('docs.architecture') }}" class="os-card os-card-pad os-card-hover group block">
            <div class="flex items-center gap-3 mb-3">
                <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-sitemap text-sm"></i></div>
                <h3 class="text-base font-semibold text-[var(--color-ink)]">System architecture</h3>
            </div>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-3">Admin, user, and public separation, Livewire components, and the comprehensive security layer.</p>
            <span class="text-sm font-medium text-[var(--color-accent)] inline-flex items-center">
                Read more <i class="fas fa-arrow-right ml-1.5 text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </span>
        </a>
        <a href="{{ route('docs.architecture') }}#database-schema" class="os-card os-card-pad os-card-hover group block">
            <div class="flex items-center gap-3 mb-3">
                <div class="os-icon-tile os-icon-tile-ink"><i class="fas fa-database text-sm"></i></div>
                <h3 class="text-base font-semibold text-[var(--color-ink)]">Database schema</h3>
            </div>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-3">PostgreSQL design with security isolation, access controls, and audit trails.</p>
            <span class="text-sm font-medium text-[var(--color-accent)] inline-flex items-center">
                Read more <i class="fas fa-arrow-right ml-1.5 text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </span>
        </a>
    </div>
</section>

{{-- Platform guides —— the main grid --}}
<section id="platforms" class="mb-16">
    <div class="flex items-end justify-between mb-6">
        <div>
            <h2 class="os-eyebrow text-[var(--color-muted)] mb-1">Integration guides</h2>
            <p class="text-sm text-[var(--color-muted)]">Choose your stack and follow the step-by-step guide.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Laravel --}}
        <a href="{{ route('docs.laravel') }}" class="os-card os-card-hover group flex items-center gap-4 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fab fa-laravel text-lg"></i></div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-[var(--color-ink)]">Laravel</span>
                    <span class="os-badge os-badge-accent">Popular</span>
                </div>
                <p class="text-xs text-[var(--color-muted)] mt-0.5">Composer package &middot; 5 min</p>
            </div>
            <i class="fas fa-chevron-right text-[var(--color-faint)] text-xs group-hover:text-[var(--color-muted)] transition-colors"></i>
        </a>

        {{-- .NET --}}
        <a href="{{ route('docs.dotnet') }}" class="os-card os-card-hover group flex items-center gap-4 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fab fa-microsoft text-lg"></i></div>
            <div class="min-w-0 flex-1">
                <span class="text-sm font-semibold text-[var(--color-ink)]">.NET MVC</span>
                <p class="text-xs text-[var(--color-muted)] mt-0.5">C# &middot; NuGet &middot; 10 min</p>
            </div>
            <i class="fas fa-chevron-right text-[var(--color-faint)] text-xs group-hover:text-[var(--color-muted)] transition-colors"></i>
        </a>

        {{-- Node.js --}}
        <a href="{{ route('docs.nodejs') }}" class="os-card os-card-hover group flex items-center gap-4 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fab fa-node-js text-lg"></i></div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-[var(--color-ink)]">Node.js</span>
                    <span class="os-badge os-badge-accent">Popular</span>
                </div>
                <p class="text-xs text-[var(--color-muted)] mt-0.5">Express middleware &middot; 3 min</p>
            </div>
            <i class="fas fa-chevron-right text-[var(--color-faint)] text-xs group-hover:text-[var(--color-muted)] transition-colors"></i>
        </a>

        {{-- Java --}}
        <a href="{{ route('docs.java') }}" class="os-card os-card-hover group flex items-center gap-4 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fab fa-java text-lg"></i></div>
            <div class="min-w-0 flex-1">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Java Spring</span>
                <p class="text-xs text-[var(--color-muted)] mt-0.5">Spring Security &middot; 8 min</p>
            </div>
            <i class="fas fa-chevron-right text-[var(--color-faint)] text-xs group-hover:text-[var(--color-muted)] transition-colors"></i>
        </a>

        {{-- Python --}}
        <a href="{{ route('docs.python') }}" class="os-card os-card-hover group flex items-center gap-4 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fab fa-python text-lg"></i></div>
            <div class="min-w-0 flex-1">
                <span class="text-sm font-semibold text-[var(--color-ink)]">Python Django</span>
                <p class="text-xs text-[var(--color-muted)] mt-0.5">Django middleware &middot; 7 min</p>
            </div>
            <i class="fas fa-chevron-right text-[var(--color-faint)] text-xs group-hover:text-[var(--color-muted)] transition-colors"></i>
        </a>

        {{-- JavaScript --}}
        <a href="{{ route('docs.javascript') }}" class="os-card os-card-hover group flex items-center gap-4 p-4">
            <div class="os-icon-tile os-icon-tile-ink"><i class="fab fa-js text-lg"></i></div>
            <div class="min-w-0 flex-1">
                <span class="text-sm font-semibold text-[var(--color-ink)]">JavaScript</span>
                <p class="text-xs text-[var(--color-muted)] mt-0.5">Frontend / SPA &middot; 5 min</p>
            </div>
            <i class="fas fa-chevron-right text-[var(--color-faint)] text-xs group-hover:text-[var(--color-muted)] transition-colors"></i>
        </a>
    </div>
</section>

{{-- Quick start —— three numbered steps, clean horizontal layout --}}
<section class="mb-16">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-6">Quick start</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="relative pl-10">
            <span class="absolute left-0 top-0 w-7 h-7 rounded-full bg-[var(--color-ink)] text-white text-xs font-semibold flex items-center justify-center">1</span>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Register your app</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Register in the One System admin panel to receive your client ID and client secret (the secret is shown once).</p>
        </div>
        <div class="relative pl-10">
            <span class="absolute left-0 top-0 w-7 h-7 rounded-full bg-[var(--color-ink)] text-white text-xs font-semibold flex items-center justify-center">2</span>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Install the package</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Install the SDK for your platform via Composer, npm, pip, or NuGet.</p>
        </div>
        <div class="relative pl-10">
            <span class="absolute left-0 top-0 w-7 h-7 rounded-full bg-[var(--color-ink)] text-white text-xs font-semibold flex items-center justify-center">3</span>
            <h3 class="text-sm font-semibold text-[var(--color-ink)] mb-1">Configure &amp; test</h3>
            <p class="text-sm text-[var(--color-muted)] leading-relaxed">Set your environment variables, add the middleware, and verify the SSO flow.</p>
        </div>
    </div>
</section>

{{-- Code example —— compact, well-formatted code block --}}
<section class="mb-16">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-6">Example &mdash; Laravel</h2>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span class="flex items-center gap-2"><i class="fab fa-laravel"></i> routes/web.php</span>
            <button onclick="copyCode()" id="copy-btn" class="hover:text-[var(--color-line-strong)] transition-colors flex items-center gap-1">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
        <pre id="code-block"><code><span class="text-[#94a3b8]">// Install the package</span>
<span class="text-[#a5b4fc]">composer require</span> <span class="text-[#e2e8f0]">one-system/laravel-sso-client</span>

<span class="text-[#94a3b8]">// Protect routes with the SSO middleware</span>
<span class="text-[#a5b4fc]">Route</span>::middleware([<span class="text-[#e2e8f0]">'sso.auth'</span>])->group(function () {
    <span class="text-[#a5b4fc]">Route</span>::get(<span class="text-[#e2e8f0]">'/dashboard'</span>, [DashboardController::class, <span class="text-[#e2e8f0]">'index'</span>]);
    <span class="text-[#a5b4fc]">Route</span>::get(<span class="text-[#e2e8f0]">'/profile'</span>,   [ProfileController::class, <span class="text-[#e2e8f0]">'show'</span>]);
});

<span class="text-[#94a3b8]">// Access the authenticated user</span>
$user = session(<span class="text-[#e2e8f0]">'sso_user'</span>);</code></pre>
    </div>
</section>

{{-- Complete documentation map —— links to all sections --}}
<section class="border-t border-[var(--color-line)] pt-10 mb-16">
    <h2 class="os-eyebrow text-[var(--color-muted)] mb-2">Complete documentation</h2>
    <p class="text-sm text-[var(--color-muted)] mb-6">Navigate to any section of the One System SSO documentation.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('docs') }}" class="os-card os-card-hover group flex items-center gap-3 p-4 border-[var(--color-accent-line)] bg-[var(--color-accent-soft)]">
            <div class="os-icon-tile w-8 h-8 text-xs font-semibold">1</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Overview</span>
                <p class="text-xs text-[var(--color-muted)]">System concept &amp; navigation</p>
            </div>
        </a>
        <a href="{{ route('docs.architecture') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">2</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Architecture</span>
                <p class="text-xs text-[var(--color-muted)]">System design &amp; code structure</p>
            </div>
        </a>
        <a href="{{ route('docs.security') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">3</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Security features</span>
                <p class="text-xs text-[var(--color-muted)]">Implemented security layers</p>
            </div>
        </a>
        <a href="{{ route('docs.quick-start') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">4</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Deploy locally</span>
                <p class="text-xs text-[var(--color-muted)]">Development environment setup</p>
            </div>
        </a>
        <a href="{{ route('docs.deployment') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">5</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Deploy to Linux</span>
                <p class="text-xs text-[var(--color-muted)]">Production deployment guide</p>
            </div>
        </a>
        <a href="{{ route('docs.sdks') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">6</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Integration guides</span>
                <p class="text-xs text-[var(--color-muted)]">SDK packages for all languages</p>
            </div>
        </a>
        <a href="{{ route('docs.admin-panel') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">7</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Admin guide</span>
                <p class="text-xs text-[var(--color-muted)]">Setup, monitoring &amp; management</p>
            </div>
        </a>
        <a href="{{ route('docs.user-guide') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">8</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">User guide</span>
                <p class="text-xs text-[var(--color-muted)]">End-user login &amp; dashboard</p>
            </div>
        </a>
        <a href="{{ route('docs.security-guide') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">9</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Security guide</span>
                <p class="text-xs text-[var(--color-muted)]">App &amp; server hardening</p>
            </div>
        </a>
        <a href="{{ route('docs.troubleshooting') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">10</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Troubleshooting</span>
                <p class="text-xs text-[var(--color-muted)]">Common issues &amp; solutions</p>
            </div>
        </a>
        <a href="{{ route('docs.examples') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8 text-xs font-semibold">11</div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">Code examples</span>
                <p class="text-xs text-[var(--color-muted)]">Copy-paste integration code</p>
            </div>
        </a>
        <a href="{{ route('docs.api.overview') }}" class="os-card os-card-hover group flex items-center gap-3 p-4">
            <div class="os-icon-tile os-icon-tile-ink w-8 h-8"><i class="fas fa-code text-xs"></i></div>
            <div>
                <span class="text-sm font-semibold text-[var(--color-ink)]">API reference</span>
                <p class="text-xs text-[var(--color-muted)]">Endpoint documentation</p>
            </div>
        </a>
    </div>
</section>

<script>
function copyCode() {
    const code = document.getElementById('code-block').textContent;
    navigator.clipboard.writeText(code);
    const btn = document.getElementById('copy-btn');
    btn.innerHTML = '<i class="fas fa-check"></i> Copied';
    setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i> Copy'; }, 2000);
}
</script>
@endsection

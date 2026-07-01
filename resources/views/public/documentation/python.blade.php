@extends('public.documentation.layout')

@section('title', 'Python / Django integration — CAS SSO')
@section('description', 'Integrate Python, Django, Flask, and FastAPI apps with CAS SSO using the cas-system-client package.')

@php
    // Highlight palette tuned to the ONE codeblock surface (dark ink background).
    $kw  = 'color:#c7d2fe';        // keywords / accent-tinted
    $str = 'color:#a5b4fc';        // strings
    $fn  = 'color:#e2e8f0';        // functions / identifiers
    $var = 'color:#cbd5e1';        // variables
    $com = 'color:#64748b';        // comments
    $num = 'color:#93c5fd';        // numbers / literals
@endphp

@section('content')
{{-- Header --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <div class="flex items-center gap-3 mb-4">
            <span class="os-icon-tile os-icon-tile-ink"><i class="fab fa-python text-lg"></i></span>
            <div>
                <p class="os-eyebrow">Integration guide</p>
                <h1 class="text-3xl font-bold text-[var(--color-ink)] tracking-tight leading-tight">Python / Django</h1>
            </div>
        </div>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed mb-5">{{ $pythonGuide['description'] }}</p>
        <div class="flex flex-wrap items-center gap-2">
            <span class="os-badge"><i class="fas fa-clock"></i>5 min setup</span>
            <span class="os-badge"><i class="fas fa-signal"></i>Intermediate</span>
            <span class="os-badge"><i class="fas fa-tag"></i>Python 3.9+</span>
            <span class="os-badge os-badge-accent"><i class="fas fa-cube"></i>cas-system-client 2.0.0</span>
        </div>
    </div>
</section>

{{-- TOC --}}
<nav class="os-card os-card-pad mb-12">
    <h2 class="os-eyebrow mb-3">On this page</h2>
    <ol class="space-y-1.5 text-sm">
        <li><a href="#installation" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. Installation</a></li>
        <li><a href="#settings" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Django settings</a></li>
        <li><a href="#middleware" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. Middleware &amp; callback</a></li>
        <li><a href="#views" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. Protecting views</a></li>
        <li><a href="#flask" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. Flask &amp; manual use</a></li>
        <li><a href="#reference" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">6. Client reference</a></li>
    </ol>
</nav>

{{-- 1. Installation --}}
<section id="installation" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-3">1. Installation</h2>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        Install the <code class="os-code-inline">cas-system-client</code> package from PyPI. The <code class="os-code-inline">requests</code>
        dependency is installed automatically — token validation is server-to-server, so no local JWT library is required.
    </p>
    <div class="os-codeblock mb-4">
        <div class="os-codeblock-head"><span>Terminal</span></div>
        <pre><code>pip install cas-system-client

<span class="text-[var(--color-faint)]"># Django framework helpers</span>
pip install cas-system-client[django]

<span class="text-[var(--color-faint)]"># Flask framework helpers</span>
pip install cas-system-client[flask]</code></pre>
    </div>
    <p class="text-sm text-[var(--color-muted)] leading-relaxed">
        Works with Django, Flask, FastAPI, and any Python framework. Requires Python 3.9 or newer. The optional
        extras pull in Django 4.2+ (<code class="os-code-inline">[django]</code>) and Flask 3.0+
        (<code class="os-code-inline">[flask]</code>); the core client depends only on <code class="os-code-inline">requests&nbsp;&gt;=&nbsp;2.32</code>.
    </p>
</section>

{{-- 2. Django settings --}}
<section id="settings" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-3">2. Django settings</h2>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        Keep credentials in the environment, then build a single <code class="os-code-inline">CasClient</code> instance in
        <code class="os-code-inline">settings.py</code>. The client secret is used only for server-to-server validation and must never
        reach the browser.
    </p>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head"><span>.env</span></div>
        <pre><code><span style="{{ $var }}">CAS_SERVER_URL</span>=<span style="{{ $str }}">https://your-cas-server.com</span>
<span style="{{ $var }}">CAS_CLIENT_ID</span>=<span style="{{ $str }}">your_client_id</span>
<span style="{{ $var }}">CAS_CLIENT_SECRET</span>=<span style="{{ $str }}">your_client_secret</span>
<span style="{{ $var }}">CAS_CALLBACK_URL</span>=<span style="{{ $str }}">https://your-app.com/cas/callback</span></code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>settings.py</span></div>
        <pre><code><span style="{{ $kw }}">import</span> os
<span style="{{ $kw }}">from</span> cas_client <span style="{{ $kw }}">import</span> CasClient

<span style="{{ $com }}"># Single shared client instance, read in middleware and views</span>
CAS_CLIENT = CasClient(
    server_url=os.environ[<span style="{{ $str }}">'CAS_SERVER_URL'</span>],
    client_id=os.environ[<span style="{{ $str }}">'CAS_CLIENT_ID'</span>],
    client_secret=os.environ[<span style="{{ $str }}">'CAS_CLIENT_SECRET'</span>],
    callback_url=os.environ[<span style="{{ $str }}">'CAS_CALLBACK_URL'</span>],
    verify_ssl=<span style="{{ $kw }}">True</span>,            <span style="{{ $com }}"># keep True in production</span>
)

<span style="{{ $com }}"># Paths that require an authenticated CAS session</span>
CAS_PROTECTED_PATHS = [<span style="{{ $str }}">'/dashboard'</span>, <span style="{{ $str }}">'/admin'</span>]
CAS_LOGIN_URL = <span style="{{ $str }}">'/auth/login'</span>

MIDDLEWARE = [
    <span style="{{ $com }}"># ...</span>
    <span style="{{ $str }}">'django.contrib.sessions.middleware.SessionMiddleware'</span>,
    <span style="{{ $str }}">'cas_client.middleware.DjangoCasMiddleware'</span>,
]</code></pre>
    </div>
</section>

{{-- 3. Middleware & callback --}}
<section id="middleware" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-3">3. Middleware &amp; callback</h2>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        <code class="os-code-inline">DjangoCasMiddleware</code> ships with the package — you don't write it yourself. It guards every
        path in <code class="os-code-inline">CAS_PROTECTED_PATHS</code>: if there's no session user it accepts a
        <code class="os-code-inline">Bearer</code> token, validates it once against the CAS server, and stores the result in
        <code class="os-code-inline">request.cas_user</code>. Unauthenticated browser requests are redirected to your
        <code class="os-code-inline">CAS_LOGIN_URL</code>.
    </p>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        Add a callback view that completes the SSO redirect. The CAS server appends the single-use token to your registered
        callback URL as <code class="os-code-inline">?token={JWT}</code>; you exchange it for the user and create the Django session.
    </p>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head"><span>views.py — SSO entry &amp; callback</span></div>
        <pre><code><span style="{{ $kw }}">from</span> django.conf <span style="{{ $kw }}">import</span> settings
<span style="{{ $kw }}">from</span> django.shortcuts <span style="{{ $kw }}">import</span> redirect
<span style="{{ $kw }}">from</span> django.http <span style="{{ $kw }}">import</span> HttpResponseBadRequest


<span style="{{ $kw }}">def</span> <span style="{{ $fn }}">login</span>(request):
    <span style="{{ $com }}"># Send the browser to the CAS server; it redirects back to the</span>
    <span style="{{ $com }}"># client's registered callback_url with ?token=&lt;JWT&gt; appended.</span>
    <span style="{{ $kw }}">return</span> redirect(settings.CAS_CLIENT.get_login_url())


<span style="{{ $kw }}">def</span> <span style="{{ $fn }}">callback</span>(request):
    token = request.GET.get(<span style="{{ $str }}">'token'</span>)
    <span style="{{ $kw }}">if</span> <span style="{{ $kw }}">not</span> token:
        <span style="{{ $kw }}">return</span> HttpResponseBadRequest(<span style="{{ $str }}">'Missing token'</span>)

    <span style="{{ $com }}"># Single-use: validates server-to-server, returns the user dict.</span>
    user = settings.CAS_CLIENT.validate_token(token)
    <span style="{{ $kw }}">if</span> <span style="{{ $kw }}">not</span> user:
        <span style="{{ $kw }}">return</span> HttpResponseBadRequest(<span style="{{ $str }}">'Invalid or expired token'</span>)

    request.session[<span style="{{ $str }}">'cas_user'</span>] = user
    <span style="{{ $kw }}">return</span> redirect(<span style="{{ $str }}">'/dashboard'</span>)</code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>urls.py</span></div>
        <pre><code><span style="{{ $kw }}">from</span> django.urls <span style="{{ $kw }}">import</span> path
<span style="{{ $kw }}">from</span> . <span style="{{ $kw }}">import</span> views

urlpatterns = [
    path(<span style="{{ $str }}">'auth/login'</span>, views.login),
    path(<span style="{{ $str }}">'cas/callback'</span>, views.callback),   <span style="{{ $com }}"># matches CAS_CALLBACK_URL</span>
]</code></pre>
    </div>
</section>

{{-- 4. Protecting views --}}
<section id="views" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-3">4. Protecting views</h2>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        Any view under a protected path can read <code class="os-code-inline">request.cas_user</code>. For role checks, wrap the view
        with <code class="os-code-inline">django_role_required</code> — it returns <code class="os-code-inline">403</code> when the
        user lacks every listed role.
    </p>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head"><span>views.py</span></div>
        <pre><code><span style="{{ $kw }}">from</span> django.http <span style="{{ $kw }}">import</span> JsonResponse
<span style="{{ $kw }}">from</span> cas_client.middleware <span style="{{ $kw }}">import</span> django_role_required


<span style="{{ $kw }}">def</span> <span style="{{ $fn }}">dashboard</span>(request):
    <span style="{{ $kw }}">return</span> JsonResponse({<span style="{{ $str }}">'user'</span>: request.cas_user})


<span style="{{ $fn }}">@django_role_required</span>(<span style="{{ $str }}">'admin'</span>, <span style="{{ $str }}">'manager'</span>)
<span style="{{ $kw }}">def</span> <span style="{{ $fn }}">admin_panel</span>(request):
    user = request.cas_user
    <span style="{{ $kw }}">return</span> JsonResponse({<span style="{{ $str }}">'username'</span>: user[<span style="{{ $str }}">'username'</span>]})</code></pre>
    </div>
    <div class="os-alert os-alert-success">
        <i class="fas fa-check-circle mt-0.5"></i>
        <div>
            <strong>Done.</strong> Protected views read the authenticated user from
            <code class="os-code-inline">request.cas_user</code> — a dict containing <code class="os-code-inline">id</code>,
            <code class="os-code-inline">username</code>, and <code class="os-code-inline">email</code> (plus
            <code class="os-code-inline">roles</code> when the CAS server returns them).
        </div>
    </div>
</section>

{{-- 5. Flask & manual --}}
<section id="flask" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-3">5. Flask &amp; manual use</h2>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        The same client powers Flask through the <code class="os-code-inline">flask_cas_required</code> and
        <code class="os-code-inline">flask_role_required</code> decorators. The authenticated user is available on
        <code class="os-code-inline">flask.g.cas_user</code>.
    </p>
    <div class="os-codeblock mb-6">
        <div class="os-codeblock-head"><span>app.py — Flask</span></div>
        <pre><code><span style="{{ $kw }}">import</span> flask
<span style="{{ $kw }}">from</span> cas_client <span style="{{ $kw }}">import</span> CasClient
<span style="{{ $kw }}">from</span> cas_client.middleware <span style="{{ $kw }}">import</span> flask_cas_required, flask_role_required

cas = CasClient(
    server_url=<span style="{{ $str }}">'https://your-cas-server.com'</span>,
    client_id=<span style="{{ $str }}">'your_client_id'</span>,
    client_secret=<span style="{{ $str }}">'your_client_secret'</span>,
)


<span style="{{ $fn }}">@app.route</span>(<span style="{{ $str }}">'/dashboard'</span>)
<span style="{{ $fn }}">@flask_cas_required</span>(cas)
<span style="{{ $kw }}">def</span> <span style="{{ $fn }}">dashboard</span>():
    user = flask.g.cas_user
    <span style="{{ $kw }}">return</span> <span style="{{ $str }}">f'Hello {user["username"]}'</span>


<span style="{{ $fn }}">@app.route</span>(<span style="{{ $str }}">'/admin'</span>)
<span style="{{ $fn }}">@flask_cas_required</span>(cas)
<span style="{{ $fn }}">@flask_role_required</span>(cas, <span style="{{ $str }}">'admin'</span>)
<span style="{{ $kw }}">def</span> <span style="{{ $fn }}">admin</span>():
    <span style="{{ $kw }}">return</span> <span style="{{ $str }}">'Admin area'</span></code></pre>
    </div>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        For service-to-service flows you can drive the client directly. <code class="os-code-inline">generate_sso_token</code> calls the
        IP-whitelisted <code class="os-code-inline">/api/sso/token</code> endpoint and returns a dict with
        <code class="os-code-inline">token</code> and <code class="os-code-inline">redirect_url</code>.
    </p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Manual authentication</span></div>
        <pre><code><span style="{{ $com }}"># Issue a token for a known user (service-to-service)</span>
result = cas.generate_sso_token(<span style="{{ $str }}">'john_doe'</span>)
redirect_url = result[<span style="{{ $str }}">'redirect_url'</span>]

<span style="{{ $com }}"># Validate a token (single-use)</span>
user = cas.validate_token(token)

<span style="{{ $com }}"># Role checks against the user dict</span>
<span style="{{ $kw }}">if</span> cas.user_has_role(user, <span style="{{ $str }}">'admin'</span>):
    ...

<span style="{{ $com }}"># Invalidate the cached token and end the CAS session</span>
cas.logout(token)</code></pre>
    </div>
</section>

{{-- 6. Reference --}}
<section id="reference" class="mb-12">
    <h2 class="text-xl font-semibold text-[var(--color-ink)] mb-3">6. Client reference</h2>
    <p class="text-sm text-[var(--color-ink-2)] leading-relaxed mb-4">
        Methods exposed by <code class="os-code-inline">cas_client.CasClient</code>.
    </p>
    <div class="os-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--color-line)] bg-[var(--color-surface-2)]">
                    <th class="text-left font-medium text-[var(--color-ink-2)] px-4 py-2.5">Method</th>
                    <th class="text-left font-medium text-[var(--color-ink-2)] px-4 py-2.5">Description</th>
                </tr>
            </thead>
            <tbody class="text-[var(--color-muted)]">
                <tr class="border-b border-[var(--color-line)]">
                    <td class="px-4 py-2.5"><code class="os-code-inline">get_login_url(return_url=None)</code></td>
                    <td class="px-4 py-2.5">Build the CAS SSO login URL for the browser redirect.</td>
                </tr>
                <tr class="border-b border-[var(--color-line)]">
                    <td class="px-4 py-2.5"><code class="os-code-inline">generate_sso_token(username)</code></td>
                    <td class="px-4 py-2.5">Issue a token via client credentials (service-to-service).</td>
                </tr>
                <tr class="border-b border-[var(--color-line)]">
                    <td class="px-4 py-2.5"><code class="os-code-inline">validate_token(token)</code></td>
                    <td class="px-4 py-2.5">Validate a single-use token; returns the user dict or <code class="os-code-inline">None</code>.</td>
                </tr>
                <tr class="border-b border-[var(--color-line)]">
                    <td class="px-4 py-2.5"><code class="os-code-inline">get_user_from_token(token)</code></td>
                    <td class="px-4 py-2.5">Read a previously validated user from the in-memory cache.</td>
                </tr>
                <tr class="border-b border-[var(--color-line)]">
                    <td class="px-4 py-2.5"><code class="os-code-inline">logout(token=None)</code></td>
                    <td class="px-4 py-2.5">Clear the cached token and end the CAS session.</td>
                </tr>
                <tr class="border-b border-[var(--color-line)]">
                    <td class="px-4 py-2.5"><code class="os-code-inline">user_has_role(user, role)</code></td>
                    <td class="px-4 py-2.5">Check a single role on the user dict.</td>
                </tr>
                <tr>
                    <td class="px-4 py-2.5"><code class="os-code-inline">user_has_any_role / user_has_all_roles</code></td>
                    <td class="px-4 py-2.5">Check whether the user has any or all of the given roles.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="os-alert os-alert-warning mt-6">
        <i class="fas fa-triangle-exclamation mt-0.5"></i>
        <div>
            Keep <code class="os-code-inline">CAS_CLIENT_SECRET</code> server-side only and serve your app over HTTPS in production.
            Tokens are single-use — validate once, then rely on the Django/Flask session.
        </div>
    </div>
</section>

{{-- Next steps --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="os-eyebrow mb-4">Next steps</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('docs.api.overview') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <i class="fas fa-code text-[var(--color-muted)] text-sm"></i>
            <span class="text-sm font-medium text-[var(--color-ink-2)]">API reference</span>
        </a>
        <a href="{{ route('docs.security') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <i class="fas fa-shield-halved text-[var(--color-muted)] text-sm"></i>
            <span class="text-sm font-medium text-[var(--color-ink-2)]">Security guide</span>
        </a>
        <a href="{{ route('docs.deployment') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <i class="fas fa-server text-[var(--color-muted)] text-sm"></i>
            <span class="text-sm font-medium text-[var(--color-ink-2)]">Deployment</span>
        </a>
    </div>
</section>
@endsection

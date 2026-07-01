@extends('public.documentation.layout')

@section('title', 'Code examples — One System SSO')
@section('description', 'Ready-to-use integration examples for One System single sign-on: the browser login flow, server-to-server token validation, and service-to-service token issuance.')

@section('content')
<div class="os-container !px-0 !max-w-none">

{{-- Hero --}}
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <p class="os-eyebrow mb-3">Getting started</p>
    <h1 class="text-4xl font-semibold text-[var(--color-ink)] tracking-tight leading-tight mb-4">Code examples</h1>
    <p class="text-lg text-[var(--color-muted)] leading-relaxed">Copy-and-adapt examples for the One System flow: send the user to <code class="os-code-inline">/sso/login</code>, receive a single-use token at your callback, then validate it server to server with your client secret.</p>
</section>

{{-- Platform links --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Integration guides</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('docs.laravel') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <span class="os-icon-tile os-icon-tile-ink"><i class="fab fa-laravel"></i></span>
            <span><span class="block text-sm font-semibold text-[var(--color-ink)]">Laravel</span><span class="block text-xs text-[var(--color-muted)]">Composer &middot; 2 min</span></span>
        </a>
        <a href="{{ route('docs.dotnet') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <span class="os-icon-tile os-icon-tile-ink"><i class="fab fa-microsoft"></i></span>
            <span><span class="block text-sm font-semibold text-[var(--color-ink)]">.NET MVC</span><span class="block text-xs text-[var(--color-muted)]">NuGet &middot; 10 min</span></span>
        </a>
        <a href="{{ route('docs.nodejs') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <span class="os-icon-tile os-icon-tile-ink"><i class="fab fa-node-js"></i></span>
            <span><span class="block text-sm font-semibold text-[var(--color-ink)]">Node.js</span><span class="block text-xs text-[var(--color-muted)]">npm &middot; 3 min</span></span>
        </a>
        <a href="{{ route('docs.java') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <span class="os-icon-tile os-icon-tile-ink"><i class="fab fa-java"></i></span>
            <span><span class="block text-sm font-semibold text-[var(--color-ink)]">Java Spring</span><span class="block text-xs text-[var(--color-muted)]">Maven &middot; 8 min</span></span>
        </a>
        <a href="{{ route('docs.python') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <span class="os-icon-tile os-icon-tile-ink"><i class="fab fa-python"></i></span>
            <span><span class="block text-sm font-semibold text-[var(--color-ink)]">Python Django</span><span class="block text-xs text-[var(--color-muted)]">pip &middot; 7 min</span></span>
        </a>
        <a href="{{ route('docs.javascript') }}" class="os-card os-card-hover flex items-center gap-3 p-4">
            <span class="os-icon-tile os-icon-tile-ink"><i class="fab fa-js"></i></span>
            <span><span class="block text-sm font-semibold text-[var(--color-ink)]">JavaScript</span><span class="block text-xs text-[var(--color-muted)]">npm &middot; 5 min</span></span>
        </a>
    </div>
</section>

{{-- Flow overview --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">The flow in three steps</h2>
    <div class="os-card os-card-pad">
        <ol class="space-y-3 text-sm text-[var(--color-ink-2)]">
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent flex-shrink-0">1</span><span>Redirect the browser to <code class="os-code-inline">GET {CAS_BASE}/sso/login?client_id={CLIENT_ID}</code>. One System authenticates the user.</span></li>
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent flex-shrink-0">2</span><span>The browser is redirected back to your registered <code class="os-code-inline">callback_url</code> with <code class="os-code-inline">?token={JWT}</code> appended.</span></li>
            <li class="flex items-start gap-3"><span class="os-badge os-badge-accent flex-shrink-0">3</span><span>Your server validates the token once via <code class="os-code-inline">POST {CAS_BASE}/api/validate-token</code> with the client secret, then creates its own session.</span></li>
        </ol>
    </div>
</section>

{{-- Quick start cURL --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Quick start — cURL</h2>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>terminal</span>
            <span>Bash</span>
        </div>
        <pre><code># 1. Send the user's browser here to sign in (returns a redirect to your callback)
#    GET https://your-one-system.com/sso/login?client_id=your_client_id
#    -> 302 https://your-app.com/cas/callback?token=eyJhbGci...

# 2. At your callback, validate the token server-to-server (never in the browser)
curl -X POST https://your-one-system.com/api/validate-token \
  -H "Content-Type: application/json" \
  -d '{
    "token": "eyJhbGci...",
    "client_id": "your_client_id",
    "client_secret": "your_client_secret"
  }'</code></pre>
    </div>
</section>

{{-- JavaScript / Node server-side --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Node.js — validating at the callback</h2>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>callback.js · server-side</span>
            <span>JS</span>
        </div>
        <pre><code>// Express route registered as your client's callback_url.
// The client_secret stays on the server and is never exposed to the browser.
app.get('/cas/callback', async (req, res) =&gt; {
  const { token } = req.query;

  const result = await fetch('https://your-one-system.com/api/validate-token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      token,
      client_id: process.env.ONE_SYSTEM_CLIENT_ID,
      client_secret: process.env.ONE_SYSTEM_CLIENT_SECRET,
    }),
  });

  const data = await result.json(); // { valid, user, expires_at }
  if (result.status === 200 &amp;&amp; data.valid) {
    req.session.user = data.user; // create your app's own session
    return res.redirect('/');
  }
  return res.status(401).send('Invalid or expired token');
});</code></pre>
    </div>
</section>

{{-- PHP --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">PHP — validating at the callback</h2>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>callback.php · server-side</span>
            <span>PHP</span>
        </div>
        <pre><code>&lt;?php
// Read the single-use token from the callback query string.
$token = $_GET['token'] ?? '';

$ch = curl_init('https://your-one-system.com/api/validate-token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_POST           =&gt; true,
    CURLOPT_HTTPHEADER     =&gt; ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     =&gt; json_encode([
        'token'         =&gt; $token,
        'client_id'     =&gt; getenv('ONE_SYSTEM_CLIENT_ID'),
        'client_secret' =&gt; getenv('ONE_SYSTEM_CLIENT_SECRET'),
    ]),
]);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

if (($response['valid'] ?? false) === true) {
    $_SESSION['user'] = $response['user']; // { id, username, email }
    header('Location: /');
}</code></pre>
    </div>
</section>

{{-- Python --}}
<section class="mb-12">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Python — validating at the callback</h2>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>callback.py · server-side</span>
            <span>Python</span>
        </div>
        <pre><code>import os
import requests

def validate_token(token):
    response = requests.post(
        'https://your-one-system.com/api/validate-token',
        json={
            'token': token,
            'client_id': os.environ['ONE_SYSTEM_CLIENT_ID'],
            'client_secret': os.environ['ONE_SYSTEM_CLIENT_SECRET'],
        },
    )
    data = response.json()  # { valid, user, expires_at }
    if response.status_code == 200 and data.get('valid'):
        return data['user']  # create your app's own session from this
    return None</code></pre>
    </div>
</section>

{{-- Service-to-service issuance --}}
<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Service-to-service token issuance</h2>
    <p class="text-sm text-[var(--color-muted)] mb-4">From a trusted, IP-whitelisted backend you can issue a token for a known user without a browser round trip. This returns a <code class="os-code-inline">redirect_url</code> you can hand to the user to log them straight in.</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head">
            <span>terminal</span>
            <span>Bash</span>
        </div>
        <pre><code>curl -X POST https://your-one-system.com/api/sso/token \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": "your_client_id",
    "client_secret": "your_client_secret",
    "username": "john_doe"
  }'

# -> { "redirect_url": "https://your-app.com/cas/callback?token=eyJhbG...", "token": "eyJhbG..." }</code></pre>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info text-[var(--color-accent)] mt-0.5"></i>
        <div>Keep <code class="os-code-inline">client_secret</code> on the server only. Store secrets such as <code class="os-code-inline">ONE_SYSTEM_CLIENT_SECRET</code> and <code class="os-code-inline">JWT_SECRET</code> in environment variables, and serve every request over HTTPS in production.</div>
    </div>
</section>

</div>
@endsection

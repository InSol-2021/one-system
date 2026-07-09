@extends('public.documentation.layout')

@section('title', 'Rust integration — CAS SSO')
@section('description', 'Integrate Rust applications (axum, actix-web, or any stack) with CAS Single Sign-On using the rust-cas-client crate.')

@section('content')
@php
    // Highlight palette tuned to the ONE codeblock surface (dark ink background).
    $kw  = 'color:#c7d2fe';        // keywords / accent-tinted
    $str = 'color:#a5b4fc';        // strings
    $fn  = 'color:#e2e8f0';        // functions / identifiers
    $var = 'color:#cbd5e1';        // variables
    $com = 'color:#64748b';        // comments
    $num = 'color:#93c5fd';        // numbers / literals
@endphp
<section class="border-b border-[var(--color-line)] pb-10 mb-12">
    <div class="">
        <div class="flex items-center gap-3 mb-4">
            <span class="os-icon-tile os-icon-tile-ink">
                <i class="fab fa-rust"></i>
            </span>
            <div>
                <p class="os-eyebrow">Integration guide</p>
                <h1 class="text-3xl font-bold text-[var(--color-ink)] tracking-tight leading-tight">Rust</h1>
            </div>
        </div>
        <p class="text-lg text-[var(--color-muted)] leading-relaxed mb-5">{{ $rustGuide['description'] }}</p>
        <div class="flex flex-wrap gap-2">
            <span class="os-badge"><i class="fas fa-clock"></i>5 min setup</span>
            <span class="os-badge"><i class="fas fa-signal"></i>Beginner</span>
            <span class="os-badge"><i class="fas fa-tag"></i>edition 2021</span>
            <span class="os-badge os-badge-accent"><i class="fas fa-cube"></i>v1.0.0</span>
        </div>
    </div>
</section>

<nav class="mb-12 os-card os-card-pad">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-3">On this page</h2>
    <ol class="space-y-1.5 text-sm">
        <li><a href="#overview" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">1. Overview</a></li>
        <li><a href="#installation" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">2. Add the crate</a></li>
        <li><a href="#configuration" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">3. Configuration</a></li>
        <li><a href="#client" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">4. Build the client</a></li>
        <li><a href="#flow" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">5. Login, callback &amp; logout</a></li>
        <li><a href="#example" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">6. Complete axum example</a></li>
        <li><a href="#api" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">7. API reference</a></li>
    </ol>
</nav>

<section id="overview" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">1. Overview</h2>
    <p class="text-[var(--color-muted)] mb-4">The <code class="os-code-inline">rust-cas-client</code> crate is a small, framework-agnostic client for the One System CAS SSO protocol. It does exactly two things, mirroring the other language SDKs in this repo:</p>
    <ul class="list-disc pl-6 space-y-2 text-[var(--color-muted)] mb-4">
        <li><strong class="text-[var(--color-ink-2)]">Build the browser login redirect</strong> — <code class="os-code-inline">login_url()</code> returns <code class="os-code-inline">{CAS_BASE}/sso/login?client_id=ID</code>. The CAS server authenticates the user and redirects back to your client's <em>registered</em> callback URL with <code class="os-code-inline">?token=&lt;JWT&gt;</code> appended.</li>
        <li><strong class="text-[var(--color-ink-2)]">Validate the single-use token server-to-server</strong> — <code class="os-code-inline">validate_token()</code> POSTs <code class="os-code-inline">{token, client_id, client_secret}</code> to <code class="os-code-inline">{CAS_BASE}/api/validate-token</code> and parses the returned user.</li>
    </ul>
    <p class="text-[var(--color-muted)] mb-4">The crate is deliberately runtime- and framework-agnostic: it exposes one async method backed by <code class="os-code-inline">reqwest</code> plus plain data types, so it drops into axum, actix-web, or any other stack. The examples below use axum.</p>
    <div class="os-alert mt-4">
        <i class="fas fa-shield-halved mt-0.5 text-[var(--color-muted)]"></i>
        <div>The <code class="os-code-inline">client_secret</code> is only ever used here (server-side) and is never exposed to the browser. The JWT is single-use, so validate it exactly once and then establish your own application session.</div>
    </div>
</section>

<section id="installation" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">2. Add the crate</h2>
    <p class="text-[var(--color-muted)] mb-4">Add <code class="os-code-inline">rust-cas-client</code> from <a href="https://crates.io/crates/rust-cas-client" class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">crates.io</a> as a normal registry dependency &mdash; either edit <code class="os-code-inline">Cargo.toml</code> directly or run <code class="os-code-inline">cargo add rust-cas-client</code>. Its only runtime dependencies are <code class="os-code-inline">reqwest</code>, <code class="os-code-inline">serde</code>, <code class="os-code-inline">serde_json</code>, <code class="os-code-inline">url</code>, and <code class="os-code-inline">thiserror</code>.</p>
    <div class="os-codeblock mb-3">
        <div class="os-codeblock-head"><span>Install</span></div>
        <pre><code><span style="{{ $com }}"># or add the line below to Cargo.toml manually</span>
<span style="{{ $fn }}">cargo</span> add rust-cas-client</code></pre>
    </div>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>Cargo.toml</span></div>
        <pre><code><span style="{{ $com }}"># The CAS client crate, resolved from the public crates.io registry.</span>
[dependencies]
<span style="{{ $var }}">rust-cas-client</span> = <span style="{{ $str }}">"1.0"</span>

<span style="{{ $com }}"># A web framework + async runtime to drive the flow (axum shown here).</span>
<span style="{{ $var }}">axum</span> = { version = <span style="{{ $str }}">"0.7"</span> }
<span style="{{ $var }}">tokio</span> = { version = <span style="{{ $str }}">"1"</span>, features = [<span style="{{ $str }}">"full"</span>] }
<span style="{{ $var }}">tower-sessions</span> = <span style="{{ $str }}">"0.13"</span>
<span style="{{ $var }}">serde_json</span> = <span style="{{ $str }}">"1"</span></code></pre>
    </div>
    <p class="text-[var(--color-muted)] mt-4">Because <code class="os-code-inline">validate_token</code> is <code class="os-code-inline">async</code>, the crate needs an async runtime. Any executor works; the examples assume Tokio.</p>
</section>

<section id="configuration" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">3. Configuration</h2>
    <p class="text-[var(--color-muted)] mb-4">All four values come from the host application's environment. <code class="os-code-inline">CAS_BASE_URL</code> is the CAS origin used for the <strong>server-side</strong> validation call; <code class="os-code-inline">CAS_CALLBACK_URL</code> is the public, browser-facing URL registered for your client (<code class="os-code-inline">CAS_SERVER_URL</code> is accepted as an alias of <code class="os-code-inline">CAS_BASE_URL</code> for parity with the other SDKs).</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>.env</span></div>
        <pre><code><span style="{{ $com }}"># CAS origin for the SERVER-TO-SERVER validation call (no trailing slash).</span>
<span style="{{ $var }}">CAS_BASE_URL</span>=<span style="{{ $str }}">https://your-cas-server.com</span>
<span style="{{ $var }}">CAS_CLIENT_ID</span>=<span style="{{ $str }}">your_client_id</span>
<span style="{{ $var }}">CAS_CLIENT_SECRET</span>=<span style="{{ $str }}">your_client_secret</span>
<span style="{{ $com }}"># Must EXACTLY match the callback registered for this client on the CAS server.</span>
<span style="{{ $var }}">CAS_CALLBACK_URL</span>=<span style="{{ $str }}">https://your-app.com/cas/callback</span></code></pre>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info mt-0.5 text-[var(--color-muted)]"></i>
        <div>In the Docker deployment, <code class="os-code-inline">CAS_BASE_URL</code> is the internal host (e.g. <code class="os-code-inline">http://one-system-cas</code>) reachable server-to-server, while <code class="os-code-inline">CAS_CALLBACK_URL</code> is the public host the browser is redirected back to. The secret is server-side only — never ship it to the browser.</div>
    </div>
</section>

<section id="client" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">4. Build the client</h2>
    <p class="text-[var(--color-muted)] mb-4">Construct a <code class="os-code-inline">CasClient</code> from a <code class="os-code-inline">CasConfig</code>. <code class="os-code-inline">CasClient::new</code> returns <code class="os-code-inline">Err(CasError::Config)</code> if <code class="os-code-inline">server_url</code>, <code class="os-code-inline">client_id</code>, or <code class="os-code-inline">client_secret</code> is empty, and normalizes a trailing slash on the base URL. The client is <code class="os-code-inline">Clone</code>, so build it once and share it across handlers.</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>main.rs</span></div>
        <pre><code><span style="{{ $kw }}">use</span> rust_cas_client::{CasClient, CasConfig};

<span style="{{ $kw }}">let</span> cas = CasClient::<span style="{{ $fn }}">new</span>(CasConfig {
    server_url:    std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_BASE_URL"</span>).<span style="{{ $fn }}">unwrap</span>(),       <span style="{{ $com }}">// server-side origin</span>
    client_id:     std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_CLIENT_ID"</span>).<span style="{{ $fn }}">unwrap</span>(),      <span style="{{ $com }}">// e.g. "rust-sample"</span>
    client_secret: std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_CLIENT_SECRET"</span>).<span style="{{ $fn }}">unwrap</span>(),
    callback_url:  std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_CALLBACK_URL"</span>).<span style="{{ $fn }}">unwrap</span>(),   <span style="{{ $com }}">// browser-facing</span>
})
.<span style="{{ $fn }}">expect</span>(<span style="{{ $str }}">"CAS client config is invalid (check CAS_* env vars)"</span>);

<span style="{{ $com }}">// Handy config accessors:</span>
<span style="{{ $kw }}">let</span> _ = cas.<span style="{{ $fn }}">client_id</span>();      <span style="{{ $com }}">// &str</span>
<span style="{{ $kw }}">let</span> _ = cas.<span style="{{ $fn }}">callback_url</span>();   <span style="{{ $com }}">// &str</span></code></pre>
    </div>
</section>

<section id="flow" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">5. Login, callback &amp; logout</h2>
    <p class="text-[var(--color-muted)] mb-4">Redirect the browser to the login URL. CAS authenticates the user and redirects back to your registered callback with a single-use <code class="os-code-inline">token</code> query parameter. Validate it server-to-server, then create your own session. <code class="os-code-inline">validate_token</code> returns <code class="os-code-inline">Ok(Some(CasUser))</code> on success, <code class="os-code-inline">Ok(None)</code> when the token is rejected (invalid, expired, or already used), and <code class="os-code-inline">Err(CasError::Http)</code> only on a transport failure.</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>auth.rs</span></div>
        <pre><code><span style="{{ $com }}">// 1) Send the user to CAS to sign in.</span>
<span style="{{ $com }}">//    login_url() -&gt; {CAS_BASE}/sso/login?client_id=ID</span>
<span style="{{ $kw }}">let</span> url = cas.<span style="{{ $fn }}">login_url</span>()?;   <span style="{{ $com }}">// redirect the browser here</span>

<span style="{{ $com }}">// 2) At the callback (?token=...), validate exactly once, server-to-server.</span>
<span style="{{ $kw }}">match</span> cas.<span style="{{ $fn }}">validate_token</span>(&amp;token).<span style="{{ $kw }}">await</span>? {
    <span style="{{ $fn }}">Some</span>(user) =&gt; {
        <span style="{{ $com }}">// user.id / user.username / user.email / user.roles</span>
        <span style="{{ $com }}">// create YOUR OWN application session for `user` here</span>
    }
    <span style="{{ $fn }}">None</span> =&gt; {
        <span style="{{ $com }}">// token was invalid, expired, or already used -&gt; 401</span>
    }
}

<span style="{{ $com }}">// 3) On logout, best-effort notify CAS, then clear your own session.</span>
<span style="{{ $kw }}">let</span> _ = cas.<span style="{{ $fn }}">logout</span>().<span style="{{ $kw }}">await</span>;   <span style="{{ $com }}">// advisory; returns Ok(false) on error</span></code></pre>
    </div>
    <div class="os-alert mt-4">
        <i class="fas fa-circle-info mt-0.5 text-[var(--color-muted)]"></i>
        <div>The token is single-use. <code class="os-code-inline">validate_token()</code> POSTs to <code class="os-code-inline">/api/validate-token</code> with your client credentials and an <code class="os-code-inline">X-Client-ID</code> header, then returns the parsed <code class="os-code-inline">CasUser</code> on <code class="os-code-inline">200 { valid: true, user, expires_at }</code> — or <code class="os-code-inline">None</code> otherwise.</div>
    </div>
</section>

<section id="example" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">6. Complete axum example</h2>
    <p class="text-[var(--color-muted)] mb-4">A minimal but complete app: it builds the login URL, handles the callback (validating the single-use token), stores the user in a <code class="os-code-inline">tower-sessions</code> session, protects a route, and logs out. Both auth paths land in the same session, so the rest of the app does not care how the user signed in.</p>
    <div class="os-codeblock">
        <div class="os-codeblock-head"><span>main.rs</span></div>
        <pre><code><span style="{{ $kw }}">use</span> std::collections::HashMap;

<span style="{{ $kw }}">use</span> axum::{
    extract::{Query, State},
    http::StatusCode,
    response::{Html, IntoResponse, Redirect, Response},
    routing::{get, post},
    Router,
};
<span style="{{ $kw }}">use</span> tower_sessions::{MemoryStore, Session, SessionManagerLayer};
<span style="{{ $kw }}">use</span> rust_cas_client::{CasClient, CasConfig};

<span style="{{ $kw }}">const</span> SESSION_USER: &amp;<span style="{{ $kw }}">str</span> = <span style="{{ $str }}">"user"</span>;

<span style="{{ $com }}">// The client is Clone, so it can live in axum's shared state.</span>
#[derive(Clone)]
<span style="{{ $kw }}">struct</span> AppState {
    cas: CasClient,
}

#[tokio::main]
<span style="{{ $kw }}">async fn</span> <span style="{{ $fn }}">main</span>() {
    <span style="{{ $kw }}">let</span> cas = CasClient::<span style="{{ $fn }}">new</span>(CasConfig {
        server_url:    std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_BASE_URL"</span>).<span style="{{ $fn }}">unwrap</span>(),
        client_id:     std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_CLIENT_ID"</span>).<span style="{{ $fn }}">unwrap</span>(),
        client_secret: std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_CLIENT_SECRET"</span>).<span style="{{ $fn }}">unwrap</span>(),
        callback_url:  std::env::<span style="{{ $fn }}">var</span>(<span style="{{ $str }}">"CAS_CALLBACK_URL"</span>).<span style="{{ $fn }}">unwrap</span>(),
    })
    .<span style="{{ $fn }}">expect</span>(<span style="{{ $str }}">"invalid CAS config"</span>);

    <span style="{{ $kw }}">let</span> session_layer = SessionManagerLayer::<span style="{{ $fn }}">new</span>(MemoryStore::<span style="{{ $fn }}">default</span>());

    <span style="{{ $kw }}">let</span> app = Router::<span style="{{ $fn }}">new</span>()
        .<span style="{{ $fn }}">route</span>(<span style="{{ $str }}">"/auth/login"</span>, <span style="{{ $fn }}">get</span>(cas_login))
        .<span style="{{ $fn }}">route</span>(<span style="{{ $str }}">"/cas/callback"</span>, <span style="{{ $fn }}">get</span>(cas_callback))
        .<span style="{{ $fn }}">route</span>(<span style="{{ $str }}">"/dashboard"</span>, <span style="{{ $fn }}">get</span>(dashboard))
        .<span style="{{ $fn }}">route</span>(<span style="{{ $str }}">"/logout"</span>, <span style="{{ $fn }}">post</span>(logout))
        .<span style="{{ $fn }}">with_state</span>(AppState { cas })
        .<span style="{{ $fn }}">layer</span>(session_layer);

    <span style="{{ $kw }}">let</span> listener = tokio::net::TcpListener::<span style="{{ $fn }}">bind</span>(<span style="{{ $str }}">"0.0.0.0:9111"</span>)
        .<span style="{{ $kw }}">await</span>
        .<span style="{{ $fn }}">unwrap</span>();
    axum::<span style="{{ $fn }}">serve</span>(listener, app).<span style="{{ $kw }}">await</span>.<span style="{{ $fn }}">unwrap</span>();
}

<span style="{{ $com }}">// 1) Start SSO: redirect the browser to the CAS login URL.</span>
<span style="{{ $kw }}">async fn</span> <span style="{{ $fn }}">cas_login</span>(State(state): State&lt;AppState&gt;) -&gt; Response {
    <span style="{{ $kw }}">match</span> state.cas.<span style="{{ $fn }}">login_url</span>() {
        <span style="{{ $fn }}">Ok</span>(url) =&gt; Redirect::<span style="{{ $fn }}">to</span>(&amp;url).<span style="{{ $fn }}">into_response</span>(),
        <span style="{{ $fn }}">Err</span>(e) =&gt; (StatusCode::INTERNAL_SERVER_ERROR, e.<span style="{{ $fn }}">to_string</span>()).<span style="{{ $fn }}">into_response</span>(),
    }
}

<span style="{{ $com }}">// 2) CAS redirects back here with ?token=&lt;JWT&gt;. Validate it ONCE.</span>
<span style="{{ $kw }}">async fn</span> <span style="{{ $fn }}">cas_callback</span>(
    State(state): State&lt;AppState&gt;,
    session: Session,
    Query(params): Query&lt;HashMap&lt;String, String&gt;&gt;,
) -&gt; Response {
    <span style="{{ $kw }}">let</span> <span style="{{ $fn }}">Some</span>(token) = params.<span style="{{ $fn }}">get</span>(<span style="{{ $str }}">"token"</span>) <span style="{{ $kw }}">else</span> {
        <span style="{{ $kw }}">return</span> (StatusCode::BAD_REQUEST, <span style="{{ $str }}">"missing token"</span>).<span style="{{ $fn }}">into_response</span>();
    };

    <span style="{{ $kw }}">match</span> state.cas.<span style="{{ $fn }}">validate_token</span>(token).<span style="{{ $kw }}">await</span> {
        <span style="{{ $fn }}">Ok</span>(<span style="{{ $fn }}">Some</span>(user)) =&gt; {
            <span style="{{ $com }}">// Create OUR OWN session for the validated user.</span>
            <span style="{{ $kw }}">let</span> _ = session.<span style="{{ $fn }}">insert</span>(SESSION_USER, user.username.<span style="{{ $fn }}">clone</span>()).<span style="{{ $kw }}">await</span>;
            Redirect::<span style="{{ $fn }}">to</span>(<span style="{{ $str }}">"/dashboard"</span>).<span style="{{ $fn }}">into_response</span>()
        }
        <span style="{{ $fn }}">Ok</span>(<span style="{{ $fn }}">None</span>) =&gt; (
            StatusCode::UNAUTHORIZED,
            <span style="{{ $str }}">"CAS rejected the token (invalid, expired, or already used)."</span>,
        )
            .<span style="{{ $fn }}">into_response</span>(),
        <span style="{{ $fn }}">Err</span>(e) =&gt; (StatusCode::BAD_GATEWAY, e.<span style="{{ $fn }}">to_string</span>()).<span style="{{ $fn }}">into_response</span>(),
    }
}

<span style="{{ $com }}">// 3) Protected page — only a signed-in session reaches it.</span>
<span style="{{ $kw }}">async fn</span> <span style="{{ $fn }}">dashboard</span>(session: Session) -&gt; Response {
    <span style="{{ $kw }}">let</span> user: <span style="{{ $fn }}">Option</span>&lt;String&gt; = session.<span style="{{ $fn }}">get</span>(SESSION_USER).<span style="{{ $kw }}">await</span>.<span style="{{ $fn }}">unwrap_or</span>(<span style="{{ $fn }}">None</span>);
    <span style="{{ $kw }}">match</span> user {
        <span style="{{ $fn }}">Some</span>(name) =&gt; Html(<span style="{{ $fn }}">format!</span>(<span style="{{ $str }}">"&lt;h1&gt;Welcome, {name}&lt;/h1&gt;"</span>)).<span style="{{ $fn }}">into_response</span>(),
        <span style="{{ $fn }}">None</span> =&gt; Redirect::<span style="{{ $fn }}">to</span>(<span style="{{ $str }}">"/auth/login"</span>).<span style="{{ $fn }}">into_response</span>(),
    }
}

<span style="{{ $com }}">// 4) Logout: best-effort CAS notify, then clear our own session.</span>
<span style="{{ $kw }}">async fn</span> <span style="{{ $fn }}">logout</span>(State(state): State&lt;AppState&gt;, session: Session) -&gt; Response {
    <span style="{{ $kw }}">let</span> _ = state.cas.<span style="{{ $fn }}">logout</span>().<span style="{{ $kw }}">await</span>;
    <span style="{{ $kw }}">let</span> _ = session.<span style="{{ $fn }}">flush</span>().<span style="{{ $kw }}">await</span>;
    Redirect::<span style="{{ $fn }}">to</span>(<span style="{{ $str }}">"/"</span>).<span style="{{ $fn }}">into_response</span>()
}</code></pre>
    </div>
    <div class="os-alert os-alert-success mt-4">
        <i class="fas fa-circle-check mt-0.5"></i>
        <div><strong>Done.</strong> A full runnable version of this app — with local username/password accounts layered on top — lives in <code class="os-code-inline">examples/rust</code> in this repo.</div>
    </div>
</section>

<section id="api" class="mb-12">
    <h2 class="text-xl font-bold text-[var(--color-ink)] mb-2">7. API reference</h2>
    <p class="text-[var(--color-muted)] mb-4">Methods on <code class="os-code-inline">CasClient</code> (constructed via <code class="os-code-inline">CasClient::new(CasConfig)</code>).</p>
    <div class="os-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--color-line)] bg-[var(--color-surface-2)] text-left">
                    <th class="px-4 py-2.5 font-semibold text-[var(--color-ink-2)]">Method</th>
                    <th class="px-4 py-2.5 font-semibold text-[var(--color-ink-2)]">Description</th>
                </tr>
            </thead>
            <tbody class="text-[var(--color-muted)]">
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">CasClient::new(CasConfig)</code></td><td class="px-4 py-2.5">Construct a client. Returns <code class="os-code-inline">Err(CasError::Config)</code> if a required field is empty.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">login_url() -&gt; Result&lt;String&gt;</code></td><td class="px-4 py-2.5">Build <code class="os-code-inline">{CAS_BASE}/sso/login?client_id=ID</code> to redirect the browser to.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">validate_token(&amp;token).await</code></td><td class="px-4 py-2.5">Validate a callback token server-to-server. Resolves to <code class="os-code-inline">Ok(Some(CasUser))</code>, <code class="os-code-inline">Ok(None)</code>, or <code class="os-code-inline">Err(CasError::Http)</code>.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">logout().await</code></td><td class="px-4 py-2.5">Best-effort <code class="os-code-inline">POST /api/logout</code> notification. Returns <code class="os-code-inline">Ok(true)</code> on success, <code class="os-code-inline">Ok(false)</code> on error.</td></tr>
                <tr class="border-b border-[var(--color-line)]"><td class="px-4 py-2.5"><code class="os-code-inline">client_id() -&gt; &amp;str</code></td><td class="px-4 py-2.5">The configured client id (handy for templates/logging).</td></tr>
                <tr><td class="px-4 py-2.5"><code class="os-code-inline">callback_url() -&gt; &amp;str</code></td><td class="px-4 py-2.5">The configured browser-facing callback URL.</td></tr>
            </tbody>
        </table>
    </div>
    <p class="text-sm text-[var(--color-muted)] mt-4"><code class="os-code-inline">CasUser</code> exposes <code class="os-code-inline">id</code> (a <code class="os-code-inline">serde_json::Value</code>), <code class="os-code-inline">username</code>, <code class="os-code-inline">email</code>, and an optional <code class="os-code-inline">roles</code> vector. The validation endpoint <code class="os-code-inline">POST /api/validate-token</code> responds with <code class="os-code-inline">{ valid: true, user: { id, username, email }, expires_at }</code> on success. Errors surface through the <code class="os-code-inline">CasError</code> enum: <code class="os-code-inline">Config</code>, <code class="os-code-inline">Http</code>, and <code class="os-code-inline">Url</code>.</p>
</section>

<section class="border-t border-[var(--color-line)] pt-10">
    <h2 class="text-xs font-semibold text-[var(--color-faint)] uppercase tracking-widest mb-4">Next steps</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('docs.api.overview') }}" class="os-card os-card-hover flex items-center gap-3 p-4"><i class="fas fa-code text-[var(--color-muted)] text-sm"></i><span class="text-sm font-medium text-[var(--color-ink-2)]">API reference</span></a>
        <a href="{{ route('docs.security') }}" class="os-card os-card-hover flex items-center gap-3 p-4"><i class="fas fa-shield-halved text-[var(--color-muted)] text-sm"></i><span class="text-sm font-medium text-[var(--color-ink-2)]">Security guide</span></a>
        <a href="{{ route('docs.webhooks') }}" class="os-card os-card-hover flex items-center gap-3 p-4"><i class="fas fa-bolt text-[var(--color-muted)] text-sm"></i><span class="text-sm font-medium text-[var(--color-ink-2)]">Webhooks</span></a>
    </div>
</section>
@endsection

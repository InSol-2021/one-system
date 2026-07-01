<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — One System CAS Laravel Sample</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
               max-width: 640px; margin: 4rem auto; padding: 0 1rem; color: #1a1a1a; }
        .card { border: 1px solid #e2e2e2; border-radius: 12px; padding: 1.5rem 2rem; }
        h1 { font-size: 1.4rem; }
        h2 { font-size: 1.15rem; margin-top: 0; }
        label { display: block; font-size: .9rem; font-weight: 600; margin: .9rem 0 .3rem; }
        input[type=text], input[type=password] {
               width: 100%; box-sizing: border-box; padding: .6rem .7rem; font-size: 1rem;
               border: 1px solid #d1d5db; border-radius: 8px; }
        input:focus { outline: 2px solid #2563eb; outline-offset: 1px; border-color: #2563eb; }
        .btn { display: inline-block; border: 0; border-radius: 8px; padding: .6rem 1.1rem;
               font-size: 1rem; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-block { width: 100%; margin-top: 1.2rem; }
        .flash { padding: .7rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .flash-success { background: #ecfdf5; color: #065f46; }
        .flash-error { background: #fef2f2; color: #991b1b; }
        code { background: #f3f4f6; padding: .1rem .3rem; border-radius: 4px; }
        .muted { color: #6b7280; font-size: .9rem; }
        .divider { display: flex; align-items: center; text-align: center;
                   color: #9ca3af; font-size: .85rem; margin: 1.4rem 0; }
        .divider::before, .divider::after {
                   content: ''; flex: 1; border-bottom: 1px solid #e5e7eb; }
        .divider:not(:empty)::before { margin-right: .75rem; }
        .divider:not(:empty)::after { margin-left: .75rem; }
        .demo { background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 8px;
                padding: .7rem .9rem; margin-top: 1.2rem; font-size: .85rem; color: #374151; }
    </style>
</head>
<body>
    <h1>One System CAS — Laravel Sample</h1>

    {{-- Flash + validation feedback --}}
    @if (session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h2>Sign in</h2>

        {{-- (2) Local username/password form. POSTs to the same /login route the
              CAS server uses for link-validation; the handler tells the two apart. --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="{{ old('username') }}" autocomplete="username" autofocus required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   autocomplete="current-password" required>

            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        </form>

        <div class="demo">
            <strong>Demo accounts:</strong>
            <code>rajan</code> / <code>rajan123</code> &nbsp;·&nbsp;
            <code>demo</code> / <code>demo123</code>
        </div>

        <div class="divider">or</div>

        {{-- (KEEP) CAS single sign-on path, unchanged. --}}
        <a class="btn btn-primary btn-block" href="{{ route('cas.login') }}">
            Login with One System CAS
        </a>
    </div>

    <p class="muted" style="margin-top:1.5rem">
        Local accounts are stored in SQLite (<code>database/app.db</code>) and seeded
        on first run. CAS sign-on is powered by the
        <code>cas-system/laravel-client</code> package via the <code>CasClient</code> facade.
    </p>
</body>
</html>

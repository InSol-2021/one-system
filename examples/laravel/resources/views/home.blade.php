<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>One System CAS — Laravel Sample</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
               max-width: 640px; margin: 4rem auto; padding: 0 1rem; color: #1a1a1a; }
        .card { border: 1px solid #e2e2e2; border-radius: 12px; padding: 1.5rem 2rem; }
        h1 { font-size: 1.4rem; }
        .btn { display: inline-block; border: 0; border-radius: 8px; padding: .6rem 1.1rem;
               font-size: 1rem; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-danger { background: #dc2626; color: #fff; }
        .flash { padding: .7rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .flash-success { background: #ecfdf5; color: #065f46; }
        .flash-error { background: #fef2f2; color: #991b1b; }
        table { border-collapse: collapse; width: 100%; margin: 1rem 0; }
        td, th { text-align: left; padding: .4rem .6rem; border-bottom: 1px solid #eee; }
        code { background: #f3f4f6; padding: .1rem .3rem; border-radius: 4px; }
        .muted { color: #6b7280; font-size: .9rem; }
        .badge { display: inline-block; padding: .1rem .5rem; border-radius: 999px;
                 font-size: .8rem; font-weight: 600; }
        .badge-cas { background: #eef2ff; color: #3730a3; }
        .badge-local { background: #ecfdf5; color: #065f46; }
    </style>
</head>
<body>
    <h1>One System CAS — Laravel Sample</h1>

    {{-- Flash messages from the controller --}}
    @if (session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        @if ($user)
            {{-- Show the authenticated user, however they signed in (local OR CAS). --}}
            <h2>You are signed in ✅</h2>
            <table>
                <tr><th>ID</th><td>{{ $user['id'] ?? '—' }}</td></tr>
                <tr><th>Username</th><td>{{ $user['username'] ?? '—' }}</td></tr>
                <tr><th>Email</th><td>{{ $user['email'] ?? '—' }}</td></tr>
                <tr><th>Method</th><td>
                    @if ($authMethod === 'cas')
                        <span class="badge badge-cas">CAS single sign-on</span>
                    @else
                        <span class="badge badge-local">Local account</span>
                    @endif
                </td></tr>
            </table>

            {{-- Logout clears the local session (works for both methods). --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        @else
            <h2>You are not signed in</h2>
            <p class="muted">
                Sign in with a <strong>local account</strong> (username + password)
                or via <strong>CAS single sign-on</strong>. Both lead to the same
                session.
            </p>
            <a class="btn btn-primary" href="{{ route('login') }}">Sign in</a>
        @endif
    </div>

    <p class="muted" style="margin-top:1.5rem">
        Local accounts are stored in SQLite (<code>database/app.db</code>); CAS
        sign-on is powered by the <code>cas-system/laravel-client</code> package via
        the <code>CasClient</code> facade.
    </p>
</body>
</html>

/**
 * Local login page — GET /login
 *
 * Renders a small username + password form that posts (form-encoded) to
 * /api/login. On success the API route sets the app's own session cookie and
 * redirects to the dashboard; on failure it redirects back here with
 * `?error=…`, which we surface inline.
 *
 * This is the app's OWN local-account login. CAS Single-Sign-On is still
 * available separately via the "Sign in with SSO" button in the navbar.
 */
import Link from 'next/link';

export const dynamic = 'force-dynamic';

export default async function LoginPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string; returnUrl?: string }>;
}) {
  const { error } = await searchParams;
  const hasError = typeof error === 'string' && error.length > 0;

  return (
    <div className="card auth-card">
      <h1>Sign in</h1>
      <p className="muted">
        Use a local account below, or sign in with{' '}
        <strong>Single Sign-On</strong> from the top-right of the page.
      </p>

      {hasError ? (
        <p className="auth-error" role="alert">
          Invalid username or password. Please try again.
        </p>
      ) : null}

      <form className="auth-form" method="post" action="/api/login">
        <label className="field">
          <span>Username</span>
          <input
            name="username"
            type="text"
            autoComplete="username"
            required
            autoFocus
          />
        </label>

        <label className="field">
          <span>Password</span>
          <input
            name="password"
            type="password"
            autoComplete="current-password"
            required
          />
        </label>

        <button type="submit" className="btn btn-primary auth-submit">
          Sign in
        </button>
      </form>

      <p className="muted auth-hint">
        Demo accounts: <code>rajan / rajan123</code> ·{' '}
        <code>demo / demo123</code>
      </p>

      <p className="muted">
        <Link href="/">← Back home</Link>
      </p>
    </div>
  );
}

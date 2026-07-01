import { useEffect, useState } from 'react';
import {
  useCasAuth,
  useCasUser,
  CasLoginButton,
  CasProtectedRoute,
} from '@one-system/react-cas-client';

/**
 * The whole sample lives in this one component to keep things minimal.
 *
 * It now demonstrates TWO independent ways to sign in:
 *
 *   1. CAS Single-Sign-On (the original flow), driven by the
 *      @one-system/react-cas-client SDK:
 *        (a) trigger CAS login        -> <CasLoginButton /> (or login())
 *        (b) handle the ?token= callback -> done by <CasProvider> on mount
 *        (c) show the authenticated user -> useCasAuth() / useCasUser()
 *        (d) logout                   -> logout() from useCasAuth()
 *
 *   2. A LOCAL username/password account, backed by the Express server's own
 *      SQLite store + server-side session:
 *        - GET  /login renders a form (server-rendered HTML)
 *        - POST /login establishes the local session
 *        - GET  /api/me reports the locally-signed-in user
 *        - POST /logout clears the local session
 *
 * The home page reflects whichever session is active (local OR CAS).
 */
export default function App() {
  // useCasAuth() exposes the full CAS state + actions.
  const { user: casUser, isAuthenticated: casAuthed, isLoading, error, login, logout } =
    useCasAuth();

  // The LOCAL session is owned by the Express server, so we ask it via /api/me.
  const { localUser, localLoading, refreshLocal } = useLocalSession();

  // While EITHER the CAS provider is validating a callback OR we're checking the
  // local session, show a neutral loading state.
  if (isLoading || localLoading) {
    return (
      <main className="card">
        <h1>One System · React sample</h1>
        <p>Authenticating…</p>
      </main>
    );
  }

  const isAuthenticated = casAuthed || Boolean(localUser);

  async function handleLocalLogout() {
    await fetch('/logout', {
      method: 'POST',
      headers: { Accept: 'application/json' },
    });
    await refreshLocal();
  }

  return (
    <main className="card">
      <h1>One System · React sample</h1>
      <p className="subtitle">
        Vite + React using <code>@one-system/react-cas-client</code>, with a
        local username/password account option.
      </p>

      {error && <p className="error">Auth error: {error}</p>}

      {!isAuthenticated ? (
        // Not signed in -> offer BOTH options: CAS SSO and a local account.
        <section>
          <p>You are not signed in.</p>

          <div className="auth-options">
            <CasLoginButton
              className="btn btn-primary"
              returnUrl={window.location.origin + '/'}
            >
              Sign in with One System SSO
            </CasLoginButton>

            {/* You can also drive CAS login manually from the hook: */}
            <button className="btn btn-link" onClick={() => login()}>
              (or login() via useCasAuth)
            </button>
          </div>

          <p className="or">— or —</p>

          {/* The local login form is server-rendered HTML at /login. */}
          <a className="btn btn-secondary" href="/login">
            Sign in with a local account
          </a>
          <p className="hint">
            Demo accounts: <code>rajan / rajan123</code> ·{' '}
            <code>demo / demo123</code>
          </p>
        </section>
      ) : (
        // Signed in (locally OR via CAS) -> show who, and offer logout.
        <section>
          <p className="ok">
            Signed in via {casAuthed ? 'One System CAS (SSO)' : 'a local account'}.
          </p>

          {casAuthed ? (
            <CasUserCard />
          ) : (
            <LocalUserCard user={localUser} />
          )}

          {casAuthed ? (
            // CAS logout clears the SDK session (sessionStorage), pings
            // {CAS_BASE}/api/logout, then reloads to the app root.
            <button
              className="btn btn-secondary"
              onClick={() => logout(window.location.origin + '/')}
            >
              Sign out
            </button>
          ) : (
            // Local logout clears the server-side session via POST /logout.
            <button className="btn btn-secondary" onClick={handleLocalLogout}>
              Sign out
            </button>
          )}
        </section>
      )}

      {/* Demonstrates <CasProtectedRoute>: its children only render when
          authenticated via CAS. Gated behind casAuthed here to avoid a redirect
          loop for local-only sessions in this single-page demo. */}
      {casAuthed && (
        <section className="protected">
          <h2>Protected content (CAS)</h2>
          <CasProtectedRoute fallback={<p>Checking access…</p>}>
            <p>
              🔒 This block is wrapped in <code>&lt;CasProtectedRoute&gt;</code>{' '}
              and is only visible to CAS-authenticated users.
            </p>
          </CasProtectedRoute>
        </section>
      )}
    </main>
  );
}

/**
 * Tracks the LOCAL (server-side) session by polling GET /api/me. Returns the
 * locally-signed-in user (or null), a loading flag, and a refresh function.
 */
function useLocalSession() {
  const [localUser, setLocalUser] = useState(null);
  const [localLoading, setLocalLoading] = useState(true);

  const refreshLocal = async () => {
    try {
      const res = await fetch('/api/me', { headers: { Accept: 'application/json' } });
      const data = await res.json().catch(() => ({}));
      setLocalUser(data.user ?? null);
    } catch {
      setLocalUser(null);
    } finally {
      setLocalLoading(false);
    }
  };

  useEffect(() => {
    refreshLocal();
  }, []);

  return { localUser, localLoading, refreshLocal };
}

/**
 * Reads the CAS user via the lighter useCasUser() hook (user data + role
 * helpers) to show both hooks in action.
 */
function CasUserCard() {
  const { user, hasRole } = useCasUser();
  if (!user) return null;

  return (
    <dl className="user">
      <dt>id</dt>
      <dd>{user.id}</dd>
      <dt>username</dt>
      <dd>{user.username}</dd>
      <dt>email</dt>
      <dd>{user.email}</dd>
      {user.roles && (
        <>
          <dt>roles</dt>
          <dd>{user.roles.join(', ') || '(none)'}</dd>
        </>
      )}
      <dt>is admin?</dt>
      <dd>{hasRole('admin') ? 'yes' : 'no'}</dd>
    </dl>
  );
}

/** Renders the locally-authenticated user (from /api/me). */
function LocalUserCard({ user }) {
  if (!user) return null;
  return (
    <dl className="user">
      <dt>id</dt>
      <dd>{user.id}</dd>
      <dt>username</dt>
      <dd>{user.username}</dd>
      <dt>source</dt>
      <dd>{user.source || 'local'}</dd>
    </dl>
  );
}

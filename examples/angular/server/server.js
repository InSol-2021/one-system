/**
 * Tiny Express backend for the Angular CAS sample.
 *
 * Angular is a browser SPA, so it CANNOT hold the client_secret or call the
 * CAS server's /api/validate-token endpoint directly. This backend bridges
 * that gap. It does three small things:
 *
 *   1. GET  /api/config        → hands the SPA the NON-SECRET config it needs
 *                                (CAS base URL, client_id, callback_url) so the
 *                                SDK can build the login redirect at runtime.
 *
 *   2. POST /api/auth/validate → the endpoint the SDK's
 *                                `validateTokenViaBackend()` POSTs to. We take
 *                                the { token, client_id } from the browser, ADD
 *                                the client_secret, and call the CAS server
 *                                SERVER-TO-SERVER at {CAS_BASE}/api/validate-token.
 *                                We return the CAS envelope { valid, user, ... }
 *                                straight through; the SDK reads `user` from it.
 *
 *   3. Static host             → serves the built Angular app (dist/) and SPA
 *                                fallback so deep links like /profile work.
 *
 * The JWT is single-use: it is validated exactly once here, then the SDK
 * creates the app's OWN session in the browser (sessionStorage).
 *
 * ── LOCAL ACCOUNTS (in addition to CAS SSO) ─────────────────────────────────
 * The app also has its OWN local username/password accounts backed by SQLite
 * (server/db.js). The same POST /login route serves TWO contracts:
 *
 *   a) Browser login — the SPA's login form POSTs { username, password }. On
 *      success we return { success:true, user } and the SPA establishes the
 *      app's OWN browser session (the same sessionStorage the SDK/CAS uses).
 *
 *   b) CAS link-validation — the CAS server POSTs
 *      { username, password, client_validation:true }. We answer 200
 *      { success:true } for valid creds or 401 { success:false } for invalid,
 *      and NEVER create a browser session for that call.
 *
 * The detection is by the presence of the `client_validation` field (and/or an
 * Accept: application/json header). Both paths validate against the SAME
 * SQLite-backed store, so the credentials are identical.
 */
'use strict';

const path = require('path');
const express = require('express');

// Load .env first (CAS_BASE_URL, CAS_CLIENT_ID, CAS_CLIENT_SECRET,
// CAS_CALLBACK_URL, APP_PORT, optional APP_DB_PATH) so the local store below
// can honour any APP_DB_PATH override.
require('dotenv').config({ path: path.join(__dirname, '..', '.env') });

// Local user store (SQLite). Seeds two demo users on first run.
const { authenticate, DB_PATH } = require('./db');

const {
  CAS_BASE_URL = 'http://localhost:8080',
  CAS_CLIENT_ID = 'angular-sample',
  CAS_CLIENT_SECRET = '',
  CAS_CALLBACK_URL = 'http://localhost:9110/cas/callback',
  APP_PORT = '9110',
} = process.env;

// Split-horizon CAS base URLs.
//   • CAS_BASE_URL   — INTERNAL/back-channel base. Used ONLY for the
//                      server-to-server token validation below (this process
//                      reaches CAS over the internal network / docker name).
//   • CAS_PUBLIC_URL — PUBLIC, browser-facing base. Shipped to the SPA via
//                      /api/config as `serverUrl` so the in-browser SDK builds
//                      the /sso/login redirect against a host the USER'S BROWSER
//                      can actually reach. Falls back to CAS_BASE_URL when unset
//                      so single-url local dev keeps working unchanged.
const CAS_PUBLIC_URL = process.env.CAS_PUBLIC_URL || CAS_BASE_URL;

const app = express();
app.use(express.json());
// The CAS server may POST link-validation as form-encoded; accept both shapes.
app.use(express.urlencoded({ extended: true }));

// ── 1. Public, NON-SECRET config for the SPA ────────────────────────────────
// Note: CAS_CLIENT_SECRET is deliberately NOT included here.
// `serverUrl` is the PUBLIC, browser-facing base (CAS_PUBLIC_URL): the SDK's
// getLoginUrl() builds {serverUrl}/sso/login in the browser, so it MUST resolve
// from the user's network — never the internal CAS_BASE_URL used for validation.
app.get('/api/config', (_req, res) => {
  res.json({
    serverUrl: CAS_PUBLIC_URL,
    clientId: CAS_CLIENT_ID,
    callbackUrl: CAS_CALLBACK_URL,
  });
});

// ── 2. Server-to-server token validation ────────────────────────────────────
app.post('/api/auth/validate', async (req, res) => {
  const token = req.body && req.body.token;
  if (!token) {
    return res.status(400).json({ valid: false, error: 'Missing token' });
  }

  try {
    // The secret is injected HERE, on the server — never in the browser.
    const casRes = await fetch(`${CAS_BASE_URL}/api/validate-token`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        token,
        client_id: CAS_CLIENT_ID,
        client_secret: CAS_CLIENT_SECRET,
      }),
    });

    const data = await casRes.json().catch(() => ({}));

    if (!casRes.ok) {
      // CAS returns 401 { error } on failure. Mirror that to the SDK; its
      // validateTokenViaBackend() treats a non-2xx as a failed validation.
      return res
        .status(casRes.status)
        .json({ valid: false, error: data.error || 'Token validation failed' });
    }

    // Success: CAS returns { valid: true, user: { id, username, email }, expires_at }.
    // The SDK's CasValidateResponse envelope matches this shape exactly, so we
    // return it unchanged.
    return res.json(data);
  } catch (err) {
    console.error('[validate] CAS server unreachable:', err.message);
    return res
      .status(502)
      .json({ valid: false, error: 'CAS server unreachable' });
  }
});

// ── 3. Local username/password login (SQLite store) ─────────────────────────
// ONE route, TWO contracts (see file header):
//   • CAS link-validation  → presence of `client_validation` (or JSON Accept)
//                            ⇒ { success } JSON, NO browser session.
//   • Browser login        → otherwise ⇒ { success, user } for the SPA to
//                            establish the app's own session.
app.post('/login', (req, res) => {
  const body = req.body || {};
  const username = body.username;
  const password = body.password;

  // Detect the CAS server's server-to-server validation call. It posts
  // { username, password, client_validation: true } and expects only a boolean
  // verdict — it must NOT mint a browser session. The presence of the
  // `client_validation` field is the authoritative discriminator. (The CAS
  // server also sends Accept: application/json, but the SPA's own fetch does
  // too, so we key strictly on `client_validation` to avoid starving the SPA
  // of the `user` object it needs to build a session.)
  const isCasValidation = 'client_validation' in body;

  const user = authenticate(username, password);

  // (a) CAS link-validation contract: { success:true } (200) or
  //     { success:false } (401). No session is created here.
  if (isCasValidation) {
    if (user) {
      return res.status(200).json({ success: true });
    }
    return res.status(401).json({ success: false });
  }

  // (b) Browser login from the SPA's form. On success return the user so the
  //     SPA can store the app's own session (sessionStorage). The SPA treats a
  //     non-2xx as a failed login and re-renders the form with an error.
  if (!user) {
    return res
      .status(401)
      .json({ success: false, error: 'Invalid username or password.' });
  }

  // Shape the user like a CasUser so the SPA's existing session/streams work
  // unchanged for BOTH local and CAS sign-in.
  return res.status(200).json({
    success: true,
    user: {
      id: `local:${user.id}`,
      username: user.username,
      email: '',
      roles: ['local'],
    },
  });
});

// GET /login renders the SPA's login screen. The Angular app owns the /login
// route and form; the SPA fallback below serves index.html for it, so this is
// handled by the catch-all. We keep an explicit note here for discoverability.

// ── 4. Serve the built Angular SPA ───────────────────────────────────────────
// `ng build` (application builder) emits to dist/angular-sample/browser.
const distDir = path.join(__dirname, '..', 'dist', 'angular-sample', 'browser');
app.use(express.static(distDir));

// SPA fallback: any non-API route returns index.html so the Angular router
// can handle it (e.g. /profile, /cas/callback?token=...).
app.get('*', (_req, res) => {
  res.sendFile(path.join(distDir, 'index.html'));
});

const port = Number(APP_PORT);
app.listen(port, () => {
  console.log(`\nOne System CAS — Angular sample`);
  console.log(`  Listening on http://localhost:${port}`);
  console.log(`  CAS server:  ${CAS_BASE_URL}  (internal — validation)`);
  console.log(`  CAS public:  ${CAS_PUBLIC_URL}  (browser — /sso/login redirect)`);
  console.log(`  client_id:   ${CAS_CLIENT_ID}`);
  console.log(`  callback:    ${CAS_CALLBACK_URL}`);
  console.log(`  local DB:    ${DB_PATH}`);
  console.log(`  local login: http://localhost:${port}/login  (demo: rajan/rajan123, demo/demo123)`);
  if (!CAS_CLIENT_SECRET) {
    console.warn(
      '  WARNING: CAS_CLIENT_SECRET is empty — token validation will fail.',
    );
  }
  console.log('');
});

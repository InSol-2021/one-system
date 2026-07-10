# One System CAS — Next.js sample

The smallest end-to-end Next.js (App Router) app demonstrating the
[`@cas-system/nextjs-cas-client`](https://www.npmjs.com/package/@cas-system/nextjs-cas-client) SDK:

1. **Trigger CAS login** — `<CasLoginButton>` / `useCasAuth().login()`, or just
   navigating to a protected route (the middleware redirects to CAS).
2. **Handle the callback & validate the token** — the `/api/cas/callback` route
   handler validates the single-use JWT **server-to-server** via the package
   (`POST {CAS_SERVER_URL}/api/validate-token` using the `client_secret`) and
   creates the app's own signed, HttpOnly session cookie.
3. **Show the authenticated user** — `/dashboard` (a Server Component) reads the
   session via `getCasSession()` and renders the user; the navbar shows the user
   via the client-side `useCasAuth()` hook.
4. **Logout** — `/api/cas/logout` clears the session (and notifies CAS).

It is **also a real app with its own local accounts**: in addition to CAS SSO
you can sign in with a local username/password (stored in SQLite). See
[Local accounts](#local-accounts-username--password) below.

Why server-side validation: the token check needs the `client_secret`, which
must never reach the browser. In Next.js the route handlers run on the server
and hold the secret — exactly the pattern this package is built for. No separate
backend is required.

## What each piece does

| File | Role |
| ---- | ---- |
| `src/app/layout.tsx` | Wraps the app in `<CasProvider>` (client auth state). |
| `src/components/Navbar.tsx` | `useCasAuth()` + `<CasLoginButton>` — login/logout UI. |
| `src/middleware.ts` | `createCasMiddleware()` — guards `/dashboard`, redirects to CAS. |
| `src/app/api/cas/callback/route.ts` | `createCallbackHandler()` — validates token, sets session. |
| `src/app/api/cas/user/route.ts` | `createUserHandler()` — returns current user JSON. |
| `src/app/api/cas/logout/route.ts` | `createLogoutHandler()` — clears session. |
| `src/app/dashboard/page.tsx` | Protected Server Component using `getCasSession()`. |
| `src/lib/db.ts` | Local SQLite user store (better-sqlite3): schema, scrypt hashing, demo-user seeding. |
| `src/app/login/page.tsx` | `/login` — local username/password form (posts to `/api/login`). |
| `src/app/api/login/route.ts` | `/api/login` — browser form login **and** the CAS `client_validation` JSON contract. |

## Local accounts (username + password)

Besides CAS SSO, the app has its **own** local accounts in a SQLite file
(`./data/app.db` in dev; `/app/data/app.db` in Docker, override with
`APP_DB_PATH`). On first startup, if the `users` table is empty, two demo
accounts are seeded with hashed passwords (Node `crypto.scrypt` + per-user salt):

| Username | Password |
| -------- | -------- |
| `rajan`  | `rajan123` |
| `demo`   | `demo123` |

Flow:

- **Browser login** — `GET /login` renders the form; `POST /api/login`
  (form-encoded) validates against SQLite and, on success, establishes the app's
  **own** session — the *same* signed HttpOnly cookie the CAS flow uses (via
  `setCasSession`) — then redirects to `/dashboard`. On failure it re-renders the
  form with an error. A locally-authenticated user is treated identically to a
  CAS user everywhere downstream (`/dashboard`, the navbar, logout).
- **CAS link-validation contract** — the **same** `POST /api/login` also answers
  the CAS server's credential check. When the body contains `client_validation`
  (the CAS server posts `{"username","password","client_validation":true}` as
  JSON), it returns `200 {"success": true}` for valid credentials or
  `401 {"success": false}` for invalid — and **never** creates a browser session.
  The validation call is detected by the `client_validation` field and/or an
  `Accept: application/json` header.

## Package install (published, from npm)

`package.json` declares the package as a normal npm dependency, resolved from
the public registry:

```json
"@cas-system/nextjs-cas-client": "^1.0.1"
```

Just run `npm install` — npm downloads the published package (currently
`1.0.1`) straight from the npm registry into `node_modules`, built `dist/` and
all. No local linking, symlinks, or path dependencies are involved.

Note: `1.0.1` fixed the packaging bug that `1.0.0` shipped with — `1.0.0`'s
`exports` map advertised ESM/`.mjs` builds for every subpath that were never
actually emitted, so Next's webpack resolver failed with "Module not found" on
every import. `1.0.1` ships a proper dual build (`dist/esm/*.js` +
`dist/cjs/*.js`, each with its own `package.json` `"type"` marker) whose
`exports` map only points at artifacts that exist, so no build-config workaround
is needed. The `webpack.resolve.alias` block that `next.config.mjs` used to
carry has been removed.

## Prerequisites

- Node.js >= 18.17
- A running **One System CAS server** reachable at `CAS_SERVER_URL`.
- This client app **registered** on the CAS server with:
  - `client_id` = the value you put in `CAS_CLIENT_ID` (sample uses `nextjs-sample`)
  - `callback_url` = `http://localhost:9108/api/cas/callback`
    (must match `CAS_CALLBACK_URL` exactly)

## Configure

```bash
cp .env.example .env.local
# then edit .env.local with your CAS server URL, client_id and client_secret
```

Environment variables (see `.env.example` for the full list):

| Var | Used by | Notes |
| --- | --- | --- |
| `CAS_SERVER_URL` | server (handlers, middleware) | CAS origin, no trailing slash |
| `CAS_CLIENT_ID` | server | registered client id |
| `CAS_CLIENT_SECRET` | server only | **never** sent to the browser |
| `CAS_CALLBACK_URL` | server (middleware) | must equal the registered callback |
| `CAS_COOKIE_SECRET` | server | optional; signs the session cookie (falls back to `CAS_CLIENT_SECRET`) |
| `NEXT_PUBLIC_CAS_SERVER_URL` | browser | builds the SSO login redirect |
| `NEXT_PUBLIC_CAS_CLIENT_ID` | browser | builds the SSO login redirect |
| `NEXT_PUBLIC_CAS_CALLBACK_URL` | browser | builds the SSO login redirect |
| `PORT` | both | app port (9108) |

The `NEXT_PUBLIC_*` values are not secret — they only let the client-side
provider construct the `…/sso/login?client_id=…` URL. The `client_secret` stays
server-side and is used solely by the route handlers.

## Install & run (dev)

```bash
npm install
npm run dev      # http://localhost:9108  (Next dev server on the assigned port)
```

Open http://localhost:9108, click **Sign in with SSO** (or open the Dashboard).
You'll be sent to the CAS server, then bounced back to `/api/cas/callback`, and
finally land on `/dashboard` showing your user.

## Production build & run

```bash
npm run build
npm run start    # http://localhost:9108
```

## Docker

The image uses Next.js standalone output to stay small. The app depends on the
**published** `@cas-system/nextjs-cas-client` npm package (installed inside the
image via `npm install`, same as any other dependency), so the build only
needs this example directory in its context:

```bash
# from the one-system/ directory:
docker build -f examples/nextjs/Dockerfile -t cas-nextjs-sample .
docker run --rm -p 9108:9108 --env-file examples/nextjs/.env.local cas-nextjs-sample
# -> http://localhost:9108
```

## Assigned port

This sample runs on **port 9108** (dev, start, and Docker all use it).

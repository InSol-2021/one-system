# One System CAS — React sample

The smallest end-to-end React app that proves
[`@cas-system/react-cas-client`](https://www.npmjs.com/package/@cas-system/react-cas-client) works against a
running One System CAS server. It demonstrates the full lifecycle:

1. **(a) Trigger CAS login** — `<CasLoginButton>` / `login()` redirect to `{CAS_BASE}/sso/login`.
2. **(b) Handle the callback + validate** — `<CasProvider>` auto-extracts the `?token=` on
   the callback URL and POSTs it to a tiny backend, which performs the
   **server-to-server** `POST {CAS_BASE}/api/validate-token` (with the `client_secret`).
3. **(c) Show the authenticated user** — `useCasAuth()` and `useCasUser()` render the user.
4. **(d) Logout** — `logout()` clears the app's own session and pings `{CAS_BASE}/api/logout`.

A `<CasProtectedRoute>` block is included to show gated content.

In addition to CAS SSO, this sample is also a **real app with its own local
username/password accounts**. The Express backend keeps a small **SQLite** user
store (`./data/app.db`) and its own **cookie-based session**, so you can sign in
**either** with a local account **or** via One System CAS:

- `GET /login` renders a styled username + password form.
- `POST /login` validates against the SQLite store and establishes the app's own
  local session, then redirects home (re-renders the form with an error on failure).
- The **same** `POST /login` also serves the **CAS link-validation contract**:
  when the body contains `client_validation: true` (the CAS server posts
  `{username, password, client_validation:true}`), it returns `200 {"success":true}`
  for valid credentials or `401 {"success":false}` for invalid — **without** creating
  a browser session.
- Two demo accounts are seeded on first startup: **`rajan` / `rajan123`** and
  **`demo` / `demo123`** (passwords stored as salted scrypt hashes).

> **Assigned port: `9107`** — the browser app and the production server both listen here.

---

## Why there is a backend

The CAS token is validated by calling `POST {CAS_BASE}/api/validate-token` with the
**`client_secret`**. A secret must never live in browser code. So this sample ships a
**tiny Express backend** (`server.js`) that:

| Route | Purpose |
| ----- | ------- |
| `GET /api/config` | Hands the SPA the **non-secret** config (`serverUrl`, `clientId`, `callbackUrl`, `backendValidateUrl`). |
| `POST /api/auth/validate` | The endpoint the SDK targets via `backendValidateUrl`. Receives `{ token }`, adds `client_id` + `client_secret`, forwards to `{CAS_BASE}/api/validate-token`, and returns the **bare `CasUser`** the SDK expects. |
| `GET /login` | Renders the local username/password login form (server-rendered HTML). |
| `POST /login` | **Local login** → validates against SQLite, sets the local session, redirects home. **CAS validation** → when `client_validation` is present (or `Accept: application/json`), returns `{"success":bool}` (200/401) with **no** session. |
| `POST /logout` | Clears the local session. |
| `GET /api/me` | Reports the locally-signed-in user (`{ user }` or `{ user: null }`). The SPA uses this to know if a local session is active. |
| `GET *` (prod) | Serves the built SPA from `./dist`. |

The SDK's flow (see the package README): `<CasProvider>` → `validateTokenViaBackend()` →
`POST backendValidateUrl` → **our backend** → `POST {CAS_BASE}/api/validate-token`. The
`client_secret` stays on the server.

---

## How it depends on the package

`package.json` declares the SDK as a normal npm dependency, resolved from the public registry:

```json
"@cas-system/react-cas-client": "^1.0.0"
```

Install it the same way as any other npm package:

```bash
npm install
```

There is no local `file:` link, no monorepo alias, and no build step required in this
repository for the SDK — the published package is expected to ship its own built `dist/`
output (per its `package.json` `main`/`module`/`exports` fields).

> **Known issue (as of `1.0.0`):** the tarball currently published to npm for
> `@cas-system/react-cas-client@1.0.0` does **not** actually contain `dist/` (verified with
> `npm pack @cas-system/react-cas-client@1.0.0` — the tarball only has `LICENSE`,
> `package.json`, and `README.md`), even though its `package.json` points `main`/`module`/
> `exports`/`types` at `dist/index.js` / `dist/index.mjs` / `dist/index.d.ts`. As a result,
> `npm run build` in this sample currently fails with:
> `[commonjs--resolver] Failed to resolve entry for package "@cas-system/react-cas-client". The package may have incorrect main/module/exports specified in its package.json.`
> This needs to be fixed upstream (re-publish the package with `dist/` included). There is
> intentionally no local workaround/alias in this sample for it — see `vite.config.js`.

---

## Prerequisites

- Node.js 18+ (uses the built-in `fetch`).
- A running **One System CAS server** reachable at `CAS_BASE_URL`.
- A **client-system registration** on that CAS server for this sample:
  - **`client_id`**: `react-sample` (matches `CAS_CLIENT_ID`)
  - **`callback_url`**: `http://localhost:9107/` (matches `CAS_CALLBACK_URL`)
  - …and the matching **`client_secret`**.

---

## Configure

```bash
cp .env.example .env
# edit .env: set CAS_BASE_URL, CAS_CLIENT_ID, CAS_CLIENT_SECRET, CAS_CALLBACK_URL, PORT
```

| Var | Meaning |
| --- | ------- |
| `CAS_BASE_URL` | CAS server origin, no trailing slash (e.g. `http://localhost:8080`). |
| `CAS_CLIENT_ID` | This app's registered client id (`react-sample`). |
| `CAS_CLIENT_SECRET` | Paired secret. **Backend only — never sent to the browser.** |
| `CAS_CALLBACK_URL` | Registered redirect target (`http://localhost:9107/`). |
| `PORT` | App port (`9107`). |
| `SESSION_SECRET` | Secret used to sign the local session cookie (set a long random value in production). |
| `DB_PATH` | Path to the SQLite user store (writable). Defaults to `./data/app.db`; `/app/data/app.db` in Docker. |

---

## Install & run

```bash
npm install
```

### Development

```bash
npm run dev
```

- Vite serves the SPA on **`http://localhost:9107`** (the assigned port / callback origin).
- Express runs the API on an internal port (`9108`); Vite proxies `/api/*`, `/login`, and
  `/logout` to it, so the browser only ever talks to `9107`.

Open `http://localhost:9107`, then either:

- click **Sign in with One System SSO**, authenticate on the CAS server, and you'll be
  redirected back to `9107/?token=…` — `<CasProvider>` validates the token via the backend
  and shows your user; or
- click **Sign in with a local account** to open `/login`, and sign in with one of the seeded
  demo accounts (**`rajan` / `rajan123`** or **`demo` / `demo123`**).

### Production (single server)

```bash
npm run build      # Vite builds the SPA into ./dist
npm start          # Express on PORT serves ./dist AND /api/*
```

Open `http://localhost:9107`.

---

## Docker

The Dockerfile only needs its own directory's sources (`package.json`, `server.js`, `src/`) —
the SDK is installed from the npm registry inside the image, not copied in from the monorepo.
The build is still invoked with the monorepo root as the build context (matching
`docker-compose.yml`'s `context: ..`), so run it from this directory as:

```bash
docker build -f Dockerfile -t cas-react-sample ../..
docker run --rm -p 9107:9107 --env-file .env cas-react-sample
```

A multi-stage build compiles the SPA, then runs a slim `node:20-alpine` image with the
runtime deps (`express`, `express-session`, `better-sqlite3`, `dotenv`), serving both the
API and `./dist` on port `9107`. The image creates a writable `/app/data` directory (owned
by the non-root `node` user) for the SQLite store; mount a volume there to persist accounts
across container restarts:

```bash
docker run --rm -p 9107:9107 --env-file .env -v cas-react-data:/app/data cas-react-sample
```

---

## Files

| File | Role |
| ---- | ---- |
| `src/main.jsx` | Fetches `/api/config`, mounts `<CasProvider>`. |
| `src/App.jsx` | CAS login button + local-account link, user card (`useCasAuth`/`useCasUser` or `/api/me`), protected block, logout. |
| `server.js` | Express backend: `/api/config`, `/api/auth/validate` (server-to-server CAS), local auth (`/login`, `/logout`, `/api/me`) backed by SQLite + sessions, static SPA. |
| `vite.config.js` | React plugin, dev proxy for `/api`, `/login`, `/logout`. |
| `.env.example` | Config template (CAS + local-auth vars). |
| `Dockerfile` | Single-server production image (compiles `better-sqlite3`, writable `/app/data`). |

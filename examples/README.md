# One System CAS — Sample Applications

This directory contains minimal, runnable sample applications, one per One System CAS
client SDK. Each sample demonstrates the **same CAS Single Sign-On (SSO) flow** end to
end, using only its SDK's documented public API:

1. **Trigger login** — build the CAS login URL and redirect the browser to
   `{CAS_BASE_URL}/sso/login?client_id=...`.
2. **Handle the callback** — the CAS server redirects back with a single-use `?token=`.
3. **Validate the token server-to-server** — the SDK (or a tiny backend) POSTs the token
   plus the `client_id`/`client_secret` to the CAS server's token-validation endpoint and
   receives the authenticated user. The client never decodes the JWT itself.
4. **Establish a local session** — store the returned user in the app's own session and
   render it; **logout** clears the local session and best-effort notifies CAS.

The samples are deliberately small so you can read each one and see exactly how its SDK
maps onto these four steps.

## Samples

| Sample | Language / Framework | Package used | Port | Run command | How it installs the package |
|--------|----------------------|--------------|------|-------------|--------------------------------|
| [laravel](./laravel) | PHP / Laravel 11 | `cas-system/laravel-client@1.0.0` | 9101 | `cp .env.example .env && composer install && php artisan key:generate && php artisan serve --host=0.0.0.0 --port=9101` → http://localhost:9101 | Composer dependency on the published [`cas-system/laravel-client`](https://packagist.org/packages/cas-system/laravel-client) (`^1.0.0`) in `composer.json`, resolved from the public Packagist registry into `vendor/` (registry install, no local path repository or symlink). |
| [nodejs](./nodejs) | JavaScript / Node.js (Express) | `@cas-system/node-cas-client@1.0.0` | 9102 | `cp .env.example .env && (edit values) && npm install && npm start` → http://localhost:9102 | npm dependency on the published [`@cas-system/node-cas-client`](https://www.npmjs.com/package/@cas-system/node-cas-client) (`^1.0.0`), resolved from the public npm registry into `node_modules` (registry install, no local linking). |
| [javascript](./javascript) | JavaScript (static HTML + UMD SDK) + Express backend | `@cas-system/js-cas-client` | 9103 | `cp .env.example .env && (edit values) && npm install && npm start` → http://localhost:9103 | npm dependency on the published `@cas-system/js-cas-client` (`^1.0.0`, registry install, no local linking); `server.js` serves the installed UMD file to the browser at `/vendor/cas-client.js` via `require.resolve(...)`. |
| [python](./python) | Python / Flask | `cas-system-client` (python-cas-client) | 9104 | `cp .env.example .env && (edit values)` then `python3 -m venv .venv && source .venv/bin/activate && pip install -r requirements.txt && python app.py` → http://localhost:9104 | pip dependency on the published `cas-system-client>=2.0.0` from PyPI (registry install via `requirements.txt`, no local path or editable install). |
| [java](./java) | Java / Spring Boot 2.7.x | `io.github.insol-dev:cas-client:2.0.0` (java-cas-client) | 9105 | In `examples/java`: `set -a; . ./.env; set +a; mvn spring-boot:run` → http://localhost:9105 | Maven dependency on the published [`io.github.insol-dev:cas-client`](https://central.sonatype.com/artifact/io.github.insol-dev/cas-client) `2.0.0` in `pom.xml`, resolved directly from Maven Central (registry install, no local `mvn install` step or path reference). |
| [dotnet](./dotnet) | C# / ASP.NET Core (.NET 8 minimal API) | `CasSystem.Client@2.0.0` | 9106 | `cp .env.example .env && set -a && . ./.env && set +a && dotnet run` → http://localhost:9106 | NuGet `PackageReference Include="CasSystem.Client" Version="2.0.0"` in `CasDemo.csproj`, resolved from the published [`CasSystem.Client`](https://www.nuget.org/packages/CasSystem.Client) package on nuget.org (registry install, no local linking). |
| [react](./react) | JavaScript / React (Vite SPA) + Express backend | `@cas-system/react-cas-client` | 9107 | `cp .env.example .env && npm install && npm run dev` (dev SPA on 9107, API proxied to internal 9108); prod: `npm run build && npm start` → http://localhost:9107 | npm dependency on the published `@cas-system/react-cas-client@^1.0.0` (registry install, no local linking); resolved straight from the npm registry with no `vite.config.js` alias to local source. |
| [nextjs](./nextjs) | TypeScript / Next.js 14 (App Router) | `@cas-system/nextjs-cas-client` | 9108 | `cp .env.example .env.local && (fill in values) && npm install && npm run dev` → http://localhost:9108 | npm dependency on the published `@cas-system/nextjs-cas-client@^1.0.0` (npm registry install, no local linking); `next.config.mjs` adds webpack subpath aliases only as a workaround for the published package's `exports` map bug (resolved via `require.resolve` against the installed `node_modules` package, not local source). |
| [vue](./vue) | JavaScript / Vue 3 (Vite SPA) + Express backend | `@cas-system/vue-cas-client` | 9109 | `cp .env.example .env && (edit values) && npm install && npm run dev` (serves SPA + API on 9109) → http://localhost:9109 | npm dependency on the published `@cas-system/vue-cas-client@^1.0.0` (registry install, no local linking); Vite compiles its TS/`.vue` source on the fly like any other `node_modules` package, and its peer deps (vue, vue-router, pinia) hoist normally. |
| [angular](./angular) | TypeScript / Angular 18 (standalone) + Express backend | `@cas-system/angular-cas-client` | 9110 | `cp .env.example .env && (edit secrets) && npm install && npm run build && npm start` (serves app + API on 9110) → http://localhost:9110 | npm dependency on the published `@cas-system/angular-cas-client` (`^1.0.0`, registry install, no local linking); the published package's `main`/`types` still point at raw TypeScript, so `tsconfig.app.json` includes `node_modules/@cas-system/angular-cas-client/src/**/*.ts` to compile it from source (no `preserveSymlinks` needed anymore). |
| [rust](./rust) | Rust / axum (+ SQLite via rusqlite) | `rust-cas-client` (rust-cas-client) | 9111 | `cp .env.example .env && (edit values) && cargo run` → http://localhost:9111 | Cargo registry dependency `rust-cas-client = "1.0"` in `Cargo.toml`, resolved from the published [`rust-cas-client`](https://crates.io/crates/rust-cas-client) crate on crates.io (no local path or workspace link). |

Each sample has its own `README.md` with full per-sample details, the exact public API it
exercises, and any known caveats.

## Shared prerequisites

All samples assume the same CAS environment. Before running any of them:

1. **A running CAS server.** Have a One System CAS server reachable at a base URL (this is
   the value you put in `CAS_BASE_URL`).

2. **Register each sample as a `client_system` on the CAS server.** Every sample needs its
   own client registration with a matching `client_id` and `callback_url`. The default
   callback URL is the sample's port plus its callback path, for example:

   | Sample | Port | Callback URL |
   |--------|------|--------------|
   | laravel | 9101 | `http://localhost:9101/callback` |
   | nodejs | 9102 | `http://localhost:9102/callback` |
   | javascript | 9103 | `http://localhost:9103/cas/callback` |
   | python | 9104 | `http://localhost:9104/callback` |
   | java | 9105 | `http://localhost:9105/callback` |
   | dotnet | 9106 | `http://localhost:9106/cas/callback` |
   | react | 9107 | `http://localhost:9107/` |
   | nextjs | 9108 | `http://localhost:9108/api/cas/callback` |
   | vue | 9109 | `http://localhost:9109/auth/callback` |
   | angular | 9110 | `http://localhost:9110/cas/callback` |
   | rust | 9111 | `http://localhost:9111/cas/callback` |

   The `client_id`, `client_secret`, and `callback_url` you register **must** match the
   values configured in each sample's environment (see below).

3. **Environment configuration.** Each sample reads its CAS settings from environment
   variables (via an `.env.example` you copy to `.env`). The common keys are:

   - `CAS_BASE_URL` — base URL of the running CAS server.
   - `CAS_CLIENT_ID` — the registered `client_id` for this sample.
   - `CAS_CLIENT_SECRET` — the registered client secret (server-side only; never shipped to
     the browser).
   - `CAS_CALLBACK_URL` — the registered callback URL for this sample (matches the table
     above).

   Individual samples also accept additional, framework-specific keys (e.g. a `PORT`/
   `APP_PORT`, a session/secret key, or public mirrors prefixed for the bundler). The
   Laravel sample's underlying package additionally uses `CAS_SERVER_URL`/`CAS_CLIENT_ID`/
   `CAS_CLIENT_SECRET`/`CAS_CALLBACK_URL`; its `.env.example` and README document the
   mapping to `CAS_BASE_URL`. Always check the sample's own `.env.example` and `README.md`
   for the complete list.

## A note on SPA / browser samples

The browser-based SPA samples — **javascript**, **react**, **vue**, and **angular** —
each include a tiny backend (and **nextjs** uses its own server-side route handlers).
This is required: the `client_secret` must **never** be exposed in the browser, and
server-to-server token validation must happen on a trusted server.

In these samples the browser SDK talks only to that local backend (typically a
`POST /api/auth/validate`-style endpoint). The backend holds `CAS_CLIENT_SECRET`, adds the
`client_id` + `client_secret`, performs the server-to-server validation call to the CAS
server, and returns the authenticated user to the front end. Any public config exposed to
the browser (server URL, client id, callback URL) deliberately omits the secret.

## Unified deployment

Every sample ships a `Dockerfile` so it can be containerized and run alongside the CAS
server. The samples occupy ports **9101–9111** (one per sample, as listed above), so the
whole set can run concurrently next to a CAS server without port conflicts.

> **Docker build context.** Every sample now installs its SDK from the public registry
> (Packagist, npm, PyPI, Maven Central, NuGet, or crates.io) during the image build — no
> local `packages/` copy is needed. The provided `docker-compose.yml` still uses the
> repo root as the build context (`context: ..`) with per-sample Dockerfile paths, which
> continues to work unchanged; each sample's `Dockerfile` header and `README.md` document
> its exact standalone `docker build` command.

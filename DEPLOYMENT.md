# One System — Unified Single-Server Deployment

Run the **CAS server** and **all ten sample applications** together on one host with
Docker Compose. The CAS server provides single sign-on; each sample (ports 9101–9110)
demonstrates one client SDK doing the full SSO flow against it.

```
                         ┌─────────────────────────────────────────┐
   browser ─────────────▶│  CAS server (One System)  :8000          │
        ▲                │  Postgres · Redis · Nginx · PHP-FPM      │
        │ SSO redirect   └───────────────┬─────────────────────────┘
        │                                │ server-to-server /api/validate-token
        │                                ▼
   ┌────┴───────────────────────────────────────────────────────────────────┐
   │ examples/  (one docker-compose, shared network)                          │
   │ laravel:9101 nodejs:9102 javascript:9103 python:9104 java:9105           │
   │ dotnet:9106 react:9107 nextjs:9108 vue:9109 angular:9110                  │
   └──────────────────────────────────────────────────────────────────────────┘
```

## Prerequisites

- Docker Engine 20.10+ and Docker Compose v2.
- This repository on the server. All sample images build with the **repo root as the
  build context** (each sample links its SDK from `packages/`), already wired in
  `examples/docker-compose.yml`.

## Ports

| Service | Port | Service | Port |
|---|---|---|---|
| CAS server | 8000 (see CAS compose) | dotnet | 9106 |
| laravel | 9101 | react | 9107 |
| nodejs | 9102 | nextjs | 9108 |
| javascript | 9103 | vue | 9109 |
| python | 9104 | angular | 9110 |
| java | 9105 | | |

## Step 1 — Configure the CAS server

The security remediation requires real values; copy `.env.docker` to `.env` and set:

| Variable | Why |
|---|---|
| `APP_KEY` | `php artisan key:generate` (or set a base64 key). Rotated value invalidates old sessions. |
| `APP_ENV=production`, `APP_DEBUG=false` | Never expose stack traces in production. |
| `JWT_SECRET` | **Required — fails closed if empty.** Strong random value; signs SSO JWTs. |
| `DB_*`, `REDIS_PASSWORD` | Real, non-default credentials. |
| `RECAPTCHAV3_SITEKEY`, `RECAPTCHAV3_SECRET` | Real keys (the package reads these exact names). |
| `CORS_ALLOWED_ORIGINS` | Comma-separated exact origins (the sample hosts). No `*` with credentials. |
| `SESSION_SECURE_COOKIE=true` | Serve over **HTTPS** in production or cookies won't be sent. |
| `TRUSTED_PROXIES` | Specific proxy CIDRs if behind a load balancer/reverse proxy. |

## Step 2 — Start the CAS server (creates the shared network)

```bash
# from the repo root
docker compose -f docker-compose.yml up -d --build      # or docker-production.yml for the full prod stack
```

This creates the `one-system-network` that the samples attach to.

## Step 3 — Migrate and seed

```bash
# run the schema migrations, including the two new security migrations:
#   2026_06_18_000001_hash_existing_client_secrets
#   2026_06_18_000002_encrypt_existing_2fa_secrets
docker compose -f docker-compose.yml exec php php artisan migrate --force

# seed an admin/user and register the ten sample apps as client systems:
docker compose -f docker-compose.yml exec php php artisan db:seed --force
docker compose -f docker-compose.yml exec php php artisan db:seed \
    --class="Database\\Seeders\\SampleClientsSeeder" --force
```

`SampleClientsSeeder` registers `laravel-sample` … `angular-sample` with the callback
URLs in `examples/README.md` and the demo secrets in `examples/.env.example` (stored
hashed). Override `*_CAS_SECRET` and `SAMPLE_BASE_HOST` for real deployments, then re-seed.

## Step 4 — Configure and start the samples

```bash
cd examples
cp .env.example .env
#   - set CAS_PUBLIC_URL to a URL reachable by BOTH browsers and containers
#   - set SAMPLE_BASE_HOST to the host browsers use
#   - override the *_CAS_SECRET values to match the seeder if you changed them
docker compose up -d --build
```

Open `http://localhost:9101` … `http://localhost:9110`. Each sample: **Login** →
CAS authenticates → callback → server-to-server token validation → shows the user → **Logout**.

## The CAS URL (split horizon) — important

Each sample uses **one** `CAS_BASE_URL` for both the browser login redirect and the
server-side token validation, so it must resolve from **both** places:

- **Real server:** use the server's domain or LAN IP (e.g. `http://203.0.113.10:8000`
  or `https://cas.example.com`). Browsers and containers both reach it. Recommended.
- **Docker Desktop (localhost only):** containers cannot use `localhost` to reach the
  host, so `CAS_PUBLIC_URL=http://host.docker.internal:8000` is the default and each
  service sets `extra_hosts: host.docker.internal:host-gateway`. Browsers still use
  `http://localhost:8000`; register callback URLs with `SAMPLE_BASE_HOST=http://localhost`.

## Per-sample notes

- Build context is the repo root for every image (already set in the compose).
- **SPA samples (react, vue, angular) and Next.js** bake some *public* config at build
  time. The compose passes the canonical `CAS_*` runtime env; if a sample needs its public
  values (server URL, client id, callback — **never the secret**) at build time, consult
  that sample's `README.md` for its exact `VITE_*` / `NEXT_PUBLIC_*` / Angular env names and
  add them as `build.args`. The secret stays server-side only.
- Each sample's own `README.md` documents its exact env keys, callback path, and run command.

## Security checklist (post-remediation)

- [ ] `JWT_SECRET` set (server refuses to issue/validate SSO tokens otherwise).
- [ ] `APP_DEBUG=false`, `APP_ENV=production`.
- [ ] Served over HTTPS; `SESSION_SECURE_COOKIE=true`; `TRUSTED_PROXIES` set if proxied.
- [ ] `CORS_ALLOWED_ORIGINS` enumerated (no wildcard with credentials).
- [ ] Real reCAPTCHA keys (`RECAPTCHAV3_SITEKEY` / `RECAPTCHAV3_SECRET`).
- [ ] DB/Redis/Grafana passwords are strong and not the committed placeholders.
- [ ] Migrations run (client secrets hashed; 2FA secrets encrypted at rest).
- [ ] Demo `*_CAS_SECRET` values replaced with strong per-client secrets and re-seeded.
- [ ] Admin/user routes confirmed gated (`cas.auth` / `cas.admin`); see `AUDIT_REPORT.md`.

## Troubleshooting

- **Sample build fails on the SDK dependency:** confirm you are building via
  `examples/docker-compose.yml` (context = repo root). Building from inside `examples/<x>/` fails.
- **Login redirects but callback errors:** the registered `callback_url` must exactly match
  the sample's `CAS_CALLBACK_URL` (scheme, host, port, path). Re-check the seeder vs `examples/.env`.
- **Token validation 401:** the sample's `CAS_CLIENT_SECRET` must match the seeded secret
  for that `client_id`. Re-seed after changing secrets.
- **`network one-system-network not found`:** start the CAS stack first (Step 2).

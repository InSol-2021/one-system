# One System CAS — Rust / axum sample

A minimal Rust web app (axum, port **9111**) that demonstrates **two** ways to
sign in, sharing one session:

1. **CAS SSO** via the [`rust-cas-client`](https://crates.io/crates/rust-cas-client)
   crate, installed from crates.io (single-use JWT validated server-to-server).
2. **Local accounts** backed by a SQLite store (rusqlite, Argon2-hashed
   passwords), seeded with two demo users on first run.

## Routes

| Method | Path | Purpose |
| --- | --- | --- |
| `GET` | `/` | Home — shows who you are (local OR CAS) and a logout button. |
| `GET` | `/login` | Renders the local username + password form. |
| `POST` | `/login` | Browser login **and** the CAS link-validation contract (see below). |
| `GET` | `/dashboard` | Protected page — requires a session. |
| `GET` | `/auth/login` | Start CAS SSO — redirects to `{CAS}/sso/login?client_id=rust-sample`. |
| `GET` | `/cas/callback` | CAS redirects back here with `?token=`; validated once, then a session is created. |
| `POST` | `/auth/logout` | Clears the local session (best-effort CAS notify). |

## Local demo accounts

Seeded automatically on startup if the `users` table is empty:

| Username | Password |
| --- | --- |
| `rajan` | `rajan123` |
| `demo` | `demo123` |

Passwords are stored as salted **Argon2** hashes in `./data/app.db`
(`users(id, username UNIQUE, password_hash)`).

## The `POST /login` dual contract

The same route serves a browser form **and** the CAS server's link-validation
check. The validation call is detected by the presence of a `client_validation`
field in the body (and/or `Accept: application/json`).

- **Browser form** (`application/x-www-form-urlencoded`, no `client_validation`):
  on success, a session is created and the browser is redirected to `/`; on
  failure the form re-renders with an error (HTTP 401).
- **CAS validation call** (`{"username","password","client_validation":true}`):
  responds with `200 {"success": true}` for valid credentials or
  `401 {"success": false}` for invalid — and **never** creates a session.

```bash
# Valid -> 200 {"success":true}
curl -i -X POST http://localhost:9111/login \
  -H 'Content-Type: application/json' \
  -d '{"username":"rajan","password":"rajan123","client_validation":true}'

# Invalid -> 401 {"success":false}
curl -i -X POST http://localhost:9111/login \
  -H 'Content-Type: application/json' \
  -d '{"username":"rajan","password":"wrong","client_validation":true}'
```

## Configuration

Copy `.env.example` to `.env` and adjust. Variables:

| Variable | Default | Notes |
| --- | --- | --- |
| `CAS_BASE_URL` / `CAS_SERVER_URL` | `http://localhost:8000` | CAS origin for server-to-server validation. |
| `CAS_CLIENT_ID` | `rust-sample` | Registered client id. |
| `CAS_CLIENT_SECRET` | `demo-secret-rust` | Server-side only. |
| `CAS_CALLBACK_URL` | `http://localhost:9111/cas/callback` | Must match the registered callback. |
| `PORT` | `9111` | Listen port. |
| `APP_DB_PATH` | `./data/app.db` | SQLite file (parent dir auto-created). |

## Run locally

```bash
cp .env.example .env
cargo run        # fetches rust-cas-client from crates.io on first build
# open http://localhost:9111
```

`rust-cas-client` is declared as a normal Cargo dependency in `Cargo.toml`
(`rust-cas-client = "1.0"`), resolved from the public crates.io registry —
no local path or workspace link is involved.

## Run with Docker

The build context is the repo **root** (kept consistent with the other
examples / the root `docker-compose.yml`), even though this Dockerfile only
copies `examples/rust/` — cargo resolves `rust-cas-client` from crates.io
during the image build, so nothing from `packages/` needs to be in context:

```bash
# from the one-system/ root
docker build -f examples/rust/Dockerfile -t one-system-sample-rust .
docker run --rm -p 9111:9111 --env-file examples/rust/.env one-system-sample-rust
```

The image creates a writable `/app/data` dir for the SQLite file.

## Tests

```bash
# crate unit tests (login URL building, config validation)
cargo test -p rust-cas-client
```

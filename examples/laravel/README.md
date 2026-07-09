# One System CAS — Laravel Sample

A small runnable Laravel app with **two ways to sign in, sharing one session**:

- **Local username/password** — accounts stored in a SQLite file
  (`database/app.db`), seeded on first run with two demo users.
- **CAS single sign-on** — the **`cas-system/laravel-client`** package
  (the One System Laravel CAS client) end-to-end.

### Local auth

- **`GET /login`** renders a small login form.
- **`POST /login`** validates the username/password against SQLite; on success it
  starts this app's own session and redirects home, on failure it re-renders the
  form with an error.
- The **same `POST /login`** also serves the **CAS link-validation contract**:
  when the body contains `client_validation` (the CAS server posts
  `{"username","password","client_validation":true}`) it replies with
  `200 {"success": true}` for valid credentials or `401 {"success": false}` for
  invalid — and **does not** create a browser session for that call.

Demo accounts (seeded on first run, passwords hashed with bcrypt):

| Username | Password |
| --- | --- |
| `rajan` | `rajan123` |
| `demo` | `demo123` |

### CAS single sign-on (unchanged)

1. **(a) Trigger CAS login** — `GET /cas/login` redirects the browser to the CAS
   server's `/sso/login`.
2. **(b) Handle the callback + (c) validate the token** — `GET /callback?token=…`
   validates the single-use JWT **server-to-server** through the package, then
   creates this app's own session.
3. **(c) Show the authenticated user** — `GET /` displays the user's id /
   username / email and whether they signed in locally or via CAS.
4. **(d) Logout** — `POST /logout` clears the session (and best-effort tells the
   CAS server).

Everything CAS-related goes through the package's documented public API — the
**`CasClient` facade** (`getLoginUrl`, `validateToken`, `logout`) — exactly as the
package README's "Manual Authentication" section shows. The client never decodes
the HS256 JWT itself; it always validates via the package, which POSTs to the CAS
server with the `client_id` + `client_secret`.

## How it depends on the package

`composer.json` declares **`cas-system/laravel-client`** as a normal Composer
dependency, resolved from the public Packagist registry:

```json
"require": { "cas-system/laravel-client": "^1.0.0" }
```

`composer install` downloads the published package (currently `1.0.0`) into
`vendor/cas-system/laravel-client` like any other third-party dependency — there
is no local path repository and no monorepo symlink involved.

## Files of interest

| Path | Purpose |
| --- | --- |
| `routes/web.php` | Routes: `/`, `/login` (GET+POST), `/cas/login`, `/callback`, `/logout` |
| `app/Http/Controllers/AuthController.php` | Both auth flows (local + CAS), using the `CasClient` facade for CAS |
| `app/Support/LocalUserStore.php` | SQLite-backed local user store (PDO): create table, seed demo users, validate credentials |
| `config/database.php` | SQLite connection for the local user store |
| `config/cas-client.php` | Package config (copy), wired to env vars |
| `resources/views/login.blade.php` | Local login form (+ CAS button) |
| `resources/views/home.blade.php` | Signed-in user info (local or CAS) / logout form |
| `.env.example` | All configuration |

## Prerequisites

- PHP 8.1+ with `mbstring`, `openssl`, `curl`, `tokenizer`, `xml` (and `zip` for Composer installs)
- Composer 2
- A running **One System CAS server** reachable at `CAS_BASE_URL`
- A **client system registered in the CAS server** with:
  - `client_id` = your `CAS_CLIENT_ID`
  - `client_secret` = your `CAS_CLIENT_SECRET`
  - registered **callback_url** = `http://localhost:9101/callback`
    (must match `CAS_CALLBACK_URL` exactly)

## Install

```bash
cd one-system/examples/laravel
cp .env.example .env
composer install
php artisan key:generate        # fills APP_KEY in .env
```

Edit `.env` and set `CAS_BASE_URL`, `CAS_SERVER_URL` (same value), `CAS_CLIENT_ID`,
and `CAS_CLIENT_SECRET` to match your registered client.

## Run

```bash
php artisan serve --host=0.0.0.0 --port=9101
```

Open <http://localhost:9101>, then either:

- **Local login** — enter a demo account (`rajan` / `rajan123` or `demo` /
  `demo123`) on the `/login` form, or
- **CAS** — click **Login with One System CAS**, authenticate on the CAS server,
  and you'll be redirected back to `/callback`, which validates the token.

Either path shows your user on `/`.

> Sessions and the package's user cache use the file driver. The only database is
> a small SQLite file (`database/app.db`) for the local username/password store;
> it is created and seeded automatically on first request (no `php artisan
> migrate` needed). Requires the `pdo_sqlite` PHP extension (already installed in
> the Docker image). (`config/cas-client.php` keeps `create_local_users = false`.)

## Run with Docker

The package is installed from Packagist during the image build, so the build
context only needs the example itself; build from the `one-system/` directory:

```bash
# from the repo's one-system/ directory
docker build -f examples/laravel/Dockerfile -t cas-laravel-sample .
docker run --rm -p 9101:9101 \
  -e CAS_BASE_URL=http://host.docker.internal:8001 \
  -e CAS_SERVER_URL=http://host.docker.internal:8001 \
  -e CAS_CLIENT_ID=your_client_id \
  -e CAS_CLIENT_SECRET=your_client_secret \
  -e CAS_CALLBACK_URL=http://localhost:9101/callback \
  -e APP_URL=http://localhost:9101 \
  cas-laravel-sample
```

The container generates an `APP_KEY` on start if one isn't provided.

## Configuration reference

| Env var | Meaning |
| --- | --- |
| `CAS_BASE_URL` / `CAS_SERVER_URL` | CAS server origin (set both the same) |
| `CAS_CLIENT_ID` | This client's id, registered in the CAS server |
| `CAS_CLIENT_SECRET` | This client's secret (server-to-server validation only) |
| `CAS_CALLBACK_URL` | Where CAS redirects after login; must match registration |
| `APP_URL` | Public URL of this sample (`http://localhost:9101`) |
| `APP_KEY` | Laravel session/cookie key (`php artisan key:generate`) |

### Note on the validation endpoint

Token validation is performed entirely **inside the package** (`CasAuthService::validateToken`).
This sample calls only the package's public facade, so it stays correct regardless
of the exact server endpoint the package targets. The `client_secret` is therefore
never exposed to the browser.

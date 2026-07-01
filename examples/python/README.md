# One System CAS &mdash; Python / Flask sample

A runnable Flask app that proves the local **`cas-system-client`** package
(`packages/python-cas-client`) works end-to-end against the One System CAS
server, AND ships its own **local username/password accounts** (SQLite) so a user
can sign in **either** way:

1. **Local accounts** &mdash; `GET /login` renders a username/password form;
   `POST /login` validates against a tiny SQLite store and creates the app's own
   Flask session.
2. **Trigger CAS login** &mdash; `GET /cas/login` redirects the browser to the CAS
   server (the form's "Login with CAS" button points here).
3. **Handle the callback & validate the token** &mdash; `/callback` reads `?token=...`
   and validates it **server-to-server** through the package
   (`cas.validate_token(token)`), then creates the app's own Flask session.
4. **Show the authenticated user** &mdash; `/` displays who is signed in and the
   auth source (local or CAS).
5. **Logout** &mdash; `/logout` clears the session (and notifies the CAS server).

All CAS protocol calls go through the package's public API &mdash; this sample does
not hand-roll any CAS HTTP requests. The local user store and password hashing use
only the standard library (`sqlite3`) and Werkzeug's hasher (which ships with
Flask), so **no new dependency is added**.

## Local username/password accounts

A SQLite file (`./data/app.db`, override with `APP_DB_PATH`) holds a `users`
table (`id`, `username UNIQUE`, `password_hash`). On first run, if the table is
empty, two demo accounts are seeded with **hashed** passwords:

| Username | Password   |
|----------|------------|
| `rajan`  | `rajan123` |
| `demo`   | `demo123`  |

### CAS link-validation contract on the same route

`POST /login` also serves the CAS server's link-validation call. When the body
contains `client_validation` (the CAS server posts
`{"username", "password", "client_validation": true}` as JSON or form data),
the route answers with JSON **and does not create a browser session**:

- valid credentials &rarr; `200 {"success": true}`
- invalid credentials &rarr; `401 {"success": false}`

A normal browser form `POST /login` (no `client_validation`) instead creates the
app's own session and redirects to `/` on success, or re-renders the form with an
error on failure.

## How it uses the package

`app.py` uses exactly these members of the `cas-system-client` public API:

| Used here | What it does |
|-----------|--------------|
| `from cas_client import CasClient` | Import the client class |
| `CasClient(server_url=, client_id=, client_secret=, callback_url=)` | Construct the client |
| `cas.get_login_url()` | Builds `{CAS_BASE_URL}/sso/login?client_id=...` |
| `cas.validate_token(token)` | POSTs to `{CAS_BASE_URL}/api/sso/validate` with `{token, client_id, client_secret}`; returns the user dict (`{id, username, email}`) or `None` |
| `cas.logout(token)` | Clears the package token cache and POSTs `{CAS_BASE_URL}/api/logout` |

> **Endpoint note:** the package validates against `/api/sso/validate` (the
> recommended endpoint). The CAS server also exposes a legacy
> `/api/validate-token` route, but the SDK does not use it &mdash; we follow the
> package's intended behavior.

The JWT is HS256 and is verified by the CAS server. This client never holds the
JWT secret; the `client_secret` is used **server-side only** and is never sent to
a browser. The token is **single-use**: we validate it exactly once at the
callback and then rely on our own Flask session cookie.

## Prerequisites

- Python 3.9+ (matches the `cas-system-client` package floor; tested intent: 3.12 in Docker)
- A running One System CAS server (default `http://localhost:8080`)
- A **registered client** on the CAS server (see below)

### CAS client-system registration this sample expects

Register a client on the CAS server with a callback that matches `CAS_CALLBACK_URL`:

| Field | Value |
|-------|-------|
| `client_id` | your value &rarr; put in `CAS_CLIENT_ID` |
| `client_secret` | your value &rarr; put in `CAS_CLIENT_SECRET` |
| `callback_url` | `http://localhost:9104/callback` |

The CAS server redirects to the **registered** callback URL, so it must match
`CAS_CALLBACK_URL` exactly.

## Configuration

Copy the example env file and fill it in:

```bash
cp .env.example .env
```

| Variable | Meaning |
|----------|---------|
| `CAS_BASE_URL` | CAS server origin, e.g. `http://localhost:8080` |
| `CAS_CLIENT_ID` | Registered client id |
| `CAS_CLIENT_SECRET` | Registered client secret (server-side only) |
| `CAS_CALLBACK_URL` | Must equal the registered callback (`http://localhost:9104/callback`) |
| `PORT` | App port &mdash; **9104** for this sample |
| `FLASK_SECRET_KEY` | Signs this app's own session cookie (not the CAS JWT secret) |
| `APP_DB_PATH` | Path to the local SQLite user store (default `./data/app.db`; Docker uses `/app/data/app.db`). Must be writable at runtime. |

## Install & run (local)

The sample depends on the local package via a **pip path install** (no publishing).
`requirements.txt` references `../../packages/python-cas-client` directly.

```bash
# from examples/python/
python3 -m venv .venv
source .venv/bin/activate

# Installs Flask, python-dotenv, and the local cas-client package by path.
pip install -r requirements.txt

# (If the relative path in requirements.txt is awkward for your setup, install
#  the package editable instead, then run the app:)
#   pip install -e ../../packages/python-cas-client
#   pip install Flask python-dotenv

cp .env.example .env   # then edit values
python app.py
```

Open <http://localhost:9104> and click **Login with CAS**.

## Run with Docker

Because the sample installs the local package from a sibling directory, the
Docker **build context must be the `one-system` repo root** (the directory that
contains both `packages/` and `examples/`):

```bash
# from the one-system root:
docker build -f examples/python/Dockerfile -t cas-python-sample .
docker run --rm -p 9104:9104 --env-file examples/python/.env cas-python-sample
```

The image is a small `python:3.12-slim` base and serves the app with gunicorn on
port **9104**, ready for the unified single-server deployment.

## Routes

| Route | Purpose |
|-------|---------|
| `GET /` | Show the signed-in user (local or CAS) and auth source, or a Login link |
| `GET /login` | Render the local username/password form (+ "Login with CAS" button) |
| `POST /login` | Browser form: validate against SQLite, create session, redirect. With `client_validation` in the body: CAS link-validation, returns `{"success": …}` JSON and creates no session |
| `GET /cas/login` | Redirect to the CAS server (`/sso/login?client_id=...`) |
| `GET /callback` | Read `?token=...`, validate via the package, create session |
| `GET /logout` | Clear the local session and notify the CAS server |

> **Note:** the CAS SSO entry point moved from `GET /login` to `GET /cas/login`
> so that `/login` can host the local form + the CAS link-validation contract.
> The home page's "Login with CAS" button points at `/cas/login`.

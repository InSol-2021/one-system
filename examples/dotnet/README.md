# One System CAS — .NET sample

The smallest ASP.NET Core (minimal API, .NET 8) app that proves the
**CasSystem.Client** package works end-to-end against the One System CAS server:

1. **Trigger CAS login** — `GET /auth/login` builds the login URL with
   `CasClient.GetLoginUrl()` and 302-redirects the browser to the CAS server.
2. **Handle the callback + validate the token** — `GET /cas/callback?token=...`
   reads the single-use JWT and validates it **server-to-server** with
   `CasClient.ValidateTokenAsync(token)` (the package POSTs to
   `{CAS_BASE}/api/validate-token` with the `client_secret`). On success it
   creates the app's **own** session.
3. **Show the authenticated user** — `GET /dashboard` is protected by the
   package's `CasAuthMiddleware`; it renders the user dictionary.
4. **Logout** — `POST /auth/logout` clears the app session and best-effort calls
   `CasClient.LogoutAsync()`.

The browser never sees the `client_secret`; the JWT is validated exactly once,
server-side, then exchanged for a normal ASP.NET Core session cookie.

## What package API it uses

All from `CasSystem.Client` (installed from NuGet — see below):

| API | Where |
|-----|-------|
| `CasConfig { ServerUrl, ClientId, ClientSecret, CallbackUrl }` | `Program.cs` |
| `services.AddCasClient(config)` | DI registration |
| `CasClient.GetLoginUrl()` | `/auth/login` |
| `CasClient.ValidateTokenAsync(token)` | `/cas/callback` |
| `CasClient.LogoutAsync()` | `/auth/logout` |
| `CasAuthMiddleware` (protectedPaths, loginUrl) | protects `/dashboard` |

## Package dependency (NuGet)

`CasDemo.csproj` declares a normal **PackageReference**, resolved from the
public NuGet registry:

```xml
<PackageReference Include="CasSystem.Client" Version="2.0.0" />
```

`dotnet restore` (or `dotnet build`, which restores implicitly) pulls the
published `CasSystem.Client` package straight from nuget.org — no local
project build step is needed.

## Prerequisites

- .NET SDK 8.0+ (the package targets `net8.0`), **or** Docker.
- A running One System CAS server reachable at `CAS_BASE_URL`.
- A client registered on the CAS server with:
  - **client_id**: `dotnet-demo` (matches `CAS_CLIENT_ID`)
  - **client_secret**: `dotnet-demo-secret` (matches `CAS_CLIENT_SECRET`)
  - **callback_url**: `http://localhost:9106/cas/callback` (matches `CAS_CALLBACK_URL`)

Adjust the values in `.env` to whatever you registered.

## Configuration

Copy `.env.example` to `.env` and edit:

| Variable | Meaning |
|----------|---------|
| `CAS_BASE_URL` | Origin of the CAS server (e.g. `http://localhost:8080`) |
| `CAS_CLIENT_ID` | Registered client id |
| `CAS_CLIENT_SECRET` | Registered client secret (server-to-server only) |
| `CAS_CALLBACK_URL` | Registered callback, must be `http://<host>:9106/cas/callback` |
| `PORT` | Port the app listens on — **9106** (assigned) |

## Install & run (local SDK)

```bash
cp .env.example .env          # then edit values to match your CAS registration

# Load env vars and run (zsh/bash):
set -a && . ./.env && set +a
dotnet run
```

Then open <http://localhost:9106> and click **Login with CAS**.

> The sample reads config from environment variables. `dotnet run` does not load
> `.env` automatically, hence the `set -a && . ./.env` line above. Defaults baked
> into `Program.cs` let it boot even with no `.env`, but you must register a
> matching client for login to actually succeed.

## Run with Docker

The Dockerfile pulls `CasSystem.Client` from NuGet during `dotnet restore`, so
only this sample's own directory is needed in the build context. Build from
the **`one-system/` repo root** (kept consistent with the other samples /
`docker-compose.yml`):

```bash
# from one-system/
docker build -f examples/dotnet/Dockerfile -t cas-dotnet-sample .
docker run --rm -p 9106:9106 --env-file examples/dotnet/.env cas-dotnet-sample
```

Open <http://localhost:9106>.

## Local accounts (username/password)

Alongside CAS SSO, this sample now has its **own** local accounts, stored in a
SQLite file (`Microsoft.Data.Sqlite`). On startup a `users(id, username, password_hash)`
table is created and, if empty, seeded with two demo users (passwords are salted
PBKDF2 hashes):

| Username | Password |
|----------|----------|
| `rajan`  | `rajan123` |
| `demo`   | `demo123`  |

- `GET /login` renders a small username/password form.
- `POST /login` validates against the SQLite store. On success it creates the
  app's **own** session (the same `cas_user` session key used by the CAS flow) and
  redirects to `/dashboard`; on failure it re-renders the form with an error.
- The **same** `POST /login` route also serves the CAS link-validation contract:
  when the body contains `client_validation` (the CAS server posts
  `{"username","password","client_validation":true}`, or any JSON/`Accept: application/json`
  request), it responds with JSON only — `200 {"success":true}` for valid
  credentials, `401 {"success":false}` for invalid — and does **not** create a
  browser session.

A user can sign in **either** with a local account **or** via CAS; the home page
shows who is signed in and how (`local` vs `cas`), with a logout button.

The SQLite file path is `APP_DB_PATH` (default `./data/app.db`; `/app/data/app.db`
in Docker, where `/app/data` is a writable volume).

## Routes

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/` | Public home; shows login state (local or CAS) |
| GET | `/login` | Local username/password form |
| POST | `/login` | Local login (form) **and** CAS link-validation (JSON `client_validation`) |
| GET | `/auth/login` | Redirects to CAS login |
| GET | `/cas/callback` | Validates `?token=` and creates the session |
| GET | `/dashboard` | Protected page (shows the user) |
| POST | `/auth/logout` | Clears session + CAS logout |

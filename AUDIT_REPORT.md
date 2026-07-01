# Security Audit Report — "One System" Central Authentication Service

**Target:** One System CAS (Laravel 12 + Livewire 3)
**Project root:** `/Users/rajankalyan/PhpstormProjects/one-system-final/one-system`
**Audit date:** 2026-06-18
**Scope:** Static analysis only (read-only). No files were modified.

---

## 1. Executive Summary

The One System CAS exhibits a **systemic and complete failure of access control** layered on top of **broad secret-handling and configuration weaknesses**. The single most damaging defect is that the entire `/admin/*` surface — dashboard, user management, client-system credential management, IP-whitelist management, audit logs, and SSO/security settings — is served by bare `Route::get` closures with **no authentication or authorization middleware whatsoever**, and the backing Livewire components perform no role checks. Any anonymous, remote visitor can render every admin page and invoke every state-changing Livewire action, including creating an admin account, minting/rotating client credentials, and self-whitelisting their own IP. The legacy admin REST controllers (audit logs, IP whitelist) are equally ungated.

Compounding this, the SSO token model is broken in two directions: the `/api/sso/process` endpoint consumes tokens **without enforcing single-use**, enabling replay for the full 8-hour token lifetime with no client credentials; and the JWT signing key is read via `env()` with a publicly-known hardcoded fallback that silently activates under `config:cache`, enabling token forgery. Real secrets (`APP_KEY`, `JWT_SECRET`, DB passwords) are present on disk and baked into the Docker image. Client secrets, SSO tokens, and 2FA seeds are all stored in plaintext.

Numerous lower-severity issues round out the picture: session fixation across all auth flows, weak/unenforced password and CAPTCHA policy, a wildcard CORS-with-credentials misconfiguration, ineffective rate limiting and account lockout, verbose error/health endpoints, and a large amount of dead/duplicated code.

> **Note on framework version:** The audit brief described this as Laravel 11; the project is actually **Laravel 12** (`laravel/framework` v12.53.0). This does not change any finding — the legacy `app/Http/Kernel.php` is dead code in both versions, and the live middleware config lives in `bootstrap/app.php`.

### Severity tally

| Severity | Count |
|---|---|
| Critical | 9 |
| High | 14 |
| Medium | 18 |
| Low | 15 |
| Info | 5 |
| **Total** | **61** |

---

## 2. Critical & High Findings

### 2.1 Critical

---

#### `CRITICAL` — Complete unauthenticated admin takeover: all `/admin/*` pages and Livewire actions lack any auth/authz

**Location:**
- `routes/web.php:53-90` — every admin route is a bare `Route::get(... fn() => view(...))` with no middleware (`/admin/dashboard:53`, `/admin:54`, `/admin/client-systems:57`, `/admin/ip-whitelist:69`, `/admin/users:78`, `/admin/audit-logs:81`, `/admin/sso-settings:84`, `/admin/profile:87`, `/admin/security-settings:90`)
- `bootstrap/app.php:15-25` — live Laravel 12 middleware config registers only `ip.whitelist`/`rate_limit_login`/`account_lockout` aliases and an unused `api.protected` group; no auth/can/guest middleware on the web group
- `app/Livewire/Admin/*.php` — no `mount()` authorization: `ClientSystemsManager.php:49`, `IpWhitelistManager.php:40`, `SsoSettingsComponent.php:36`, `SecuritySettingsComponent.php:53`; `DashboardComponent`, `UsersComponent`, `AuditLogsComponent` have no `mount()` at all
- `app/Http/Kernel.php` — dead code (not bound by `public/index.php`'s `$app->handleRequest()`); its `web` group and `auth`/`guest`/`can` aliases never execute

**Description:** Every admin route returns a view with no route middleware, the live middleware stack adds no global auth, and no admin Livewire component checks authentication or admin role. `config/livewire.php` sets `'middleware' => null`, so the `/livewire/update` endpoint inherits the (empty) middleware of the rendering route — meaning Livewire action methods are individually invokable by anonymous callers regardless of which page rendered. `ProfileComponent::mount` even falls back to `User::where('role','admin')->first()` and seeds a session as that admin when none exists.

**Impact:** A remote, unauthenticated attacker can render the full admin panel and invoke every state-changing action: create/elevate admin users (`UsersComponent::createUser/updateUser`), mint/rotate client credentials (`ClientSystemsManager::createClientSystem/regenerateCredentials`), disable security controls, and self-whitelist an IP (`IpWhitelistManager::addCurrentIp`) to then reach the `ip.whitelist`-protected SSO token endpoints. Total compromise of the identity provider and every downstream client system.

**Recommendation:** Wrap all `/admin/*` routes in an `['auth','can:admin']` (or equivalent) middleware group, and as defense-in-depth add an admin-role gate in each admin Livewire component's `mount()` (e.g. `abort_unless(auth()->check() && auth()->user()->isAdmin(), 403)`). Register auth middleware in `bootstrap/app.php`. Remove the `ProfileComponent` first-admin fallback. Delete the dead `app/Http/Kernel.php`.

*(This finding consolidates three confirmed reports covering the routes, the components, and the 2FA-bypass corollary.)*

---

#### `CRITICAL` — Unauthenticated SSO token replay via `/api/sso/process` (no single-use enforcement, no client auth)

**Location:** `routes/web.php:39`; `app/Http/Controllers/AuthController.php:242-290` (`processSSOCallback`); `app/Services/SsoService.php:269-294` (`processCallback`); `app/Models/SsoToken.php:66-70` (`active()` scope)

**Description:** `POST /api/sso/process` carries **no `ip.whitelist` middleware** and requires **no client credentials** (it validates only `token`). `SsoService::processCallback` decodes the JWT, looks up the `SsoToken` by `token_hash` using the `active()` scope, and returns the user — but it never checks or sets `is_used`. The `active()` scope filters only on `is_active` and `expires_at`, ignoring `is_used`. The controller then establishes a server-side session. By contrast, the credentialed `validateToken` path *does* enforce single-use (`SsoService.php:225,260`), proving the omission is real and not a global design choice. Tokens are also placed in the redirect URL query string (`SsoService.php:100,181`), so they leak via Referer/logs/history.

**Impact:** Remote account takeover. Anyone who observes a still-valid (≤8h) SSO token can POST it to this open endpoint repeatedly to obtain a full authenticated CAS session as the victim, with no client authentication and no replay limit.

**Recommendation:** Require client credential authentication on this endpoint; enforce single-use by checking `is_used` and atomically marking it used inside a DB transaction/conditional update; add `is_used` to the `active()` scope; bind consumption to the requesting `client_system_id`; stop placing the token in a query string (use POST body or a one-time code exchange); and apply `ip.whitelist`.

---

#### `CRITICAL` — Audit log API endpoints have no auth (unauthenticated read/export of the security audit trail)

**Location:** `routes/web.php:103-106`; `app/Http/Controllers/Admin/AuditLogController.php` — `index:11`, `show:74`, `export:106`, `stats:171`, `cleanup:218`

**Description:** Every method in `AuditLogController` performs zero authentication or authorization. The routes (`/api/audit-logs`, `/api/audit-logs/{id}`, `/api/audit-logs/export`, `/api/audit-logs-stats`) carry no middleware. `session('user_id')` appears only inside `logAuditEvent` (line 250) as a value written into a log row, never as a guard. `export` (lines 139-152) emits CSV including `ip_address`, `user_agent`, `username`, and event descriptions. (`cleanup()` at line 218 would mass-delete logs but is not currently wired to a route — dormant.)

**Impact:** Anonymous remote callers can enumerate and export the full security audit trail (PII: IPs, user agents, usernames/emails), and read aggregate stats. Unauthenticated sensitive-data disclosure plus tampering risk on the security audit trail.

**Recommendation:** Require authentication + admin role on all audit-log endpoints (route middleware and/or per-method guard consistent with the rest of the app). Treat audit data as sensitive PII; restrict export/cleanup to admins. Remove or gate the legacy API routes if the Livewire UI is the intended interface.

---

#### `CRITICAL` — IP-whitelist management endpoints have no auth (attacker self-whitelists to defeat `ip.whitelist` on SSO endpoints)

**Location:** `routes/web.php:72-75`; `app/Http/Controllers/Admin/IpWhitelistController.php` — `store:38`, `update:88`, `destroy:143`, `addCurrentIp:192`, `testAccess:258`, `stats:341`; `app/Http/Middleware/IpWhitelistMiddleware.php:101-108`

**Description:** `IpWhitelistController` contains no auth/authz in any method, and the routes carry no middleware. An unauthenticated attacker can `POST /api/ip-whitelist` with their own `ip_address` (`store` writes `status => 'active'`, line 51), or call `addCurrentIp` which auto-inserts `request()->ip()` and defaults `created_by` to admin user `1` (line 218). The IP-whitelist middleware matches on `status='active'` (line 101-103), so the injected entry is immediately enforced. They can also delete legitimate entries (`destroy`).

**Impact:** Because `POST /api/sso/token` and `POST /api/validate-token` rely on `ip.whitelist` as their primary access control, self-whitelisting directly enables bypass of that control. Unauthenticated control over a security boundary protecting SSO token issuance.

> Note: the GET-list route (`web.php:72`) is bound to a non-existent `index()` method (the listing method is `list()`), so anonymous *enumeration* via that route errors; but `store`/`update`/`destroy` are fully reachable and exploitable.

**Recommendation:** Gate all `IpWhitelistController` actions behind authentication + admin authorization. Remove the `created_by => session('user_id') ?: 1` fallback that masquerades anonymous changes as admin. Do not rely on the IP whitelist as the sole SSO protection while its management surface is unauthenticated.

---

#### `CRITICAL` — JWT signing key read via `env()` with publicly-known hardcoded fallback; breaks under `config:cache`

**Location:** `app/Services/SsoService.php:19` (constructor), used to sign at `:64,144` and validate at `:211,272`; `docker/entrypoint.sh:23` (`php artisan config:cache`); no `config/jwt.php` exists; same anti-pattern in `app/Console/Commands/ValidateTokenCommand.php:19` and `VerifySignatureCommand.php:23`

**Description:** `SsoService::__construct` reads `env('JWT_SECRET', 'cas-secret-key-change-in-production')`. After `php artisan config:cache` (run by the Docker entrypoint), the `.env` is no longer parsed at runtime and `env()` returns `null` for any variable not surfaced through a file under `config/`. Since there is no `config/jwt.php` and the value is read via `env()` in application code, the cached production container falls back to the **publicly-known constant** `cas-secret-key-change-in-production`, which becomes the HS256 key for both signing and validation.

**Impact:** Remote token forgery and full authentication bypass / privilege escalation in the containerized/cached deployment — an attacker can forge valid SSO JWTs with arbitrary `userId`/`role:'admin'`/`clientId`.

**Recommendation:** Add `config/jwt.php => ['secret' => env('JWT_SECRET')]` and read `config('jwt.secret')` in `SsoService` and the console commands. Remove all hardcoded secret fallbacks so a missing key fails loudly. Verify behavior after `config:cache`. Rotate the JWT secret. *(See also the High finding on the same anti-pattern for the non-cached case.)*

---

#### `CRITICAL` — Live `APP_KEY` and `JWT_SECRET` present in on-disk `.env`, baked into Docker image

**Location:** `.env:3` (`APP_KEY`), `.env:67` (`JWT_SECRET`), `.env:28` (`DB_PASSWORD=postgres`); `Dockerfile:38` (`COPY --chown=www:www . .`); no `.dockerignore`

**Description:** The working tree contains a populated `.env` with a live Laravel `APP_KEY` (AES-256 key for cookie/session encryption and `Crypt`), a real `JWT_SECRET`, and DB credentials. `.env` is gitignored and was never committed (not in repo history), but it exists on disk and `Dockerfile:38` copies the entire build context — including `.env` — into the image layers. No `.dockerignore` excludes it. The entrypoint only regenerates `APP_KEY` when it equals the literal `base64::CHANGEME` placeholder, so the real baked-in key is used as-is.

**Impact:** Anyone with access to the built image or this directory obtains the encryption key and JWT signing secret, enabling decryption of encrypted session/cookie data and forgery/replay of authentication and SSO tokens.

**Recommendation:** Add a `.dockerignore` excluding `.env` and other secrets. Inject secrets at runtime via `env_file`/orchestrator secrets. Rotate `APP_KEY` and `JWT_SECRET`.

---

### 2.2 High

---

#### `HIGH` — Client-system management API authorizes any logged-in user, not admins (privilege escalation + secret exposure)

**Location:** `routes/web.php:61-66`; `app/Http/Controllers/Admin/ClientSystemController.php` — auth-only checks at `index:17-19`, `store:72-74`, `markCredentialsViewed:153-155`, `regenerateCredentials:210-213`, `update:294-296`, `destroy:335-337`

**Description:** Every method gates only on `if (!session('user_id')) return 401` — i.e. "is someone logged in", not "is this user an admin". There is no role check anywhere in the controller, and the routes carry no middleware. Any authenticated regular user can list/create/modify/delete client systems and, critically, receive plaintext `client_secret`/`webhook_secret` in the `store` (`:126-131`) and `regenerateCredentials` (`:276-281`) responses.

**Impact:** Any standard user session can manage OAuth/SSO client systems and harvest client credentials, enabling forged SSO token requests against the CAS. (High rather than critical because a valid logged-in session is still required.)

**Recommendation:** Add an explicit admin-role check (`User::find(session('user_id'))->role === 'admin'`) in each method, or place behind admin-authorization middleware.

---

#### `HIGH` — Unauthenticated/IP-unrestricted `/api/sso/validate` route duplicates the guarded handler

**Location:** `routes/api.php:18`; `routes/web.php:36`; `app/Http/Controllers/AuthController.php:221-235`

**Description:** The same `validateSsoToken` handler is registered at `POST /api/validate-token` (web.php:36, **with** `->middleware('ip.whitelist')`) and at `POST /api/sso/validate` (api.php:18, **with no** IP control, sitting outside even the `api` group). The handler is state-changing (marks tokens used, `SsoService.php:260`).

**Impact:** An attacker outside the IP allowlist can reach SSO token validation through the api.php path, bypassing the `ip.whitelist` control applied to the web equivalent. (The handler still requires `client_id`+`client_secret`, so it is an IP-control bypass, not full anonymous access — the title's "Unauthenticated" overstates it.)

**Recommendation:** Apply `ip.whitelist` (or the unused `api.protected` group) to `/api/sso/validate`, or remove the duplicate route so only the guarded path exists.

---

#### `HIGH` — No session ID regeneration on login / 2FA / registration / SSO callback (session fixation)

**Location:** `app/Services/AuthService.php:55-73` (`completeLogin`), `:75-103` (`register`); `app/Http/Controllers/Auth2FAController.php:61-90` (`verify2FA`); `app/Http/Controllers/AuthController.php:79-134` (`login`), `:260` (`processSSOCallback`)

**Description:** Every authentication path sets session data / calls `Auth::login()` without ever calling `session()->regenerate()` or `regenerateToken()`. A codebase-wide grep finds zero session-regeneration calls in any auth flow.

**Impact:** An attacker who can fix a victim's pre-auth session id (subdomain cookie injection, shared/kiosk machine, XSS) retains the authenticated session after the victim logs in — session fixation leading to account takeover.

**Recommendation:** Call `$request->session()->regenerate()` immediately after successful credential verification and after successful 2FA verification, before populating `user_id`/`role`. Regenerate the CSRF token as well.

---

#### `HIGH` — Registration enforces only `password min:6` and ignores the hardened `RegisterRequest`

**Location:** `app/Http/Controllers/AuthController.php:136-162`; `app/Http/Requests/RegisterRequest.php:23-52`; `app/Rules/PasswordComplexity.php`

**Description:** `AuthController::register(Request $request)` validates inline with `'password' => 'required|string|min:6'`. The far stronger `RegisterRequest` (`Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()`, username regex, recaptchav3) is never type-hinted and is dead code. `PasswordComplexity` is likewise unused.

**Impact:** 6-character passwords with no complexity, no breach check, and no CAPTCHA are accepted at registration, easing brute-force/credential-stuffing.

**Recommendation:** Type-hint `RegisterRequest $request` in `register()` (or merge its rules). Remove or wire up the unused `PasswordComplexity` rule.

---

#### `HIGH` — JWT secret weak hardcoded default; unreliable under config caching (non-cached case)

**Location:** `app/Services/SsoService.php:19,64,144,211,272`

**Description:** Companion to the critical config-cache finding. Even outside the cached scenario, the fallback `cas-secret-key-change-in-production` is a publicly-known guessable value; if `JWT_SECRET` is ever unset, any party can forge HS256 SSO tokens that pass `JWT::decode`. The `.env` currently sets a strong value, so the weak default is latent today (hence high, not critical).

**Recommendation:** Move the secret into `config/jwt.php`, read via `config('jwt.secret')`, remove the hardcoded fallback, and fail closed (throw) if empty. Rotate if the default was ever used.

---

#### `HIGH` — Client secrets and webhook secrets stored and compared in plaintext

**Location:** `app/Services/SsoService.php:25,191` (`where('client_secret', $plaintext)`); `database/migrations/2025_08_05_060120_create_client_systems_table.php:19-20`; `app/Models/ClientSystem.php:20-44`; `database/seeders/ClientSystemSeeder.php:21,27`; `app/Livewire/Admin/ClientSystemsManager.php:362-374`

**Description:** `client_secret` and `webhook_secret` are plain string columns, generated as `bin2hex(random_bytes(32))` and stored verbatim, seeded with literal plaintext (`laravel_portal_secret`), and authenticated via a direct equality query (no hashing, no constant-time comparison). The model `$hidden` only affects JSON serialization and does nothing for at-rest storage or the many raw `DB::table()` reads. *(Two confirmed reports cover the same root cause — consolidated here.)*

**Impact:** Any read access to the `client_systems` table (SQLi elsewhere, backup leak, replica, DB compromise) directly exposes all client credentials, enabling SSO token forgery/validation against every registered client.

**Recommendation:** Store only a hash of `client_secret`/`webhook_secret` (e.g. SHA-256 lookup column or keyed HMAC) and verify with `hash_equals`/`Hash::check`. Display plaintext only once at generation. Rotate existing secrets.

---

#### `HIGH` — Raw JWT stored in `sso_tokens.token` column alongside its hash

**Location:** `app/Models/SsoToken.php:17-36`; `database/migrations/2025_08_05_060140_create_sso_tokens_table.php:17`; `database/migrations/2026_01_05_150000_alter_token_column_in_sso_tokens_table.php:15`; `app/Services/SsoService.php:67-79,147-149`

**Description:** `SsoToken` persists both `token` (the full HS256 JWT, a `text` column) and `token_hash = sha256(token)`. Validation looks up exclusively by `token_hash`, so the raw token column is functionally redundant for auth and serves only as a replayable secret at rest. The column was widened from `string(255)` to `text` specifically to fit the full JWT, confirming intentional plaintext storage. `$hidden` only suppresses serialization.

**Impact:** Read access to `sso_tokens` exposes live SSO bearer tokens replayable until used or expired (8h).

**Recommendation:** Persist only `token_hash` (plus non-secret payload metadata). Drop the `token` column; look up by `sha256(presented token)`.

---

#### `HIGH` — `two_factor_secret` and backup codes stored unencrypted; backup codes use non-CSPRNG

**Location:** `app/Models/User.php:40-72`; `app/Models/UserSecurity.php:22-42,110-120`; `database/migrations/2025_08_05_060110_create_users_table.php:27-29`; `database/migrations/2025_08_05_060200_create_user_security_table.php:17-19`

**Description:** The TOTP seed (`two_factor_secret`) and `two_factor_backup_codes` are stored in plaintext — columns are `text`/`json` and `$casts` only marks backup codes as `'array'` (no `'encrypted'` cast). `generateBackupCodes()` builds codes from `str_shuffle()` of a fixed alphabet, which relies on the non-cryptographic Mersenne Twister.

**Impact:** A database read exposes TOTP seeds and backup codes, fully bypassing 2FA for all users.

**Recommendation:** Use the `'encrypted'` / `'encrypted:array'` casts so the secret and codes are encrypted at rest with the app key. Replace `str_shuffle` with `random_bytes`/`random_int`.

---

#### `HIGH` — Newly generated client/webhook secrets held in a public Livewire property and shipped to the browser

**Location:** `app/Livewire/Admin/ClientSystemsManager.php:25-29` (`$newCredentials`), `:115-158` (`createClientSystem`), `:362-403` (`regenerateCredentials`); `resources/views/admin/livewire/client-systems-manager.blade.php:239,252,265` (and inline `copyToClipboard('{{ ... }}')` at `:241,254,267`)

**Description:** Freshly generated plaintext `client_secret`/`webhook_secret` are assigned to the **public** `$newCredentials` property (no `#[Locked]`), which Livewire 3 serializes into the component snapshot sent to the browser on every update, and rendered directly into readonly inputs and JS string literals. Combined with the missing admin gate, these are obtainable by any anonymous caller who triggers creation/regeneration.

**Impact:** Client credentials (the SSO `client_secret`) can be exposed to anyone able to invoke the component, enabling forged SSO token issuance.

**Recommendation:** Do not hold plaintext secrets in long-lived public properties; show once via a transient mechanism and clear immediately; store only hashed secrets. Gate the component behind admin authorization.

---

#### `HIGH` — `ProfileComponent` falls back to the first admin user when no session exists (unauthenticated admin manipulation + session fixation)

**Location:** `app/Livewire/Admin/ProfileComponent.php:43-56` (`mount`, writes admin identity into session at `:54`), `:73-80` (`updateProfile`), `:176-189`/`:233-246`/`:285-298` (2FA actions); `routes/web.php:87`

**Description:** `ProfileComponent` resolves the acting user as `auth()->user() ?? User::find(session('user_id')) ?? User::where('role','admin')->first()`, and `mount()` writes that admin's identity into the session. An unauthenticated visitor silently assumes the first admin: they can change its email/username (`updateProfile` requires no current password), and enable/disable 2FA or regenerate backup codes. (`changePassword` does require `current_password`.) The route is an ungated closure.

**Impact:** Unauthenticated takeover/manipulation of the first admin account (email change → reset hijack; 2FA disable) plus silent session fixation as that admin.

**Recommendation:** Remove the first-admin fallback. Require an authenticated admin session in `mount()` and operate strictly on the authenticated user's id.

---

#### `HIGH` — Committed `.env.docker` ships plaintext infrastructure passwords and placeholder secrets

**Location:** `.env.docker:13` (`PGPASSWORD=cas_secure_password`), `:28` (`REDIS_PASSWORD=cas_redis_password`), `:41-42` (JWT/SIGNATURE placeholders); `docker-compose.yml:49` (hardcoded redis `--requirepass cas_redis_password`); `docker-production.yml:126` (`GF_SECURITY_ADMIN_PASSWORD=admin123`)

**Description:** `.env.docker` is tracked in git and ships hardcoded Postgres/Redis passwords; the same redis password is a literal default in `docker-compose.yml`; Grafana admin password is hardcoded `admin123`. `deploy.sh`/`deploy-docker.sh` only rotate the JWT/SIGNATURE placeholders, never the DB/Redis/Grafana passwords, so if `.env.docker` is the active env these known credentials reach running infrastructure.

**Impact:** Default DB/Redis/Grafana credentials in source control usable against backing services if deployed without override.

**Recommendation:** Remove real passwords from version control; keep only placeholders and inject via secrets management. Rotate `cas_secure_password` / `cas_redis_password` / `admin123`. Use a variable for the compose `requirepass`.

---

#### `HIGH` — CORS allows wildcard origin `*` while `supports_credentials` is true

**Location:** `config/cors.php:18` (paths `api/*`,`sso/*`,`sanctum/csrf-cookie`), `:22-28` (`allowed_origins` includes `'*'`), `:45` (`supports_credentials => true`), `:20`/`:32-39` (methods `*`, headers include `Authorization`)

**Description:** `allowed_origins` is a hardcoded array containing localhost origins **plus** the literal `'*'`, combined with `supports_credentials => true`, on auth/SSO paths. The config reads no `env()` at all, so the intended `CORS_ALLOWED_ORIGINS` restriction in `.env.docker` is dead/ignored. Wildcard origin with credentials is invalid per the CORS spec.

**Impact:** High-severity CORS misconfiguration on authentication and SSO endpoints. (Note: standards-compliant browsers reject `*` with `Allow-Credentials: true` and Laravel emits a literal `*` rather than reflecting the Origin, so the precise reflection attack is not guaranteed — but the misconfiguration and dead env restriction are real.)

**Recommendation:** Remove `'*'`; enumerate exact trusted origins (or wire the config to `CORS_ALLOWED_ORIGINS`). Never combine wildcard origin with `supports_credentials=true`.

---

#### `HIGH` — reCAPTCHA env-name mismatch + committed Google test keys disable bot protection

**Location:** `.env.docker:58-59` (`RECAPTCHA_SITE_KEY`/`RECAPTCHA_SECRET_KEY`); `vendor/josiasmontag/laravel-recaptchav3/src/config/recaptchav3.php:4-5` (expects `RECAPTCHAV3_SITEKEY`/`RECAPTCHAV3_SECRET`); `app/Http/Requests/LoginRequest.php:39-42,56`; `app/Http/Requests/RegisterRequest.php:48-51`; `app/Http/Controllers/Auth/ForgotPasswordController.php:41`

**Description:** The recaptchav3 package reads `RECAPTCHAV3_SITEKEY`/`RECAPTCHAV3_SECRET`, but `.env.docker` defines the differently-named `RECAPTCHA_SITE_KEY`/`RECAPTCHA_SECRET_KEY`, so the package resolves to empty keys. The committed values are Google's well-known public test keys (always pass). On login the rule is also `nullable` (skippable by omitting the field) and labeled "optional for testing".

**Impact:** CAPTCHA protection on authentication endpoints is non-functional, enabling automated brute-force/credential-stuffing.

**Recommendation:** Use the package's expected env names with real production keys injected via secrets. Remove the Google test keys. Make enforcement non-optional.

---

#### `HIGH` — Health endpoints leak raw exceptions and operational internals to unauthenticated clients

**Location:** `routes/web.php:174-345` — error leaks at `:192` (`/health/database`), `:214` (`/health`), `:286` (`/health/full`), `:339` (`/health/redis`); info disclosure block `:240-281`

**Description:** All four health routes are unguarded closures. Catch paths return `$e->getMessage()` directly (leaking SQL fragments, schema, connection detail). `/health/full` discloses on the success path: `PHP_VERSION`, Laravel version, memory usage, environment, and exact counts of total/active/admin users, client systems, audit logs, and IP-whitelist rules.

**Impact:** Unauthenticated stack/version fingerprinting, account and admin census, and raw DB/exception detail on failure.

**Recommendation:** Place behind `ip.whitelist`/internal auth. Remove `$e->getMessage()` from client responses (return generic body, `Log::error` server-side). Strip version/environment/memory/entity counts from public output. Consider Laravel's built-in `/up` (already configured in `bootstrap/app.php:13`).

---

#### `HIGH` — `APP_DEBUG=true` with empty exception handler renders full stack traces (incl. secrets) on any error

**Location:** `.env:2,4` (`APP_ENV=local`, `APP_DEBUG=true`); `.env.example:2,4`; `bootstrap/app.php:26-28` (empty `withExceptions` closure)

**Description:** The committed `.env` (and `.env.example` default) enable debug mode, and `withExceptions()` adds no custom rendering. With `APP_DEBUG=true`, Ignition/Whoops renders full stack traces, file paths, source snippets, environment values, and query bindings on any uncaught exception. The `.env` contains real `APP_KEY`, `DB_PASSWORD`, and `JWT_SECRET`, which would be disclosed.

**Impact:** If these defaults reach a reachable environment, any error discloses full stack traces, source, and environment secrets, enabling token forgery and full compromise. (Conditional on production-reachability; `docker-production.yml` injects overrides, but plain `docker run`/`docker-compose.yml` paths do not.)

**Recommendation:** Set `APP_DEBUG=false`/`APP_ENV=production` for non-local deployments and change `.env.example` to the safe default. Add explicit exception rendering in `withExceptions()` returning generic responses.

---

## 3. Medium Findings

- **`MEDIUM` — GET-based logout enables CSRF/forced logout.** `routes/web.php:94` exposes `GET /auth/logout` (`logout.get`) alongside POST. GET carries no CSRF token, so any cross-origin `<img>`/link/prefetch can terminate a victim's session. *Fix: remove the GET route; keep POST + CSRF.*

- **`MEDIUM` — Login bypasses `LoginRequest`; reCAPTCHA never enforced.** `app/Http/Controllers/AuthController.php:79-89` validates inline (`'login'=>'required','password'=>'required'`) instead of using `LoginRequest` (`:21-44`, which defines `min:8` + recaptchav3). The FormRequest is dead code; the login reCAPTCHA is inactive (and `nullable` anyway). *Fix: use `LoginRequest` or delete it; decide reCAPTCHA intent.*

- **`MEDIUM` — Mass-assignment surface: `register` passes `$request->all()` with a widely-fillable User model.** `AuthController.php:152` → `AuthService::register` (`:75-103`). The service sets `role`/`is_active` explicitly so no current escalation exists, but `User` `$fillable` (`User.php:29-43`) includes `role`/`is_active`/`two_factor_*`; `first_name`/`last_name` are read unvalidated. *Fix: whitelist fields; remove `role`/`is_active` from `$fillable`.* (Defense-in-depth.)

- **`MEDIUM` — Account lockout keys on `email` but the login field is `login`; failure detection mismatched.** `app/Http/Middleware/AccountLockoutMiddleware.php:21-67` reads `email` (early-returns if absent) and detects failure only on HTTP 401 / JSON `'Invalid credentials'`; the web path posts `login` and fails with a 302 + `'Invalid username or password'`. Lockout is effectively non-functional for web/username logins. *Fix: key on the actual login identifier; detect failure from the auth result.* (`rate_limit_login` provides some independent throttling.)

- **`MEDIUM` — Rate limiter increments on success and counts only by IP.** `app/Http/Middleware/RateLimitLogin.php:18-33` calls `hit()` unconditionally (including successful logins), never clears on success, and keys purely on `request()->ip()`. Causes shared-NAT over-blocking and zero per-account throttling for distributed attackers. *Fix: hit only on failure / clear on success; combine IP with login identifier.*

- **`MEDIUM` — Legacy scrypt verification calls a non-existent `scrypt()` function.** `app/Services/AuthService.php:116-143` falls through to `verifyScryptPassword`, which calls a bare global `scrypt(...)` (`:139`) that PHP does not provide and `composer.json` does not polyfill. Any non-bcrypt hash → fatal 500 (login DoS) or dead code. *Fix: confirm whether scrypt hashes exist; install a real implementation or remove the branch; wrap in try/catch.*

- **`MEDIUM` — Missing `auth.sso-callback` Blade view breaks the callback flow.** `AuthController.php:239` returns `view('auth.sso-callback')`, but no such template exists (`routes/web.php:38`). `GET /auth/sso/callback` throws View-not-found (500). *Fix: create the view (consuming the token over POST, not echoing it) or remove the dead route.* (Downgraded from high — the verifiable defect is a non-functional route; the security framing is speculative.)

- **`MEDIUM` — `/api/sso/validate` exposed without IP whitelist (defense-in-depth duplicate of `/api/validate-token`).** `routes/api.php:18` vs `routes/web.php:36`. Same root cause as the High routing finding, viewed from the SSO-token dimension: credentials + single-use still required, so impact is bounded, but the intended network-layer control is silently removed. *Fix: apply `ip.whitelist` or remove the duplicate.*

- **`MEDIUM` — IP verification in `validateToken` is log-only (advisory), and client-IP derivation trusts spoofable headers.** `app/Services/SsoService.php:199-206` only `Log::warning`s on IP mismatch and continues; `getClientIp` (`:311-338`) reads raw client-controllable forwarding headers (`X-Forwarded-For`, `CF-Connecting-IP`, etc.) bypassing trusted-proxy resolution. The per-client IP binding provides no real control. *Fix: enforce on mismatch and derive client IP only via TrustProxies.*

- **`MEDIUM` — `isLocalOrPrivateIp` auto-trusts all private/loopback source IPs.** `app/Services/SsoService.php:410-416`; used in `verifyClientSystemIp` (`:343-346`) to short-circuit allow. In a Dockerized/proxied deployment the app sees a private REMOTE_ADDR, so the (already log-only) IP check always passes. *Fix: stop auto-trusting private ranges; resolve real client via TrustProxies; add unit tests.*

- **`MEDIUM` — SSO token delivered in the redirect URL query string.** `app/Services/SsoService.php:100,181` build `redirect_url = callback_url . '?token=' . $token`; `AuthController.php:305` issues `redirect()`. The 8h JWT leaks via Referer, server/proxy logs, and browser history; amplified by the no-single-use `/api/sso/process` path. *Fix: use a short-lived one-time code exchanged server-to-server, or deliver via POST/fragment; shorten TTL; enforce single-use.*

- **`MEDIUM` — Potential open-redirect / token delivery to attacker-controlled `callback_url`.** `app/Services/SsoService.php:100,181`; `AuthController.php:117-122,300-306`. `callback_url` is validated only as `url` (accepts `http://`/arbitrary host) and never re-checked at redirect time. If a `ClientSystem` is created/modified (enabled by the unauth-admin findings), the CAS mints a valid token and 302s it to an arbitrary external host. *Fix: strict registered allow-list (exact host + https) at registration and again before redirect.*

- **`MEDIUM` — User model duplicates 2FA fields with the `UserSecurity` table (two sources of truth).** `app/Models/User.php:40-69` vs `app/Models/UserSecurity.php:20-42`. Login enforcement reads only `User` (`AuthService.php:36`, `Auth2FAController.php`), while various write paths update `User` only, `UserSecurity` only, or both — no synchronizing path, so state can drift. *Fix: pick one authoritative store (recommended `user_security`) and audit all read/write paths.*

- **`MEDIUM` — `SsoToken` model/schema mismatch; `markAsUsed()` never sets `is_used`.** `app/Models/SsoToken.php:26,42,83-88` references `last_used_at` (nonexistent column; real column is `used_at`, migration `:27`), and `markAsUsed()` writes only that dead column and does **not** set `is_used`. Single-use protection works only because `SsoService::validateToken` does its own `update(['is_used'=>true])`. Latent: any future caller relying on `markAsUsed()` re-enables replay. *Fix: correct the column and have `markAsUsed()` set `['is_used'=>true,'used_at'=>now()]`.*

- **`MEDIUM` — SMTP password loaded into a public Livewire property and shipped to the client.** `app/Livewire/Admin/SecuritySettingsComponent.php:35,74,113`; `resources/views/livewire/admin/security-settings-component.blade.php:141`. The stored SMTP password is hydrated into the public `$smtp_password` and serialized into the Livewire snapshot; `saveSettings`/`testEmailConfiguration` are anonymously invokable given the missing admin gate. *Fix: treat the secret write-only (blank unless re-entered), store encrypted, and gate the component.*

- **`MEDIUM` — Exception messages reflected to clients across all admin/user controllers.** `AuditLogController.php:69,101,166,213,239`; `ClientSystemController.php:65,143,200,287`; `IpWhitelistController.php:30,80,135,184,250,299,361`; `UserDashboardController.php:94,167,228,274`. Catch blocks return `'... ' . $e->getMessage()`, leaking SQL/schema (`cas_admin.*`) on DB faults. `AuditLogController::export` also echoes `$request->all()` into the audit `data` field (`:156`). *Fix: generic client messages, `Log::error` detail server-side.* (See also the two SSO/legacy-controller error-handling reports below.)

- **`MEDIUM` — `validateSsoToken` catch-all returns raw exception message to the (IP-unrestricted) validation endpoint.** `AuthController.php:229-234` returns `$e->getMessage()` with status `$e->getCode() ?: 401`; reachable via the unguarded `/api/sso/validate`. A `QueryException` leaks SQL/schema; a PDO `SQLSTATE` code yields an invalid HTTP status. *Fix: catch known failures explicitly, return fixed 401, log real exception; don't use `$e->getCode()` as HTTP status.*

- **`MEDIUM` — Several container/deployment hygiene issues** (grouped):
  - **Container runs as root.** `Dockerfile:61` (`#USER www` commented out); `docker/supervisor/supervisord.conf` runs supervisord/php-fpm/nginx as root (only worker/scheduler drop to `www`). Increases RCE/escape blast radius. *Fix: drop privileges for php-fpm/nginx workers.*
  - **Session cookie `Secure` flag off.** `config/session.php:172` (`env('SESSION_SECURE_COOKIE')` no default) + `.env.docker:21=false`; `SESSION_ENCRYPT=false` in `.env`/`.env.example`. Auth cookies may transit over plaintext HTTP. *Fix: `SESSION_SECURE_COOKIE=true`, secure default, enforce HTTPS/HSTS.*
  - **TrustProxies misconfigured.** `app/Http/Middleware/TrustProxies.php:15` (`$proxies=null`) trusts no proxies while `TRUSTED_PROXIES` is unwired; behind nginx, `RateLimitLogin`/`AccountLockoutMiddleware` key on the proxy IP. (Note: `IpWhitelistMiddleware` reads headers directly and is unaffected.) *Fix: set trusted proxy CIDRs via `bootstrap/app.php` `trustProxies()`.*
  - **Entrypoint always seeds DB with `--force`.** `docker/entrypoint.sh:28-31` (env guard commented out) runs `db:seed --force` on every start even with `APP_ENV=production`; `ClientSystemSeeder` is keyed on `name`, resetting `client_secret` to a known plaintext value each boot. *Fix: restore the env guard / make seeders production-safe.*
  - **`APP_DEBUG=true` `.env` baked into image.** `.env:2,4` + `Dockerfile:38` + no `.dockerignore`; realized only if launched without the `docker-production.yml` overrides. *Fix: `.dockerignore` excluding `.env`; guarantee runtime `APP_DEBUG=false`.*
  - **Server requires its own client SDK via cross-project symlink + `@dev`.** `composer.json:11-16,29`; `packages/laravel-cas-client-package` symlinks to a sibling repo; name mismatch (`insol-dev/...` require vs `cas-system/laravel-client` actual); unused but auto-discovered into the container. *Fix: remove the unused dependency or vendor it in-repo with a pinned stable version.*

---

## 4. Low / Informational Findings

### Low

- **Route shadowing: `/api/audit-logs/export` captured by `/api/audit-logs/{id}`.** `routes/web.php:104-105` — the `{id}` wildcard precedes the literal `export`, so export dispatches to `show('export')`. *Fix: declare the literal route first or `->whereNumber('id')`.*
- **Broken route binding: `/api/ip-whitelist` → non-existent `IpWhitelistController@index`.** `routes/web.php:72`; the method is `list()` (`:16`). Runtime 500. *Fix: point to `list()` or rename.*
- **Dead `app/Http/Kernel.php` + unused `api.protected` group create a false sense of middleware coverage.** `app/Http/Kernel.php:31-48` (web group lists IpWhitelistMiddleware/VerifyCsrfToken, never executes); `bootstrap/app.php:22-24` (`api.protected` defined, never applied). *Fix: delete the legacy Kernel; use or remove the group.*
- **Lenient login fallback query — `orWhere` precedence.** `app/Services/AuthService.php:26-32` — ungrouped OR yields `email=X OR (username=X AND is_active=true)`, so an inactive account whose email matches can log in. *Fix: group the OR in a closure, or drop the fallback.*
- **`isUserAuthenticated` silently auto-logs-in from raw session keys.** `app/Http/Controllers/AuthController.php:35-55` re-establishes `Auth::login()` from `session('user_id')`; combined with no session regeneration, creates two parallel auth sources of truth. *Fix: standardize on the Auth guard; regenerate session on login.*
- **`validateToken` does not cross-check decoded JWT claims against the stored row.** `app/Services/SsoService.php:210-231` — `$decoded` is unused beyond signature/exp; verification leans entirely on the DB mirror. Impact minimal (forged token without a DB row is rejected). *Fix: cross-check `clientId`/`userId`/`jti` to fail closed.*
- **Duplicate models over one table with divergent `$fillable`/`$hidden`.** `app/Models/UserClientLink.php:15-25` vs `UserClientSystem.php:15-36` (both `cas_user.user_client_links`); `UserClientLink` lacks `$hidden`, risking latent serialization of `encrypted_password` ciphertext. *Fix: consolidate to one model.*
- **`User::isClientAdmin()` references a role unsupported by the schema enum.** `app/Models/User.php:85-88` checks `role === 'client_admin'`; the column enum is `('admin','user')` (`migration :20`). Dead/misleading authz helper. *Fix: remove or add the role consistently.*
- **`ClientSystem` allows mass assignment of credentials and governance flags.** `app/Models/ClientSystem.php:20-36` — `$fillable` includes `client_secret`/`webhook_secret`/`credentials_*`. No current request path feeds unfiltered input (all writes enumerate fields), so latent only. *Fix: remove these from `$fillable`.*
- **`SsoService` updates non-existent `client_systems.last_accessed`.** `app/Services/SsoService.php:81,161,208` — column absent from migration and `$fillable`, so silently discarded; admin UI always shows "Never". *Fix: add the column + cast, or remove the calls.*
- **State-changing admin actions lack object/actor validation beyond client-supplied IDs.** `ClientSystemsManager.php:285,336,419`; `IpWhitelistManager.php:239,260`; `UsersComponent.php:95,183` — no last-admin/self-deletion guards; audit `user_id` derives from a possibly-null `session('user_id')`. Low given it rides on the missing global gate. *Fix: add invariant guards and reliable audit attribution after adding the admin gate.*
- **Duplicate/dead `Auth\ForgotPasswordController`.** `app/Http/Controllers/Auth/ForgotPasswordController.php:1-68` is unreferenced (the active controller is the root-namespace one wired at `routes/web.php:5,97-100`); uses Laravel-8-style `$this->middleware('guest')` incompatible with the empty base controller; its `auth/passwords/email.blade.php` view is orphaned. *Fix: delete the dead controller + view.*
- **Active forgot-password flow lacks reCAPTCHA + email-existence parity that the dead duplicate enforces.** `app/Http/Controllers/ForgotPasswordController.php:22-64` validates only `email`; has a per-email (not per-IP) rate limit and generic responses. *Fix: add recaptchav3 and an IP-based limit.*
- **Undefined `$clientId` in `ClientSystemController::store` catch block.** `app/Http/Controllers/Admin/ClientSystemController.php:134-139` (line 138 logs `'generated_client_id' => $clientId`, never assigned → E_WARNING, logs null) and over-broadly logs `$request->all()` (`:137`). *Fix: assign/remove the variable; don't log full payload.*
- **Risky maintenance console commands print secrets / use inconsistent JWT default.** `RegenerateSecretsCommand.php` (prints new plaintext secrets, confirms plaintext storage), `ValidateTokenCommand.php:19` & `VerifySignatureCommand.php:23` (fallback `'your-secret-key'` — different from server default; `--secret=` CLI arg). Requires local CLI access. *Fix: don't print secrets; align/remove the JWT fallback; read secrets from env/stdin.*
- **Dead code:** `/admin/client-systems-old` renders a non-existent view (`routes/web.php:58`); unreferenced `resources/views/layouts/livewire.blade.php` (155 lines); two parallel Livewire admin UI conventions (`*Manager` vs `*Component`) plus superseded legacy REST controllers. *Fix: delete/redirect dead routes and views; consolidate on one Livewire convention.*
- **Redis hardcoded password + host-published port.** `docker-compose.yml:49` (literal `--requirepass cas_redis_password`), `:52-53` (`6379:6379`). *Fix: use `${REDIS_PASSWORD}`, avoid host-publishing in non-dev, rotate.*
- **Unused OpenAI SDK (pre-1.0).** `composer.json:25` (`openai-php/laravel ^0.15.0`); zero references in app code. *Fix: remove.*

### Informational

- **Configuration drift: dual middleware stacks** (`bootstrap/app.php` authoritative + dead legacy `app/Http/Kernel.php`). Maintainability/clarity hazard; compounds the TrustProxies issue. *Fix: consolidate on `bootstrap/app.php`; remove the legacy Kernel.*
- **`AuditLog` persists arbitrary unvalidated JSON in `details`** (`app/Models/AuditLog.php:17-32`). The hypothesized stored-XSS sink is **not realized** — the sole rendering view (`audit-logs-component.blade.php:149`) uses escaped Blade. Recorded as a data-handling note; add `created_at/updated_at` casts for consistency.
- **`DocumentationController::section()`/`apiEndpoint()` are NOT vulnerable to view-path traversal** (`app/Http/Controllers/Public/DocumentationController.php:240-303`) — user input is only a key into a fixed whitelist; all `view()` names are hardcoded. Verified safe.
- **User-dashboard `loginToClientSystem`/`unlinkClientSystem`/`linkClientSystem` are correctly user-scoped (no cross-user IDOR)** (`app/Http/Controllers/User/UserDashboardController.php:102-277`) — every query is constrained by the session `user_id`. Verified safe.
- **Audit premise correction: the project is Laravel 12, not 11** (`composer.json:22`, lock v12.53.0). The framework and security libs (php-jwt v6.11.1, livewire v3.7.11, google2fa, etc.) are current and non-abandoned. Dependency risk is bounded to the three dependency findings noted above.

---

## 5. Remediation Priority

Ordered most-urgent first. Items 1–7 are remotely exploitable and should be treated as emergency fixes.

1. **Lock down all `/admin/*` routes and admin Livewire components** with authentication + admin-role middleware (and `mount()` gates). This single change closes the largest attack surface (unauthenticated admin takeover, audit-log access, IP self-whitelisting, secret exposure, first-admin fallback).
2. **Authenticate + gate the legacy admin REST controllers** (`AuditLogController`, `IpWhitelistController`, `ClientSystemController`) with auth + admin role; remove the `created_by => ... ?: 1` fallback.
3. **Fix SSO token replay:** require client credentials on `/api/sso/process`, enforce single-use atomically, add `is_used` to the `active()` scope, and bind consumption to `client_system_id`.
4. **Fix the JWT secret handling:** create `config/jwt.php`, read via `config('jwt.secret')`, remove all hardcoded fallbacks (fail closed), and **rotate `JWT_SECRET`**. Align/remove the console-command fallbacks.
5. **Rotate and remove all exposed secrets:** add `.dockerignore` excluding `.env`; rotate `APP_KEY`, `JWT_SECRET`, `cas_secure_password`, `cas_redis_password`, `admin123`; remove plaintext infra passwords from `.env.docker`/compose; inject via secrets management.
6. **Apply `ip.whitelist` to `/api/sso/validate`** (or remove the duplicate route).
7. **Set `APP_DEBUG=false`/`APP_ENV=production` for non-local deployments** and fix the `.env.example` default; add generic exception rendering.
8. **Hash client/webhook secrets at rest** and verify with `hash_equals`/`Hash::check`; **stop storing the raw JWT** in `sso_tokens.token`; **encrypt `two_factor_secret`/backup codes** and replace `str_shuffle` with a CSPRNG.
9. **Regenerate the session ID** on every login / 2FA / registration / SSO-callback transition; standardize on the Auth guard.
10. **Enforce the real password and CAPTCHA policy:** use `RegisterRequest`/`LoginRequest`; fix the reCAPTCHA env-name mismatch and remove Google test keys.
11. **Fix CORS:** remove `'*'`, enumerate trusted origins, never combine wildcard with credentials.
12. **Stop placing SSO tokens in URLs;** move to a one-time code / POST exchange; shorten TTL.
13. **Restrict / sanitize health endpoints** and remove `$e->getMessage()` reflection across all controllers.
14. **Fix brute-force controls:** correct account-lockout keying/detection and rate-limiter (hit-on-failure, IP+identifier).
15. **Harden containers/deployment:** drop root for php-fpm/nginx, set `SESSION_SECURE_COOKIE=true`, configure TrustProxies, restore the seed env-guard.
16. **Clean up dead/duplicate code:** delete the legacy Kernel, dead controllers/views/routes, the unused OpenAI SDK, the cross-project symlink dependency, and consolidate duplicate models and the dual Livewire conventions.

---

## 6. Appendix — Verified and Dismissed

The following candidate findings were investigated and **refuted or downgraded to non-issues**; they are listed so reviewers know they were considered:

- **"Dead/broken `middleware('guest')` call in `ForgotPasswordController` causes password-reset routes to fatal-error."** Refuted as stated: the routes resolve to the *root-namespace* `App\Http\Controllers\ForgotPasswordController` (which has no such constructor), not the `Auth\`-namespaced duplicate. The duplicate is orphaned dead code never dispatched, so the reset flow does **not** fatal. (Captured instead as the low-severity dead-code finding.)
- **"`firebase/php-jwt` pinned to unconstrained wildcard `*`" (claimed High).** Not confirmed at the asserted High severity; the wildcard root constraint exists (`composer.json:20`) but the lock resolves a current, non-vulnerable php-jwt v6.11.1, so the dependency risk is bounded (see the informational Laravel-12 premise correction). Recorded only as dependency hygiene, not a High vulnerability.
- **`AuditLog` `details` stored-XSS sink** — the suspected sink does not exist; the rendering view uses escaped Blade. (Downgraded to informational.)
- **`DocumentationController` view-path traversal** — verified safe (whitelist-mapped dispatch, hardcoded view names).
- **User-dashboard client-system IDOR** (`loginToClientSystem`/`unlinkClientSystem`/`linkClientSystem`) — verified safe (all queries scoped to the session user).

---

*End of report.*

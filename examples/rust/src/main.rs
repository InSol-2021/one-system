//! One System CAS — Rust / axum sample app
//! ----------------------------------------
//! Proves the `rust-cas-client` crate works end-to-end **and** layers a REAL
//! local username/password login on top, so a user can sign in EITHER way:
//!
//!   CAS SSO (browser redirect → single-use JWT validated server-to-server):
//!     GET  /auth/login    -> redirect to {CAS}/sso/login?client_id=rust-sample
//!     GET  /cas/callback  -> validate ?token=... once, create our OWN session
//!     POST /auth/logout   -> clear the session (best-effort notify CAS)
//!
//!   Local accounts (SQLite store, Argon2-hashed, seeded rajan/demo):
//!     GET  /login   -> render a small username+password form
//!     POST /login   -> (a) browser form: validate, create session, redirect home
//!                      (b) CAS link-validation contract: when the body contains
//!                          `client_validation`, return JSON {"success": bool}
//!                          WITHOUT creating a session (200 valid / 401 invalid)
//!
//!   Shared:
//!     GET  /            -> home; shows who you are (local OR CAS) + logout
//!     GET  /dashboard   -> protected; requires a session
//!
//! Both auth methods land in the SAME `tower-sessions` session, so the rest of
//! the app does not care how you signed in.

mod db;

use std::collections::HashMap;

use axum::{
    body::Bytes,
    extract::State,
    http::{header, HeaderMap, StatusCode},
    response::{Html, IntoResponse, Redirect, Response},
    routing::{get, post},
    Json, Router,
};
use serde_json::json;
use tower_sessions::{MemoryStore, Session, SessionManagerLayer};

use db::Db;
use rust_cas_client::{CasClient, CasConfig};

/// Session keys. A single session is shared by both auth paths.
const SESSION_USER: &str = "user"; // display name
const SESSION_METHOD: &str = "method"; // "local" | "cas"
const SESSION_CAS_USER: &str = "cas_user"; // serialized CasUser (for the home page detail)

/// Shared application state handed to every handler.
#[derive(Clone)]
struct AppState {
    cas: CasClient,
    db: Db,
}

#[tokio::main]
async fn main() {
    // Load .env if present; real env always wins.
    let _ = dotenvy::dotenv();
    tracing_subscriber::fmt()
        .with_env_filter(
            tracing_subscriber::EnvFilter::try_from_default_env()
                .unwrap_or_else(|_| "info".into()),
        )
        .init();

    // ---- Config from env (CAS_BASE_URL/CAS_SERVER_URL accepted for parity) ---
    // Internal back-channel base for server-to-server token validation.
    let server_url = env_any(&["CAS_BASE_URL", "CAS_SERVER_URL"], "http://localhost:8000");
    // Public, browser-facing base for the /sso/login redirect (split-horizon
    // deployment). Empty -> the client falls back to `server_url`.
    let public_url = std::env::var("CAS_PUBLIC_URL").unwrap_or_default();
    let client_id = std::env::var("CAS_CLIENT_ID").unwrap_or_else(|_| "rust-sample".into());
    let client_secret =
        std::env::var("CAS_CLIENT_SECRET").unwrap_or_else(|_| "demo-secret-rust".into());
    let callback_url = std::env::var("CAS_CALLBACK_URL")
        .unwrap_or_else(|_| "http://localhost:9111/cas/callback".into());
    let port: u16 = std::env::var("PORT")
        .ok()
        .and_then(|p| p.parse().ok())
        .unwrap_or(9111);
    let db_path = std::env::var("APP_DB_PATH").unwrap_or_else(|_| "./data/app.db".into());

    // Ensure the SQLite parent dir exists (e.g. ./data) so the file is writable.
    if let Some(parent) = std::path::Path::new(&db_path).parent() {
        let _ = std::fs::create_dir_all(parent);
    }

    let cas = CasClient::new(CasConfig {
        server_url,
        public_url,
        client_id,
        client_secret,
        callback_url,
    })
    .expect("CAS client config is invalid (check CAS_* env vars)");

    let db = Db::init(&db_path).expect("failed to open/seed the SQLite user store");

    let state = AppState { cas, db };

    // In-memory session store is fine for a demo (cookie-backed session id).
    let session_layer = SessionManagerLayer::new(MemoryStore::default())
        .with_name("rust_sample_session")
        .with_secure(false); // demo over plain HTTP

    let app = Router::new()
        .route("/", get(home))
        .route("/login", get(login_form).post(login_post))
        .route("/dashboard", get(dashboard))
        .route("/auth/login", get(cas_login))
        .route("/cas/callback", get(cas_callback))
        .route("/auth/logout", post(logout))
        // Convenience GET logout for the browser link on the home page.
        .route("/logout", get(logout))
        .with_state(state)
        .layer(session_layer);

    let addr = format!("0.0.0.0:{port}");
    tracing::info!("One System CAS Rust sample listening on http://{addr}");
    let listener = tokio::net::TcpListener::bind(&addr)
        .await
        .expect("failed to bind port");
    axum::serve(listener, app).await.expect("server error");
}

// ===========================================================================
// Home + protected pages
// ===========================================================================

async fn home(session: Session) -> Html<String> {
    let user: Option<String> = session.get(SESSION_USER).await.unwrap_or(None);
    let method: Option<String> = session.get(SESSION_METHOD).await.unwrap_or(None);
    let cas_user: Option<serde_json::Value> = session.get(SESSION_CAS_USER).await.unwrap_or(None);

    let body = match user {
        None => format!(
            r#"<h1>One System CAS — Rust / axum sample</h1>
            <p class="muted">You are not signed in.</p>
            <div class="actions">
              <a class="btn primary" href="/login">Sign in with local account</a>
              <a class="btn cas" href="/auth/login">Sign in with CAS SSO</a>
            </div>
            <p class="hint">Demo local accounts: <code>rajan / rajan123</code> &middot; <code>demo / demo123</code></p>"#
        ),
        Some(name) => {
            let how = match method.as_deref() {
                Some("cas") => "via <strong>CAS SSO</strong>",
                _ => "via a <strong>local account</strong>",
            };
            let detail = cas_user
                .map(|u| {
                    format!(
                        r#"<pre>{}</pre>"#,
                        esc(&serde_json::to_string_pretty(&u).unwrap_or_default())
                    )
                })
                .unwrap_or_default();
            format!(
                r#"<h1>Welcome, {name}</h1>
                <p>You are signed in {how}.</p>
                {detail}
                <div class="actions">
                  <a class="btn primary" href="/dashboard">Go to dashboard</a>
                  <form method="post" action="/auth/logout" style="display:inline">
                    <button class="btn danger" type="submit">Logout</button>
                  </form>
                </div>"#,
                name = esc(&name),
                how = how,
                detail = detail,
            )
        }
    };
    Html(page(&body))
}

async fn dashboard(session: Session) -> Response {
    let user: Option<String> = session.get(SESSION_USER).await.unwrap_or(None);
    match user {
        None => Redirect::to("/login").into_response(),
        Some(name) => Html(page(&format!(
            r#"<h1>Dashboard</h1>
            <p>This page is protected — only a signed-in session reaches it.</p>
            <p>Signed in as <strong>{}</strong>.</p>
            <p><a class="btn" href="/">&larr; Home</a></p>"#,
            esc(&name)
        )))
        .into_response(),
    }
}

// ===========================================================================
// Local username/password auth
// ===========================================================================

async fn login_form() -> Html<String> {
    Html(page(&login_form_html(None)))
}

/// POST /login — serves BOTH:
///   (1) the CAS link-validation contract: when the parsed body contains a
///       `client_validation` field (and/or Accept: application/json), respond
///       with JSON {"success": bool} and DO NOT create a browser session.
///   (2) the normal browser login: validate, create the session, redirect home;
///       on failure re-render the form with an error (HTTP 401).
async fn login_post(
    State(state): State<AppState>,
    session: Session,
    headers: HeaderMap,
    body: Bytes,
) -> Response {
    let fields = parse_body(&headers, &body);

    let username = fields.get("username").map(String::as_str).unwrap_or("");
    let password = fields.get("password").map(String::as_str).unwrap_or("");

    let is_validation_call =
        fields.contains_key("client_validation") || wants_json(&headers);

    let valid = state.db.verify_credentials(username, password);

    // ---- (1) CAS link-validation contract -------------------------------
    if is_validation_call {
        return if valid.is_some() {
            (StatusCode::OK, Json(json!({ "success": true }))).into_response()
        } else {
            (StatusCode::UNAUTHORIZED, Json(json!({ "success": false }))).into_response()
        };
        // NOTE: no session is created for the validation call.
    }

    // ---- (2) Normal browser login --------------------------------------
    match valid {
        Some(user) => {
            let _ = session.insert(SESSION_USER, user.username.clone()).await;
            let _ = session.insert(SESSION_METHOD, "local").await;
            Redirect::to("/").into_response()
        }
        None => (
            StatusCode::UNAUTHORIZED,
            Html(page(&login_form_html(Some("Invalid username or password.")))),
        )
            .into_response(),
    }
}

// ===========================================================================
// CAS SSO flow
// ===========================================================================

async fn cas_login(State(state): State<AppState>) -> Response {
    match state.cas.login_url() {
        Ok(url) => Redirect::to(&url).into_response(),
        Err(e) => error_page(&format!("Could not build CAS login URL: {e}")),
    }
}

async fn cas_callback(
    State(state): State<AppState>,
    session: Session,
    axum::extract::Query(params): axum::extract::Query<HashMap<String, String>>,
) -> Response {
    let Some(token) = params.get("token") else {
        return error_page("Missing \"token\" query parameter on the CAS callback.");
    };

    // Single-use token: validate exactly once, server-to-server.
    match state.cas.validate_token(token).await {
        Ok(Some(user)) => {
            let display = if !user.username.is_empty() {
                user.username.clone()
            } else if !user.email.is_empty() {
                user.email.clone()
            } else {
                user.id.to_string()
            };
            let _ = session.insert(SESSION_USER, display).await;
            let _ = session.insert(SESSION_METHOD, "cas").await;
            let _ = session
                .insert(SESSION_CAS_USER, serde_json::to_value(&user).unwrap_or_default())
                .await;
            Redirect::to("/").into_response()
        }
        Ok(None) => (
            StatusCode::UNAUTHORIZED,
            error_page("CAS rejected the token (invalid, expired, or already used)."),
        )
            .into_response(),
        Err(e) => error_page(&format!("CAS validation request failed: {e}")),
    }
}

async fn logout(State(state): State<AppState>, session: Session) -> Response {
    // Best-effort CAS notification; clear our own session regardless.
    let _ = state.cas.logout().await;
    let _ = session.flush().await;
    Redirect::to("/").into_response()
}

// ===========================================================================
// Body parsing: accept JSON or form-encoded; extract the fields we need.
// ===========================================================================

fn parse_body(headers: &HeaderMap, body: &Bytes) -> HashMap<String, String> {
    let content_type = headers
        .get(header::CONTENT_TYPE)
        .and_then(|v| v.to_str().ok())
        .unwrap_or("");

    if content_type.contains("application/json") {
        // Flatten the top-level object into string fields. `client_validation`
        // is detected by key presence regardless of its value.
        if let Ok(serde_json::Value::Object(map)) = serde_json::from_slice(body) {
            return map
                .into_iter()
                .map(|(k, v)| {
                    let s = match v {
                        serde_json::Value::String(s) => s,
                        other => other.to_string(),
                    };
                    (k, s)
                })
                .collect();
        }
        return HashMap::new();
    }

    // Default: treat as application/x-www-form-urlencoded.
    serde_urlencoded::from_bytes::<Vec<(String, String)>>(body)
        .map(|pairs| pairs.into_iter().collect())
        .unwrap_or_default()
}

fn wants_json(headers: &HeaderMap) -> bool {
    headers
        .get(header::ACCEPT)
        .and_then(|v| v.to_str().ok())
        .map(|a| a.contains("application/json"))
        .unwrap_or(false)
}

fn env_any(keys: &[&str], default: &str) -> String {
    for k in keys {
        if let Ok(v) = std::env::var(k) {
            if !v.trim().is_empty() {
                return v;
            }
        }
    }
    default.to_string()
}

// ===========================================================================
// Tiny inline HTML (kept minimal so the auth flow stays the focus)
// ===========================================================================

fn login_form_html(error: Option<&str>) -> String {
    let err = error
        .map(|e| format!(r#"<p class="err">{}</p>"#, esc(e)))
        .unwrap_or_default();
    format!(
        r#"<h1>Sign in</h1>
        <p class="muted">Use a local demo account, or <a href="/auth/login">sign in with CAS SSO</a>.</p>
        {err}
        <form method="post" action="/login" class="card">
          <label>Username
            <input name="username" autocomplete="username" autofocus required>
          </label>
          <label>Password
            <input name="password" type="password" autocomplete="current-password" required>
          </label>
          <button class="btn primary" type="submit">Sign in</button>
        </form>
        <p class="hint">Demo accounts: <code>rajan / rajan123</code> &middot; <code>demo / demo123</code></p>"#,
        err = err
    )
}

fn error_page(message: &str) -> Response {
    Html(page(&format!(
        r#"<h1 class="err">Authentication error</h1>
        <p class="err">{}</p>
        <p><a class="btn primary" href="/login">Try again</a> <a class="btn" href="/">Home</a></p>"#,
        esc(message)
    )))
    .into_response()
}

fn page(body: &str) -> String {
    format!(
        r#"<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>One System CAS — Rust sample</title>
<style>
  :root {{ color-scheme: light dark; }}
  body {{ font-family: system-ui, -apple-system, Segoe UI, sans-serif; max-width: 560px;
         margin: 56px auto; padding: 0 20px; line-height: 1.55; color: #1e293b; }}
  h1 {{ font-size: 1.5rem; margin-bottom: .25rem; }}
  .muted {{ color: #64748b; }}
  .hint {{ color: #94a3b8; font-size: .9rem; }}
  .card {{ display: grid; gap: 14px; background: #f8fafc; border: 1px solid #e2e8f0;
          border-radius: 12px; padding: 20px; margin: 18px 0; }}
  label {{ display: grid; gap: 6px; font-weight: 600; font-size: .9rem; color: #334155; }}
  input {{ padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; }}
  input:focus {{ outline: 2px solid #2563eb; outline-offset: 1px; border-color: #2563eb; }}
  .actions {{ display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin: 18px 0; }}
  .btn {{ display: inline-block; padding: 10px 16px; border-radius: 8px; text-decoration: none;
         font-weight: 600; font-size: .95rem; border: 1px solid #cbd5e1; background: #fff;
         color: #1e293b; cursor: pointer; }}
  .btn.primary {{ background: #2563eb; border-color: #2563eb; color: #fff; }}
  .btn.cas {{ background: #0f172a; border-color: #0f172a; color: #fff; }}
  .btn.danger {{ background: #ef4444; border-color: #ef4444; color: #fff; }}
  pre {{ background: #0f172a; color: #e2e8f0; padding: 14px; border-radius: 8px; overflow: auto;
        font-size: .85rem; }}
  code {{ background: #f1f5f9; padding: 1px 6px; border-radius: 4px; }}
  .err {{ color: #b91c1c; font-weight: 600; }}
</style></head><body>{body}</body></html>"#,
        body = body
    )
}

/// HTML-escape untrusted text before interpolating into a page.
fn esc(s: &str) -> String {
    askama_escape::escape(s, askama_escape::Html).to_string()
}

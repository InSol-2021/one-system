//! # rust-cas-client
//!
//! A small, framework-agnostic client for the **One System CAS** SSO protocol.
//!
//! It does exactly two things, mirroring the other language SDKs in this repo:
//!
//! 1. [`CasClient::login_url`] — builds the browser redirect
//!    `{CAS_BASE}/sso/login?client_id=ID`. The CAS server authenticates the
//!    user and 302-redirects back to the client's *registered* callback URL with
//!    `?token=<JWT>` appended.
//! 2. [`CasClient::validate_token`] — validates that single-use token
//!    **server-to-server** by POSTing `{token, client_id, client_secret}` to
//!    `{CAS_BASE}/api/validate-token`. On success the CAS server returns
//!    `{ "valid": true, "user": { id, username, email }, "expires_at": ... }`.
//!
//! The `client_secret` is only ever used here (server-side) and is never exposed
//! to the browser. The JWT is single-use, so callers must validate it exactly
//! once and then establish their own application session.
//!
//! The crate is deliberately runtime/framework agnostic: it exposes one async
//! method backed by `reqwest` and plain data types, so it drops into axum,
//! actix-web, or any other stack.

use serde::{Deserialize, Serialize};
use thiserror::Error;
use url::Url;

/// Errors that can occur while talking to the CAS server.
#[derive(Debug, Error)]
pub enum CasError {
    /// A required configuration field was empty.
    #[error("configuration error: {0}")]
    Config(String),

    /// The underlying HTTP request failed (DNS, connection, timeout, ...).
    #[error("http transport error: {0}")]
    Http(#[from] reqwest::Error),

    /// The base URL could not be parsed/joined.
    #[error("invalid url: {0}")]
    Url(#[from] url::ParseError),
}

/// The authenticated user returned by the CAS server on successful validation.
///
/// Mirrors the shape returned by the other SDKs: `{ id, username, email }` plus
/// any optional roles the server includes.
#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct CasUser {
    /// Stable user identifier from the CAS server.
    #[serde(default)]
    pub id: serde_json::Value,
    /// Login name.
    #[serde(default)]
    pub username: String,
    /// Email address (may be empty depending on the CAS user record).
    #[serde(default)]
    pub email: String,
    /// Optional roles array, if the CAS server attaches them.
    #[serde(default)]
    pub roles: Vec<String>,
}

/// Wire shape of the POST body sent to `/api/validate-token`.
#[derive(Serialize)]
struct ValidateRequest<'a> {
    token: &'a str,
    client_id: &'a str,
    client_secret: &'a str,
}

/// Wire shape of the JSON returned by `/api/validate-token`.
#[derive(Deserialize)]
struct ValidateResponse {
    #[serde(default)]
    valid: bool,
    #[serde(default)]
    user: Option<CasUser>,
    #[serde(default)]
    expires_at: Option<String>,
}

/// Configuration for a [`CasClient`].
///
/// All values come from the host application's environment
/// (`CAS_BASE_URL`/`CAS_SERVER_URL`, `CAS_CLIENT_ID`, `CAS_CLIENT_SECRET`,
/// `CAS_CALLBACK_URL`). `server_url` is the address used for the **server-side**
/// validation call (internal docker host in this deployment); the callback URL
/// is browser-facing and registered with the CAS server.
#[derive(Debug, Clone)]
pub struct CasConfig {
    /// CAS server origin used for server-to-server calls, e.g.
    /// `http://one-system-cas` (no trailing slash required — it is normalized).
    pub server_url: String,
    /// Optional PUBLIC, browser-facing CAS origin used to build the
    /// `/sso/login` redirect, e.g. `http://161.97.158.202:91`. In a
    /// split-horizon deployment the browser cannot reach `server_url` (an
    /// internal docker host), so the login redirect must target this public
    /// origin instead. When empty, [`login_url`](CasClient::login_url) falls
    /// back to `server_url` (single-url local dev). Validation always uses
    /// `server_url`.
    pub public_url: String,
    /// Registered client id for this app, e.g. `rust-sample`.
    pub client_id: String,
    /// Registered client secret (server-side only — never sent to the browser).
    pub client_secret: String,
    /// Browser-facing callback URL registered with the CAS server.
    pub callback_url: String,
}

/// The CAS SSO client.
#[derive(Debug, Clone)]
pub struct CasClient {
    config: CasConfig,
    http: reqwest::Client,
    /// Normalized internal base for server-to-server calls (trailing slash
    /// stripped).
    base: String,
    /// Normalized PUBLIC base for the browser `/sso/login` redirect. Falls back
    /// to `base` when no `public_url` is configured.
    public_base: String,
}

impl CasClient {
    /// Build a client from [`CasConfig`].
    ///
    /// Returns [`CasError::Config`] if `server_url`, `client_id`, or
    /// `client_secret` are empty.
    pub fn new(config: CasConfig) -> Result<Self, CasError> {
        if config.server_url.trim().is_empty() {
            return Err(CasError::Config("server_url is required".into()));
        }
        if config.client_id.trim().is_empty() {
            return Err(CasError::Config("client_id is required".into()));
        }
        if config.client_secret.trim().is_empty() {
            return Err(CasError::Config("client_secret is required".into()));
        }

        let base = config.server_url.trim_end_matches('/').to_string();
        // The browser login redirect prefers the public origin; fall back to the
        // internal base when no public_url is configured (single-url local dev).
        let public_base = if config.public_url.trim().is_empty() {
            base.clone()
        } else {
            config.public_url.trim_end_matches('/').to_string()
        };
        let http = reqwest::Client::builder()
            .timeout(std::time::Duration::from_secs(30))
            .build()?;

        Ok(Self {
            config,
            http,
            base,
            public_base,
        })
    }

    /// Accessor for the configured client id (handy for templates/logging).
    pub fn client_id(&self) -> &str {
        &self.config.client_id
    }

    /// Accessor for the configured callback URL.
    pub fn callback_url(&self) -> &str {
        &self.config.callback_url
    }

    /// Build the CAS SSO login URL: `{CAS_PUBLIC_BASE}/sso/login?client_id=ID`.
    ///
    /// This is a BROWSER redirect, so it is built from the public-facing base
    /// (`public_url` when set, otherwise `server_url`) — never the internal
    /// back-channel host, which the browser cannot reach in a split-horizon
    /// deployment.
    ///
    /// Per the protocol, only `client_id` is sent — the CAS server looks up the
    /// client's registered callback URL and appends the token to it.
    pub fn login_url(&self) -> Result<String, CasError> {
        let mut u = Url::parse(&format!("{}/sso/login", self.public_base))?;
        u.query_pairs_mut()
            .append_pair("client_id", &self.config.client_id);
        Ok(u.to_string())
    }

    /// Validate a single-use CAS token **server-to-server**.
    ///
    /// POSTs `{token, client_id, client_secret}` to
    /// `{CAS_BASE}/api/validate-token`. On a `200` response with
    /// `{"valid": true, "user": {...}}` the parsed [`CasUser`] is returned.
    /// Any non-200 status, `valid != true`, or missing user yields `Ok(None)`.
    /// Transport-level failures return [`CasError::Http`].
    ///
    /// The token is single-use: call this exactly once per callback, then store
    /// the returned user in the app's own session.
    pub async fn validate_token(&self, token: &str) -> Result<Option<CasUser>, CasError> {
        let body = ValidateRequest {
            token,
            client_id: &self.config.client_id,
            client_secret: &self.config.client_secret,
        };

        let resp = self
            .http
            .post(format!("{}/api/validate-token", self.base))
            .header("X-Client-ID", &self.config.client_id)
            .json(&body)
            .send()
            .await?;

        if !resp.status().is_success() {
            return Ok(None);
        }

        let parsed: ValidateResponse = match resp.json().await {
            Ok(v) => v,
            Err(_) => return Ok(None),
        };

        if parsed.valid {
            if let Some(user) = parsed.user {
                let _ = parsed.expires_at; // available if the caller wants it later
                return Ok(Some(user));
            }
        }
        Ok(None)
    }

    /// Best-effort logout notification to the CAS server (`POST /api/logout`).
    ///
    /// The app should clear its own session regardless of the result; this is
    /// purely advisory. Errors are swallowed into `Ok(false)`.
    pub async fn logout(&self) -> Result<bool, CasError> {
        match self
            .http
            .post(format!("{}/api/logout", self.base))
            .send()
            .await
        {
            Ok(resp) => Ok(resp.status().is_success()),
            Err(_) => Ok(false),
        }
    }
}

#[cfg(test)]
mod tests {
    use super::*;

    fn client() -> CasClient {
        CasClient::new(CasConfig {
            server_url: "http://one-system-cas/".into(),
            public_url: "".into(),
            client_id: "rust-sample".into(),
            client_secret: "secret".into(),
            callback_url: "http://localhost:9111/cas/callback".into(),
        })
        .unwrap()
    }

    #[test]
    fn login_url_has_client_id_and_strips_trailing_slash() {
        // No public_url configured -> falls back to the server_url base.
        let url = client().login_url().unwrap();
        assert_eq!(url, "http://one-system-cas/sso/login?client_id=rust-sample");
    }

    #[test]
    fn login_url_prefers_public_url_when_set() {
        let c = CasClient::new(CasConfig {
            server_url: "http://one-system-cas".into(),
            public_url: "http://161.97.158.202:91/".into(),
            client_id: "rust-sample".into(),
            client_secret: "secret".into(),
            callback_url: "http://localhost:9111/cas/callback".into(),
        })
        .unwrap();
        // Browser redirect uses the PUBLIC base (trailing slash stripped)...
        assert_eq!(
            c.login_url().unwrap(),
            "http://161.97.158.202:91/sso/login?client_id=rust-sample"
        );
        // ...while server-to-server calls keep using the internal base.
        assert_eq!(c.base, "http://one-system-cas");
    }

    #[test]
    fn empty_config_is_rejected() {
        let err = CasClient::new(CasConfig {
            server_url: "".into(),
            public_url: "".into(),
            client_id: "x".into(),
            client_secret: "y".into(),
            callback_url: "z".into(),
        });
        assert!(err.is_err());
    }
}

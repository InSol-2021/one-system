"""
Minimal Flask sample app for the One System CAS client SDK (cas-system-client).

This sample proves the `cas-system-client` package works end-to-end AND now ships its
own local username/password accounts (backed by SQLite) so a user can sign in
EITHER way:

    CAS Single-Sign-On (unchanged):
        /login (GET, no form post) -> redirect to the CAS server
        /callback                  -> validate the single-use token via the package,
                                       then create our OWN Flask session
    Local username/password (new):
        /login (GET)               -> render a small login form
        /login (POST, form)        -> validate against SQLite, create our OWN
                                       Flask session, redirect to home
        /login (POST, JSON w/      -> CAS link-validation contract: return
                client_validation)    {"success": true|false} WITHOUT creating a
                                       browser session
    Shared:
        /          -> show the signed-in user (local OR CAS) or the login options
        /logout    -> clear our local session (and notify the CAS server)

Everything that touches the CAS protocol still goes through the `CasClient`
class from the local `cas-system-client` package -- we do NOT hand-roll any CAS HTTP
calls here. The JWT is HS256 and is validated by the CAS server; this client
never holds the JWT secret. The `client_secret` is only ever used server-side
(here), never sent to the browser.

The local user store is a tiny SQLite file. Passwords are HASHED with Werkzeug's
PBKDF2 hasher (Werkzeug ships with Flask, so this adds no new dependency). Two
demo users are seeded on first run if the table is empty.
"""

import os
import sqlite3

# Load variables from a local .env file if present (optional convenience).
# python-dotenv is listed in requirements.txt; if it is missing we simply rely
# on the real environment, so the app still runs (e.g. in the container).
try:
    from dotenv import load_dotenv

    load_dotenv()
except ImportError:
    pass

from flask import (
    Flask,
    jsonify,
    redirect,
    request,
    session,
    url_for,
)

# Werkzeug ships with Flask -- use its standard password hasher so we do NOT add
# a new dependency just to hash the local demo passwords.
from werkzeug.security import check_password_hash, generate_password_hash

# The public API of the local package (see packages/python-cas-client/cas_client).
# `CasClient` exposes get_login_url(), validate_token(), logout(), etc.
from cas_client import CasClient


# ---------------------------------------------------------------------------
# Configuration (everything comes from the environment / .env)
# ---------------------------------------------------------------------------
CAS_BASE_URL = os.environ.get("CAS_BASE_URL", "http://localhost:8080")
# Public, browser-facing CAS base used ONLY for the login redirect. In a
# split-horizon deployment the browser reaches CAS at a public host while this
# server reaches it at the internal CAS_BASE_URL. Falls back to CAS_BASE_URL
# when unset so single-url local dev keeps working unchanged.
CAS_PUBLIC_URL = os.environ.get("CAS_PUBLIC_URL", "")
CAS_CLIENT_ID = os.environ.get("CAS_CLIENT_ID", "")
CAS_CLIENT_SECRET = os.environ.get("CAS_CLIENT_SECRET", "")
CAS_CALLBACK_URL = os.environ.get(
    "CAS_CALLBACK_URL", "http://localhost:9104/callback"
)
APP_PORT = int(os.environ.get("PORT", os.environ.get("APP_PORT", "9104")))

# Path to the local SQLite user store. Defaults to ./data/app.db so it lives in
# a single writable directory that the Dockerfile prepares at build time.
DB_PATH = os.environ.get(
    "APP_DB_PATH",
    os.path.join(os.path.dirname(os.path.abspath(__file__)), "data", "app.db"),
)

# Demo accounts seeded on first startup (only when the users table is empty).
# These are the exact credentials a tester / the CAS server can use.
DEMO_USERS = [
    ("rajan", "rajan123"),
    ("demo", "demo123"),
]

app = Flask(__name__)
# Secret key for signing the *app's own* Flask session cookie. This is unrelated
# to the CAS JWT secret -- it just protects our local session.
app.secret_key = os.environ.get("FLASK_SECRET_KEY", "dev-only-change-me")

# Build the CAS client once. The constructor maps directly to the package's
# public signature: server_url / client_id / client_secret / callback_url.
cas = CasClient(
    server_url=CAS_BASE_URL,
    client_id=CAS_CLIENT_ID,
    client_secret=CAS_CLIENT_SECRET,
    callback_url=CAS_CALLBACK_URL,
    # Browser login redirect is built from the public host; server-to-server
    # validation/logout keep using server_url (CAS_BASE_URL). Empty -> falls
    # back to server_url inside the package.
    public_url=CAS_PUBLIC_URL,
)


# ---------------------------------------------------------------------------
# Local user store (SQLite via the stdlib `sqlite3`)
# ---------------------------------------------------------------------------
def _connect() -> sqlite3.Connection:
    """Open a connection to the local SQLite file (creating the dir if needed)."""
    os.makedirs(os.path.dirname(DB_PATH), exist_ok=True)
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db() -> None:
    """Create the users table if missing and seed demo accounts when empty."""
    conn = _connect()
    try:
        conn.execute(
            """
            CREATE TABLE IF NOT EXISTS users (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                username      TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL
            )
            """
        )
        count = conn.execute("SELECT COUNT(*) AS n FROM users").fetchone()["n"]
        if count == 0:
            conn.executemany(
                "INSERT INTO users (username, password_hash) VALUES (?, ?)",
                [
                    (username, generate_password_hash(password))
                    for username, password in DEMO_USERS
                ],
            )
        conn.commit()
    finally:
        conn.close()


def verify_local_user(username: str, password: str):
    """Return the matching user row dict if credentials are valid, else None."""
    if not username or not password:
        return None
    conn = _connect()
    try:
        row = conn.execute(
            "SELECT id, username, password_hash FROM users WHERE username = ?",
            (username,),
        ).fetchone()
    finally:
        conn.close()
    if row and check_password_hash(row["password_hash"], password):
        return {"id": row["id"], "username": row["username"]}
    return None


# Initialize the store at import time so it is ready under gunicorn too.
init_db()


# ---------------------------------------------------------------------------
# Session helpers (the local user and the CAS user share ONE Flask session)
# ---------------------------------------------------------------------------
def current_user():
    """Return the signed-in user (local OR CAS), tagged with its auth source."""
    local = session.get("local_user")
    if local:
        return {**local, "auth": "local"}
    cas_user = session.get("cas_user")
    if cas_user:
        return {**cas_user, "auth": "cas"}
    return None


# ---------------------------------------------------------------------------
# Routes
# ---------------------------------------------------------------------------
@app.route("/")
def home():
    """Show the signed-in user (local or CAS), or the login options."""
    user = current_user()
    if not user:
        return _page(
            "<h1>One System CAS &mdash; Python / Flask sample</h1>"
            "<p>You are not logged in.</p>"
            f'<p><a class="btn" href="{url_for("login")}">Login</a></p>'
        )

    source = "CAS Single-Sign-On" if user["auth"] == "cas" else "a local account"
    return _page(
        "<h1>One System CAS &mdash; Python / Flask sample</h1>"
        f"<p>You are signed in via <strong>{source}</strong>.</p>"
        "<table>"
        f"<tr><th>ID</th><td>{user.get('id', '')}</td></tr>"
        f"<tr><th>Username</th><td>{user.get('username', '')}</td></tr>"
        f"<tr><th>Email</th><td>{user.get('email', '') or '&mdash;'}</td></tr>"
        f"<tr><th>Auth source</th><td>{user['auth']}</td></tr>"
        "</table>"
        f'<p><a class="btn" href="{url_for("logout")}">Logout</a></p>'
    )


@app.route("/login", methods=["GET", "POST"])
def login():
    """One route, three jobs:

    1. GET                      -> render the local username/password form, with
                                   a separate button to start the CAS SSO flow.
    2. POST + `client_validation` (JSON or form, typically from the CAS server)
                                -> validate credentials and return JSON
                                   {"success": true|false}; do NOT create a
                                   browser session.
    3. POST (browser form)      -> validate credentials, create the app's OWN
                                   local Flask session, redirect to home.
    """
    if request.method == "GET":
        return _login_page()

    # Accept either a JSON body or a classic form POST.
    data = request.get_json(silent=True) or request.form
    username = (data.get("username") or "").strip()
    password = data.get("password") or ""

    # --- CAS link-validation contract -------------------------------------
    # The CAS server posts {"username", "password", "client_validation": true}.
    # Detect it by the presence of `client_validation` (or an explicit JSON
    # Accept header) and answer with JSON ONLY -- never a browser session.
    wants_json = "client_validation" in data or request.accept_mimetypes.best == (
        "application/json"
    )
    if "client_validation" in data:
        user = verify_local_user(username, password)
        if user:
            return jsonify({"success": True}), 200
        return jsonify({"success": False}), 401

    # --- Browser login ----------------------------------------------------
    user = verify_local_user(username, password)
    if not user:
        if wants_json:
            return jsonify({"success": False}), 401
        return _login_page(error="Invalid username or password."), 401

    # Create the app's OWN session (the SAME session used by the CAS callback).
    # Clear any stale CAS user so the two sources never collide.
    session.pop("cas_user", None)
    session.pop("cas_token", None)
    session["local_user"] = user
    return redirect(url_for("home"))


@app.route("/cas/login")
def cas_login():
    """Start the CAS SSO flow (the original `/login` GET behavior).

    `cas.get_login_url()` returns `{CAS_BASE_URL}/sso/login?client_id=...`.
    The CAS server authenticates the user and 302-redirects the browser back to
    this app's REGISTERED callback URL with `?token=<JWT>` appended.
    """
    return redirect(cas.get_login_url())


@app.route("/callback")
def callback():
    """Handle the CAS redirect, validate the token, create our session.

    The token arrives as a query param. It is SINGLE-USE, so we validate it
    exactly once (server-to-server, using the client_secret) and then store the
    resulting user in our own Flask session.
    """
    token = request.args.get("token")
    if not token:
        return _page("<h1>Login failed</h1><p>No token in callback.</p>"), 400

    # SERVER-TO-SERVER validation via the package. Under the hood this POSTs to
    # {CAS_BASE_URL}/api/sso/validate with {token, client_id, client_secret} and
    # returns the user dict ({id, username, email, ...}) on success, or None.
    user = cas.validate_token(token)
    if not user:
        return _page(
            "<h1>Login failed</h1>"
            "<p>The CAS server rejected the token (invalid, expired, or already "
            "used).</p>"
            f'<p><a class="btn" href="{url_for("login")}">Try again</a></p>'
        ), 401

    # Create the app's OWN session. From here on we trust our session cookie, not
    # the (now-consumed) one-time JWT. Clear any local user so sources don't mix.
    session.pop("local_user", None)
    session["cas_user"] = user
    session["cas_token"] = token  # kept only so we can notify CAS on logout
    return redirect(url_for("home"))


@app.route("/logout")
def logout():
    """Clear our local session and (best-effort) tell the CAS server."""
    token = session.get("cas_token")
    if token:
        # cas.logout() clears the package's token cache and POSTs {CAS_BASE_URL}/api/logout.
        cas.logout(token)
    session.clear()
    return redirect(url_for("home"))


# ---------------------------------------------------------------------------
# Tiny HTML helpers (kept inline so the sample stays a single readable file)
# ---------------------------------------------------------------------------
def _page(body: str) -> str:
    return (
        "<!doctype html><html><head><meta charset='utf-8'>"
        "<title>CAS Python sample</title>"
        "<style>"
        "body{font-family:system-ui,sans-serif;max-width:640px;margin:48px auto;padding:0 16px;color:#1e293b}"
        ".btn{display:inline-block;background:#2563eb;color:#fff;padding:8px 16px;"
        "border-radius:6px;text-decoration:none;border:0;cursor:pointer;font-size:14px}"
        ".btn.secondary{background:#475569}"
        "table{border-collapse:collapse;margin:16px 0}"
        "th,td{border:1px solid #e2e8f0;padding:6px 12px;text-align:left}"
        "th{background:#f8fafc}"
        "form{margin:16px 0}"
        "label{display:block;margin:10px 0 4px;font-size:14px;font-weight:600}"
        "input{width:100%;box-sizing:border-box;padding:8px 10px;border:1px solid #cbd5e1;"
        "border-radius:6px;font-size:14px}"
        ".error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:8px 12px;"
        "border-radius:6px;margin:12px 0;font-size:14px}"
        ".hint{color:#64748b;font-size:13px}"
        ".divider{margin:24px 0;border:0;border-top:1px solid #e2e8f0}"
        "</style></head><body>" + body + "</body></html>"
    )


def _login_page(error: str = "") -> str:
    error_html = f'<div class="error">{error}</div>' if error else ""
    return _page(
        "<h1>Sign in</h1>"
        f"{error_html}"
        f'<form method="post" action="{url_for("login")}">'
        '<label for="username">Username</label>'
        '<input id="username" name="username" autocomplete="username" autofocus>'
        '<label for="password">Password</label>'
        '<input id="password" name="password" type="password" '
        'autocomplete="current-password">'
        '<p><button class="btn" type="submit">Sign in</button></p>'
        "</form>"
        '<p class="hint">Demo accounts: <code>rajan / rajan123</code> &middot; '
        "<code>demo / demo123</code></p>"
        '<hr class="divider">'
        "<p>Or use Single-Sign-On:</p>"
        f'<p><a class="btn secondary" href="{url_for("cas_login")}">'
        "Login with CAS</a></p>"
    )


if __name__ == "__main__":
    # 0.0.0.0 so the container-mapped port is reachable in the unified deployment.
    app.run(host="0.0.0.0", port=APP_PORT, debug=True)

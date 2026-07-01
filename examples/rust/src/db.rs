//! Local user store backed by SQLite (rusqlite, bundled).
//!
//! Schema: `users(id INTEGER PK, username TEXT UNIQUE, password_hash TEXT)`.
//! Passwords are hashed with Argon2 (salted). On startup we seed two demo users
//! if the table is empty.

use std::sync::{Arc, Mutex};

use argon2::password_hash::{
    rand_core::OsRng, PasswordHash, PasswordHasher, PasswordVerifier, SaltString,
};
use argon2::Argon2;
use rusqlite::Connection;

/// Thread-safe handle to the SQLite connection. The sample is low-traffic, so a
/// single mutex-guarded connection is the simplest correct choice.
#[derive(Clone)]
pub struct Db {
    conn: Arc<Mutex<Connection>>,
}

/// A locally-authenticated user.
#[derive(Debug, Clone)]
pub struct LocalUser {
    pub id: i64,
    pub username: String,
}

impl Db {
    /// Open (or create) the SQLite database at `path`, ensure the schema exists,
    /// and seed the demo users if the table is empty.
    pub fn init(path: &str) -> Result<Self, rusqlite::Error> {
        let conn = Connection::open(path)?;
        conn.execute(
            "CREATE TABLE IF NOT EXISTS users (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                username      TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL
            )",
            [],
        )?;

        let db = Db {
            conn: Arc::new(Mutex::new(conn)),
        };
        db.seed_if_empty()?;
        Ok(db)
    }

    /// Seed the two demo users (rajan/rajan123, demo/demo123) when the table has
    /// no rows. Idempotent: a populated table is left untouched.
    fn seed_if_empty(&self) -> Result<(), rusqlite::Error> {
        let conn = self.conn.lock().unwrap();
        let count: i64 = conn.query_row("SELECT COUNT(*) FROM users", [], |r| r.get(0))?;
        if count > 0 {
            return Ok(());
        }
        for (username, password) in [("rajan", "rajan123"), ("demo", "demo123")] {
            let hash = hash_password(password);
            conn.execute(
                "INSERT INTO users (username, password_hash) VALUES (?1, ?2)",
                rusqlite::params![username, hash],
            )?;
        }
        tracing::info!("Seeded demo users: rajan/rajan123, demo/demo123");
        Ok(())
    }

    /// Validate a username/password against the store. Returns the matched user
    /// on success, `None` on unknown username or bad password.
    pub fn verify_credentials(&self, username: &str, password: &str) -> Option<LocalUser> {
        let conn = self.conn.lock().unwrap();
        let row: Result<(i64, String), _> = conn.query_row(
            "SELECT id, password_hash FROM users WHERE username = ?1",
            rusqlite::params![username],
            |r| Ok((r.get(0)?, r.get(1)?)),
        );

        match row {
            Ok((id, password_hash)) if verify_password(password, &password_hash) => {
                Some(LocalUser {
                    id,
                    username: username.to_string(),
                })
            }
            _ => None,
        }
    }
}

/// Hash a plaintext password with a fresh random salt (Argon2id).
fn hash_password(password: &str) -> String {
    let salt = SaltString::generate(&mut OsRng);
    Argon2::default()
        .hash_password(password.as_bytes(), &salt)
        .expect("argon2 hashing should not fail")
        .to_string()
}

/// Verify a plaintext password against a stored PHC-format Argon2 hash.
fn verify_password(password: &str, stored: &str) -> bool {
    match PasswordHash::new(stored) {
        Ok(parsed) => Argon2::default()
            .verify_password(password.as_bytes(), &parsed)
            .is_ok(),
        Err(_) => false,
    }
}

/**
 * Local user store for the Angular sample (SQLite via better-sqlite3).
 *
 * This turns the demo into a REAL app with its OWN username/password accounts
 * that live ALONGSIDE the existing CAS SSO flow. The CAS flow is untouched;
 * this module only owns the local-credential side.
 *
 * Layout:
 *   - A `users` table: (id, username UNIQUE, password_hash).
 *   - Passwords are stored as a salted scrypt hash ("salt:derivedKeyHex"),
 *     computed with Node's built-in `crypto` — no extra hashing dependency.
 *   - Two demo users are SEEDED on startup if the table is empty.
 *
 * The DB file lives at ./data/app.db (relative to the example root), which the
 * Dockerfile creates as a writable directory at runtime.
 */
'use strict';

const path = require('path');
const fs = require('fs');
const crypto = require('crypto');
const Database = require('better-sqlite3');

// ── DB file location (writable at runtime) ──────────────────────────────────
// Allow override via APP_DB_PATH; default to <example>/data/app.db.
const DB_PATH =
  process.env.APP_DB_PATH || path.join(__dirname, '..', 'data', 'app.db');

// Ensure the parent directory exists (e.g. ./data) so SQLite can create the file.
fs.mkdirSync(path.dirname(DB_PATH), { recursive: true });

const db = new Database(DB_PATH);
db.pragma('journal_mode = WAL');

// ── Schema ──────────────────────────────────────────────────────────────────
db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL
  );
`);

// ── Password hashing (salted scrypt) ────────────────────────────────────────
/** Hash a plaintext password → "saltHex:derivedKeyHex". */
function hashPassword(password) {
  const salt = crypto.randomBytes(16).toString('hex');
  const derived = crypto.scryptSync(password, salt, 64).toString('hex');
  return `${salt}:${derived}`;
}

/** Constant-time verify of a plaintext password against a stored hash. */
function verifyPassword(password, stored) {
  if (typeof stored !== 'string' || !stored.includes(':')) {
    return false;
  }
  const [salt, expectedHex] = stored.split(':');
  const expected = Buffer.from(expectedHex, 'hex');
  const actual = crypto.scryptSync(password, salt, expected.length);
  return (
    expected.length === actual.length &&
    crypto.timingSafeEqual(expected, actual)
  );
}

// ── Seed two demo users if the table is empty ───────────────────────────────
const DEMO_USERS = [
  { username: 'rajan', password: 'rajan123' },
  { username: 'demo', password: 'demo123' },
];

function seedIfEmpty() {
  const { count } = db.prepare('SELECT COUNT(*) AS count FROM users').get();
  if (count > 0) {
    return;
  }
  const insert = db.prepare(
    'INSERT INTO users (username, password_hash) VALUES (?, ?)',
  );
  const seed = db.transaction((users) => {
    for (const u of users) {
      insert.run(u.username, hashPassword(u.password));
    }
  });
  seed(DEMO_USERS);
  console.log(
    `  Seeded ${DEMO_USERS.length} demo users: ` +
      DEMO_USERS.map((u) => u.username).join(', '),
  );
}

seedIfEmpty();

// ── Public helpers ──────────────────────────────────────────────────────────
/**
 * Validate a username/password pair against the local store.
 * @returns {{ id:number, username:string } | null} the user on success, else null.
 */
function authenticate(username, password) {
  if (!username || !password) {
    return null;
  }
  const row = db
    .prepare('SELECT id, username, password_hash FROM users WHERE username = ?')
    .get(String(username));
  if (!row) {
    return null;
  }
  if (!verifyPassword(String(password), row.password_hash)) {
    return null;
  }
  return { id: row.id, username: row.username };
}

module.exports = {
  db,
  DB_PATH,
  authenticate,
  hashPassword,
  verifyPassword,
};

/**
 * Local user store (SQLite) for the JavaScript CAS sample.
 *
 * This turns the demo into a REAL app with its OWN local username/password
 * accounts, alongside the existing CAS Single-Sign-On flow.
 *
 *   - Uses better-sqlite3 (the idiomatic synchronous SQLite library for Node).
 *   - The DB file lives at ./data/app.db (override with APP_DB_PATH). The data
 *     directory must be WRITABLE at runtime (see the Dockerfile).
 *   - Passwords are stored HASHED with a per-user random salt using Node's
 *     built-in scrypt KDF (no native crypto deps beyond what Node ships).
 *   - Two demo users are SEEDED on first startup if the table is empty.
 */

'use strict';

const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const Database = require('better-sqlite3');

// Where the SQLite file lives. Default ./data/app.db (writable at runtime).
const DB_PATH = process.env.APP_DB_PATH || path.join(__dirname, 'data', 'app.db');

// Ensure the parent directory exists (so a fresh container/checkout works).
fs.mkdirSync(path.dirname(DB_PATH), { recursive: true });

const db = new Database(DB_PATH);
db.pragma('journal_mode = WAL');

db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL
  );
`);

// --- Password hashing (salted scrypt; format: scrypt$<saltHex>$<hashHex>) ---

function hashPassword(password) {
  const salt = crypto.randomBytes(16);
  const derived = crypto.scryptSync(password, salt, 64);
  return `scrypt$${salt.toString('hex')}$${derived.toString('hex')}`;
}

function verifyPassword(password, stored) {
  const parts = String(stored || '').split('$');
  if (parts.length !== 3 || parts[0] !== 'scrypt') return false;
  const salt = Buffer.from(parts[1], 'hex');
  const expected = Buffer.from(parts[2], 'hex');
  const derived = crypto.scryptSync(password, salt, expected.length);
  // Constant-time comparison to avoid timing leaks.
  return expected.length === derived.length && crypto.timingSafeEqual(expected, derived);
}

// --- Seed two demo users on startup if the table is empty ---

const DEMO_USERS = [
  { username: 'rajan', password: 'rajan123' },
  { username: 'demo', password: 'demo123' },
];

function seedIfEmpty() {
  const { count } = db.prepare('SELECT COUNT(*) AS count FROM users').get();
  if (count > 0) return;
  const insert = db.prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
  const seed = db.transaction((rows) => {
    for (const u of rows) insert.run(u.username, hashPassword(u.password));
  });
  seed(DEMO_USERS);
  console.log(`[local-auth] Seeded ${DEMO_USERS.length} demo users into ${DB_PATH}`);
}

seedIfEmpty();

// --- Public API ---

/**
 * Validate a username/password pair against the local store.
 * Returns a plain user object { id, username } on success, or null on failure.
 */
function authenticate(username, password) {
  if (!username || !password) return null;
  const row = db
    .prepare('SELECT id, username, password_hash FROM users WHERE username = ?')
    .get(username);
  if (!row) return null;
  if (!verifyPassword(password, row.password_hash)) return null;
  return { id: row.id, username: row.username };
}

module.exports = {
  db,
  authenticate,
  hashPassword,
  verifyPassword,
  DB_PATH,
};

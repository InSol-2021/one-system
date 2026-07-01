/**
 * Local user store (SQLite) for the One System CAS Node.js sample.
 * ---------------------------------------------------------------------------
 * This adds the app's OWN local username/password accounts, independent of the
 * CAS SSO flow. It uses better-sqlite3 (synchronous, embedded) and stores a
 * SALTED scrypt hash of each password — never the plaintext.
 *
 * Schema:  users(id, username UNIQUE, password_hash)
 *
 * On startup we open (creating if needed) a SQLite file and, if the users table
 * is empty, SEED two demo accounts:
 *     rajan / rajan123      demo / demo123
 */

const path = require('path');
const fs = require('fs');
const crypto = require('crypto');
const Database = require('better-sqlite3');

// ---------------------------------------------------------------------------
// Password hashing: salted scrypt, using only Node's built-in crypto.
// Stored format:  scrypt$<saltHex>$<hashHex>
// ---------------------------------------------------------------------------
function hashPassword(password) {
  const salt = crypto.randomBytes(16);
  const derived = crypto.scryptSync(String(password), salt, 64);
  return `scrypt$${salt.toString('hex')}$${derived.toString('hex')}`;
}

function verifyPassword(password, stored) {
  if (typeof stored !== 'string') return false;
  const parts = stored.split('$');
  if (parts.length !== 3 || parts[0] !== 'scrypt') return false;

  const salt = Buffer.from(parts[1], 'hex');
  const expected = Buffer.from(parts[2], 'hex');
  let derived;
  try {
    derived = crypto.scryptSync(String(password), salt, expected.length);
  } catch (_) {
    return false;
  }
  // Constant-time comparison to avoid timing leaks.
  return expected.length === derived.length &&
    crypto.timingSafeEqual(expected, derived);
}

// ---------------------------------------------------------------------------
// Demo accounts seeded on first run (when the users table is empty).
// ---------------------------------------------------------------------------
const DEMO_USERS = [
  { username: 'rajan', password: 'rajan123' },
  { username: 'demo', password: 'demo123' },
];

/**
 * Open (and initialize) the SQLite user store.
 * @param {string} [dbFile] - Path to the SQLite file. Defaults to ./data/app.db
 *                            (overridable via the DB_PATH env var).
 * @returns {{ findByUsername: Function, verifyCredentials: Function, db: object }}
 */
function initDb(dbFile) {
  const filePath = dbFile ||
    process.env.DB_PATH ||
    path.join(__dirname, 'data', 'app.db');

  // Ensure the directory exists and is writable at runtime.
  fs.mkdirSync(path.dirname(filePath), { recursive: true });

  const db = new Database(filePath);
  db.pragma('journal_mode = WAL');

  db.exec(`
    CREATE TABLE IF NOT EXISTS users (
      id            INTEGER PRIMARY KEY AUTOINCREMENT,
      username      TEXT UNIQUE NOT NULL,
      password_hash TEXT NOT NULL
    )
  `);

  // Seed demo users only when the table is empty.
  const { count } = db.prepare('SELECT COUNT(*) AS count FROM users').get();
  if (count === 0) {
    const insert = db.prepare(
      'INSERT INTO users (username, password_hash) VALUES (?, ?)'
    );
    const seed = db.transaction((users) => {
      for (const u of users) insert.run(u.username, hashPassword(u.password));
    });
    seed(DEMO_USERS);
    console.log(
      `[db] Seeded ${DEMO_USERS.length} demo users: ` +
      DEMO_USERS.map((u) => u.username).join(', ')
    );
  }

  const selectByUsername = db.prepare(
    'SELECT id, username, password_hash FROM users WHERE username = ?'
  );

  function findByUsername(username) {
    if (!username) return null;
    return selectByUsername.get(String(username)) || null;
  }

  /**
   * Validate a username/password pair against the store.
   * @returns {{id:number, username:string}|null} The user (without hash) or null.
   */
  function verifyCredentials(username, password) {
    const row = findByUsername(username);
    if (!row) return null; // no such user
    if (!verifyPassword(password, row.password_hash)) return null;
    return { id: row.id, username: row.username };
  }

  return { db, findByUsername, verifyCredentials };
}

module.exports = { initDb, hashPassword, verifyPassword, DEMO_USERS };

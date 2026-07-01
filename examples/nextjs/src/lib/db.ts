/**
 * @module lib/db
 * @description Local user store backed by SQLite (better-sqlite3).
 *
 * This is the piece that turns the CAS *demo* into a *real* app: in addition to
 * Single-Sign-On via CAS, the app now has its OWN local username/password
 * accounts. They live in a SQLite file on disk (default `./data/app.db`) and are
 * checked by the `/api/login` route.
 *
 * Password hashing uses Node's built-in `crypto.scrypt` (a memory-hard KDF) with
 * a per-user random salt — so we add ZERO native build dependencies beyond
 * better-sqlite3 itself, and never store plaintext passwords.
 *
 * The DB handle and schema are created lazily on first use (singleton), and two
 * demo users are seeded if the table is empty:
 *
 *   rajan / rajan123
 *   demo  / demo123
 */

import Database from 'better-sqlite3';
import { randomBytes, scryptSync, timingSafeEqual } from 'node:crypto';
import { existsSync, mkdirSync } from 'node:fs';
import path from 'node:path';

// ---------------------------------------------------------------------------
// Types
// ---------------------------------------------------------------------------

/** A row from the `users` table. */
export interface UserRow {
  id: number;
  username: string;
  password_hash: string;
}

// ---------------------------------------------------------------------------
// Password hashing (scrypt + per-user salt) — format: "scrypt$<saltHex>$<hashHex>"
// ---------------------------------------------------------------------------

const SCRYPT_KEYLEN = 64;

/** Hash a plaintext password with a fresh random salt. */
export function hashPassword(plain: string): string {
  const salt = randomBytes(16);
  const derived = scryptSync(plain, salt, SCRYPT_KEYLEN);
  return `scrypt$${salt.toString('hex')}$${derived.toString('hex')}`;
}

/** Verify a plaintext password against a stored "scrypt$salt$hash" string. */
export function verifyPassword(plain: string, stored: string): boolean {
  try {
    const [scheme, saltHex, hashHex] = stored.split('$');
    if (scheme !== 'scrypt' || !saltHex || !hashHex) return false;

    const salt = Buffer.from(saltHex, 'hex');
    const expected = Buffer.from(hashHex, 'hex');
    const derived = scryptSync(plain, salt, expected.length);

    // Constant-time comparison to avoid leaking timing information.
    return derived.length === expected.length && timingSafeEqual(derived, expected);
  } catch {
    return false;
  }
}

// ---------------------------------------------------------------------------
// Database singleton
// ---------------------------------------------------------------------------

/**
 * Path to the SQLite file. Override with APP_DB_PATH (the Docker image sets a
 * writable location). Defaults to ./data/app.db relative to the process cwd.
 */
function dbFilePath(): string {
  return process.env.APP_DB_PATH ?? path.join(process.cwd(), 'data', 'app.db');
}

let _db: Database.Database | null = null;

/**
 * Return the shared SQLite connection, creating the file, directory, schema,
 * and seed data on first call.
 */
export function getDb(): Database.Database {
  if (_db) return _db;

  const file = dbFilePath();
  const dir = path.dirname(file);
  if (!existsSync(dir)) {
    mkdirSync(dir, { recursive: true });
  }

  const db = new Database(file);
  db.pragma('journal_mode = WAL');

  db.exec(`
    CREATE TABLE IF NOT EXISTS users (
      id            INTEGER PRIMARY KEY AUTOINCREMENT,
      username      TEXT NOT NULL UNIQUE,
      password_hash TEXT NOT NULL
    );
  `);

  seedDemoUsers(db);

  _db = db;
  return _db;
}

/** Demo accounts seeded on first startup when the users table is empty. */
const DEMO_USERS: ReadonlyArray<{ username: string; password: string }> = [
  { username: 'rajan', password: 'rajan123' },
  { username: 'demo', password: 'demo123' },
];

/** Insert the demo users if (and only if) the table is currently empty. */
function seedDemoUsers(db: Database.Database): void {
  const { count } = db.prepare('SELECT COUNT(*) AS count FROM users').get() as {
    count: number;
  };
  if (count > 0) return;

  const insert = db.prepare(
    'INSERT INTO users (username, password_hash) VALUES (?, ?)',
  );
  const seed = db.transaction(() => {
    for (const u of DEMO_USERS) {
      insert.run(u.username, hashPassword(u.password));
    }
  });
  seed();
}

// ---------------------------------------------------------------------------
// Queries
// ---------------------------------------------------------------------------

/** Look up a user by username, or `null` if not found. */
export function findUserByUsername(username: string): UserRow | null {
  const row = getDb()
    .prepare('SELECT id, username, password_hash FROM users WHERE username = ?')
    .get(username) as UserRow | undefined;
  return row ?? null;
}

/**
 * Validate a username + password pair against the local store.
 * @returns the matching {@link UserRow} on success, or `null` on failure.
 */
export function validateLocalCredentials(
  username: string,
  password: string,
): UserRow | null {
  if (!username || !password) return null;
  const user = findUserByUsername(username);
  if (!user) return null;
  return verifyPassword(password, user.password_hash) ? user : null;
}

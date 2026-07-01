// -----------------------------------------------------------------------------
// Local user store (SQLite via better-sqlite3).
//
// This is the "real application" half of the sample: alongside CAS SSO, the app
// now has its OWN local username/password accounts. Credentials are stored in a
// SQLite file on disk (./data/app.db by default) and validated server-side.
//
// On first boot (empty `users` table) we seed two demo accounts with HASHED
// passwords so the sample is usable out of the box:
//
//     rajan / rajan123
//     demo  / demo123
//
// Passwords are never stored in plain text. We use Node's built-in `scrypt`
// (a salted, memory-hard KDF) so there is no extra native crypto dependency.
// -----------------------------------------------------------------------------

import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';
import { mkdirSync } from 'node:fs';
import { randomBytes, scryptSync, timingSafeEqual } from 'node:crypto';
import Database from 'better-sqlite3';

const __dirname = dirname(fileURLToPath(import.meta.url));

// The data directory must be writable at runtime (see Dockerfile). Allow an
// override via APP_DB_PATH for deployments that mount a volume elsewhere.
const DB_PATH =
  process.env.APP_DB_PATH || resolve(__dirname, '..', 'data', 'app.db');

mkdirSync(dirname(DB_PATH), { recursive: true });

const db = new Database(DB_PATH);
db.pragma('journal_mode = WAL');

db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL
  );
`);

// ---------------------------------------------------------------------------
// Password hashing (salted scrypt, stored as "scrypt$<salt-hex>$<hash-hex>").
// ---------------------------------------------------------------------------
function hashPassword(password) {
  const salt = randomBytes(16);
  const derived = scryptSync(password, salt, 64);
  return `scrypt$${salt.toString('hex')}$${derived.toString('hex')}`;
}

function verifyPassword(password, stored) {
  try {
    const [scheme, saltHex, hashHex] = String(stored).split('$');
    if (scheme !== 'scrypt' || !saltHex || !hashHex) return false;
    const salt = Buffer.from(saltHex, 'hex');
    const expected = Buffer.from(hashHex, 'hex');
    const actual = scryptSync(password, salt, expected.length);
    return expected.length === actual.length && timingSafeEqual(expected, actual);
  } catch {
    return false;
  }
}

// ---------------------------------------------------------------------------
// Seed demo users on startup if the table is empty.
// ---------------------------------------------------------------------------
const DEMO_USERS = [
  { username: 'rajan', password: 'rajan123' },
  { username: 'demo', password: 'demo123' },
];

function seedDemoUsers() {
  const { count } = db.prepare('SELECT COUNT(*) AS count FROM users').get();
  if (count > 0) return;

  const insert = db.prepare(
    'INSERT INTO users (username, password_hash) VALUES (?, ?)',
  );
  const seed = db.transaction((users) => {
    for (const u of users) insert.run(u.username, hashPassword(u.password));
  });
  seed(DEMO_USERS);
  console.log(
    `[db] Seeded ${DEMO_USERS.length} demo users: ` +
      DEMO_USERS.map((u) => u.username).join(', '),
  );
}

seedDemoUsers();

// ---------------------------------------------------------------------------
// Public API.
// ---------------------------------------------------------------------------

/**
 * Validate a username/password pair against the local store.
 *
 * @returns A `{ id, username, email, roles }` user object on success, or `null`.
 */
export function authenticateLocalUser(username, password) {
  if (!username || !password) return null;

  const row = db
    .prepare('SELECT id, username, password_hash FROM users WHERE username = ?')
    .get(String(username));

  if (!row) return null;
  if (!verifyPassword(String(password), row.password_hash)) return null;

  // Shape the user like a CasUser so the rest of the app treats local and CAS
  // users identically (same session, same UI).
  return {
    id: row.id,
    username: row.username,
    email: `${row.username}@local`,
    roles: ['local-user'],
  };
}

export { DEMO_USERS };
export default db;

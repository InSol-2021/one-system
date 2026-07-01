<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;
use PDO;

/**
 * Tiny, self-contained LOCAL username/password store backed by SQLite.
 *
 * This sits *alongside* the CAS single-sign-on flow: a user can authenticate
 * EITHER with one of these local accounts OR via the CAS server. The CAS flow
 * (App\Http\Controllers\AuthController::login/callback) is untouched by this class.
 *
 * We talk to SQLite through PDO (the pdo_sqlite extension) rather than Eloquent
 * so the sample needs no `php artisan migrate` step — the table is created and
 * seeded lazily the first time the store is used. The DB file lives at
 *   database/app.db   (database_path('app.db'))
 * which the Dockerfile makes writable at runtime.
 *
 * Passwords are stored HASHED with Laravel's framework hasher (bcrypt by
 * default via Hash::make / Hash::check).
 */
class LocalUserStore
{
    /**
     * The two demo accounts seeded on first run. Keep these in sync with the
     * README and the sample's structured output. Plaintext here is ONLY used to
     * generate the stored hash at seed time.
     */
    public const DEMO_USERS = [
        ['username' => 'rajan', 'password' => 'rajan123'],
        ['username' => 'demo',  'password' => 'demo123'],
    ];

    private PDO $pdo;

    public function __construct(?string $path = null)
    {
        $path = $path ?: database_path('app.db');

        // Make sure the parent directory exists and is writable.
        $dir = dirname($path);
        if (! is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->migrate();
        $this->seed();
    }

    /**
     * Create the users table if it does not yet exist.
     */
    private function migrate(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                username      TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL
            )'
        );
    }

    /**
     * Seed the two demo users on startup IF the table is empty.
     */
    private function seed(): void
    {
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $insert = $this->pdo->prepare(
            'INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)'
        );

        foreach (self::DEMO_USERS as $user) {
            $insert->execute([
                ':username'      => $user['username'],
                ':password_hash' => Hash::make($user['password']),
            ]);
        }
    }

    /**
     * Validate a username/password pair against the local store.
     *
     * @return array{id:int,username:string}|null  The user (without the hash) on
     *         success, or null if the username is unknown or the password wrong.
     */
    public function validate(string $username, string $password): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1'
        );
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();

        if (! $row) {
            return null;
        }

        if (! Hash::check($password, $row['password_hash'])) {
            return null;
        }

        return [
            'id'       => (int) $row['id'],
            'username' => $row['username'],
        ];
    }
}

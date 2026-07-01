// ---------------------------------------------------------------------------
// Local user store — SQLite-backed username/password accounts.
//
// This gives the sample its OWN local accounts, independent of CAS SSO. It uses
// Microsoft.Data.Sqlite (the idiomatic SQLite library for .NET) and stores
// passwords as salted PBKDF2 hashes (the framework-standard KDF in
// System.Security.Cryptography — no extra package needed for hashing).
//
//   users(id INTEGER PK, username TEXT UNIQUE, password_hash TEXT)
//
// On startup the table is created if missing and, if empty, seeded with two
// demo users:  rajan/rajan123  and  demo/demo123.
// ---------------------------------------------------------------------------

using System.Security.Cryptography;
using Microsoft.Data.Sqlite;

namespace CasDemo;

/// <summary>
/// SQLite-backed local user store with PBKDF2 password hashing.
/// Registered as a singleton; opens short-lived connections per operation.
/// </summary>
public sealed class UserStore
{
    private readonly string _connectionString;

    // PBKDF2 parameters.
    private const int SaltBytes = 16;
    private const int HashBytes = 32;
    private const int Iterations = 100_000;
    private static readonly HashAlgorithmName Algo = HashAlgorithmName.SHA256;

    public UserStore(string databasePath)
    {
        // Ensure the directory for the SQLite file exists and is writable.
        var dir = Path.GetDirectoryName(Path.GetFullPath(databasePath));
        if (!string.IsNullOrEmpty(dir))
            Directory.CreateDirectory(dir);

        _connectionString = new SqliteConnectionStringBuilder
        {
            DataSource = databasePath,
            Mode = SqliteOpenMode.ReadWriteCreate,
        }.ToString();
    }

    /// <summary>Create the users table if absent and seed demo users when empty.</summary>
    public void InitializeAndSeed()
    {
        using var conn = Open();

        using (var create = conn.CreateCommand())
        {
            create.CommandText =
                "CREATE TABLE IF NOT EXISTS users (" +
                "  id INTEGER PRIMARY KEY AUTOINCREMENT," +
                "  username TEXT NOT NULL UNIQUE," +
                "  password_hash TEXT NOT NULL)";
            create.ExecuteNonQuery();
        }

        long count;
        using (var countCmd = conn.CreateCommand())
        {
            countCmd.CommandText = "SELECT COUNT(*) FROM users";
            count = (long)(countCmd.ExecuteScalar() ?? 0L);
        }

        if (count == 0)
        {
            InsertUser(conn, "rajan", "rajan123");
            InsertUser(conn, "demo", "demo123");
        }
    }

    /// <summary>
    /// Return true when <paramref name="username"/> exists and the password matches.
    /// </summary>
    public bool ValidateCredentials(string username, string password)
    {
        if (string.IsNullOrEmpty(username) || password is null)
            return false;

        using var conn = Open();
        using var cmd = conn.CreateCommand();
        cmd.CommandText = "SELECT password_hash FROM users WHERE username = $u LIMIT 1";
        cmd.Parameters.AddWithValue("$u", username);

        var stored = cmd.ExecuteScalar() as string;
        if (string.IsNullOrEmpty(stored))
            return false;

        return VerifyPassword(password, stored);
    }

    private static void InsertUser(SqliteConnection conn, string username, string password)
    {
        using var cmd = conn.CreateCommand();
        cmd.CommandText = "INSERT INTO users (username, password_hash) VALUES ($u, $p)";
        cmd.Parameters.AddWithValue("$u", username);
        cmd.Parameters.AddWithValue("$p", HashPassword(password));
        cmd.ExecuteNonQuery();
    }

    private SqliteConnection Open()
    {
        var conn = new SqliteConnection(_connectionString);
        conn.Open();
        return conn;
    }

    // --- Password hashing (salted PBKDF2; format: iterations.salt.hash, base64) ---

    private static string HashPassword(string password)
    {
        var salt = RandomNumberGenerator.GetBytes(SaltBytes);
        var hash = Rfc2898DeriveBytes.Pbkdf2(password, salt, Iterations, Algo, HashBytes);
        return $"{Iterations}.{Convert.ToBase64String(salt)}.{Convert.ToBase64String(hash)}";
    }

    private static bool VerifyPassword(string password, string stored)
    {
        var parts = stored.Split('.', 3);
        if (parts.Length != 3 || !int.TryParse(parts[0], out var iterations))
            return false;

        byte[] salt, expected;
        try
        {
            salt = Convert.FromBase64String(parts[1]);
            expected = Convert.FromBase64String(parts[2]);
        }
        catch (FormatException)
        {
            return false;
        }

        var actual = Rfc2898DeriveBytes.Pbkdf2(password, salt, iterations, Algo, expected.Length);
        return CryptographicOperations.FixedTimeEquals(actual, expected);
    }
}

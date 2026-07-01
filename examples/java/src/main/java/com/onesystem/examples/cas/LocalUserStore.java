package com.onesystem.examples.cas;

import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Component;

import javax.annotation.PostConstruct;
import java.security.SecureRandom;
import java.security.spec.KeySpec;
import java.util.Base64;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.PBEKeySpec;

/**
 * The app's OWN local username/password account store, backed by SQLite.
 *
 * <p>This is completely separate from the CAS SSO flow: a user can sign in
 * EITHER with a local account validated here, OR via CAS. Both end up in the
 * same HTTP session under the {@code cas_user} attribute (see
 * {@link CasController}), so the rest of the app treats them uniformly.</p>
 *
 * <p>Passwords are stored as salted PBKDF2-HMAC-SHA256 hashes - no plaintext,
 * and no extra crypto dependency (the JDK ships the algorithm). The stored
 * format is {@code pbkdf2$<iterations>$<base64-salt>$<base64-hash>}.</p>
 */
@Component
public class LocalUserStore {

    /** PBKDF2 work factor. */
    private static final int ITERATIONS = 120_000;
    private static final int KEY_LENGTH_BITS = 256;
    private static final int SALT_BYTES = 16;
    private static final SecureRandom RANDOM = new SecureRandom();

    private final JdbcTemplate jdbc;

    public LocalUserStore(JdbcTemplate jdbc) {
        this.jdbc = jdbc;
    }

    /**
     * Create the schema if needed and seed two demo users on first run.
     *
     * <p>Runs once at startup. Seeding is idempotent: it only inserts when the
     * table is empty, so restarts never duplicate or overwrite users.</p>
     */
    @PostConstruct
    public void initAndSeed() {
        jdbc.execute(
                "CREATE TABLE IF NOT EXISTS users (" +
                "  id INTEGER PRIMARY KEY AUTOINCREMENT," +
                "  username TEXT NOT NULL UNIQUE," +
                "  password_hash TEXT NOT NULL" +
                ")");

        Integer count = jdbc.queryForObject("SELECT COUNT(*) FROM users", Integer.class);
        if (count != null && count == 0) {
            // Demo credentials (HASHED on insert).
            seed("rajan", "rajan123");
            seed("demo", "demo123");
        }
    }

    private void seed(String username, String password) {
        jdbc.update(
                "INSERT INTO users (username, password_hash) VALUES (?, ?)",
                username, hashPassword(password));
    }

    /**
     * Validate a username/password against the local store.
     *
     * @return {@code true} only when the user exists AND the password matches.
     */
    public boolean verifyCredentials(String username, String password) {
        if (username == null || password == null) {
            return false;
        }
        String stored;
        try {
            stored = jdbc.queryForObject(
                    "SELECT password_hash FROM users WHERE username = ?",
                    String.class, username);
        } catch (EmptyResultDataAccessException e) {
            return false; // no such user
        }
        return verifyHash(password, stored);
    }

    // ------------------------------------------------------------------
    // Password hashing (PBKDF2-HMAC-SHA256, salted).
    // ------------------------------------------------------------------

    private static String hashPassword(String password) {
        byte[] salt = new byte[SALT_BYTES];
        RANDOM.nextBytes(salt);
        byte[] hash = pbkdf2(password.toCharArray(), salt, ITERATIONS);
        return "pbkdf2$" + ITERATIONS + "$" +
                Base64.getEncoder().encodeToString(salt) + "$" +
                Base64.getEncoder().encodeToString(hash);
    }

    private static boolean verifyHash(String password, String stored) {
        if (stored == null) return false;
        String[] parts = stored.split("\\$");
        if (parts.length != 4 || !"pbkdf2".equals(parts[0])) {
            return false;
        }
        try {
            int iterations = Integer.parseInt(parts[1]);
            byte[] salt = Base64.getDecoder().decode(parts[2]);
            byte[] expected = Base64.getDecoder().decode(parts[3]);
            byte[] actual = pbkdf2(password.toCharArray(), salt, iterations);
            return constantTimeEquals(expected, actual);
        } catch (RuntimeException e) {
            return false;
        }
    }

    private static byte[] pbkdf2(char[] password, byte[] salt, int iterations) {
        try {
            KeySpec spec = new PBEKeySpec(password, salt, iterations, KEY_LENGTH_BITS);
            SecretKeyFactory factory = SecretKeyFactory.getInstance("PBKDF2WithHmacSHA256");
            return factory.generateSecret(spec).getEncoded();
        } catch (Exception e) {
            throw new IllegalStateException("Unable to hash password", e);
        }
    }

    /** Length-constant comparison to avoid leaking match position via timing. */
    private static boolean constantTimeEquals(byte[] a, byte[] b) {
        if (a.length != b.length) return false;
        int diff = 0;
        for (int i = 0; i < a.length; i++) {
            diff |= a[i] ^ b[i];
        }
        return diff == 0;
    }
}

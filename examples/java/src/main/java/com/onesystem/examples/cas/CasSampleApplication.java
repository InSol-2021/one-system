package com.onesystem.examples.cas;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

/**
 * One System CAS - Minimal Spring Boot sample app.
 *
 * <p>A real app with its OWN local username/password accounts (SQLite, see
 * {@link LocalUserStore} / {@link LoginController}) that ALSO proves the
 * {@code com.cassystem:cas-client} package works end-to-end. Sign in EITHER way:</p>
 *
 * <pre>
 *   Local accounts:
 *     GET  /login      -> the username/password form (+ a CAS SSO link)
 *     POST /login      -> form login: validate vs SQLite, create our OWN session;
 *                         ALSO the CAS link-validation contract (client_validation
 *                         -> JSON {"success":true|false}, no session)
 *
 *   CAS single sign-on:
 *     GET  /cas/login  -> trigger CAS SSO login (browser redirect)
 *     GET  /callback   -> handle the callback, validate the single-use token
 *                         SERVER-TO-SERVER via the package, create our OWN session
 *
 *   Shared:
 *     GET  /           -> show whoever is signed in (local OR CAS), or a Sign in link
 *     GET  /profile    -> a route GUARDED by the package's CasAuthFilter
 *     GET  /logout     -> clear our session (and tell the CAS server)
 * </pre>
 *
 * <p>The JWT is HS256 and signed with a secret the CAS server holds. Client apps
 * must NEVER hold that secret - we always validate the token by calling the CAS
 * server's {@code POST /api/validate-token}, which the package does for us inside
 * {@link com.cassystem.client.CasClient#validateToken(String)}.</p>
 */
@SpringBootApplication
public class CasSampleApplication {
    public static void main(String[] args) {
        SpringApplication.run(CasSampleApplication.class, args);
    }
}

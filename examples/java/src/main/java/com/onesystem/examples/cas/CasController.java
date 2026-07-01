package com.onesystem.examples.cas;

import com.cassystem.client.CasClient;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpSession;
import java.util.Map;

/**
 * All four steps of the CAS flow live here, kept deliberately tiny.
 *
 * <p>We store the validated user in our OWN HTTP session under the key
 * "cas_user" - the exact key the package's {@link com.cassystem.client.CasAuthFilter}
 * reads - so the filter recognises the session on protected routes.</p>
 */
@RestController
public class CasController {

    /** The session attribute key the package's CasAuthFilter expects. */
    private static final String SESSION_USER_KEY = "cas_user";

    private final CasClient cas;

    public CasController(CasClient cas) {
        this.cas = cas;
    }

    // ------------------------------------------------------------------
    // (c) Home - public. Shows whether the user is signed in (locally OR via
    //     CAS), who they are, and offers logout. Otherwise links to /login.
    // ------------------------------------------------------------------
    @GetMapping(value = "/", produces = "text/html")
    public String home(HttpServletRequest request) {
        Map<String, Object> user = currentUser(request);
        if (user == null) {
            return page(
                    "<h1>One System CAS - Java (Spring Boot) sample</h1>" +
                    "<p>You are not logged in.</p>" +
                    "<p><a class=\"btn login\" href=\"/login\">Sign in</a></p>" +
                    "<p class=\"hint\">Sign in with a local username/password, " +
                    "or with One System CAS single sign-on.</p>");
        }
        String how = isLocalUser(user) ? "a local username/password account"
                                       : "One System CAS single sign-on";
        return page(
                "<h1>Welcome, " + escape(displayName(user)) + "</h1>" +
                "<p>You are signed in via " + how + ".</p>" +
                "<pre>" + escape(prettyUser(user)) + "</pre>" +
                "<p>" +
                "  <a class=\"btn login\" href=\"/profile\">View protected /profile</a>&nbsp;" +
                "  <a class=\"btn logout\" href=\"/logout\">Logout</a>" +
                "</p>");
    }

    // ------------------------------------------------------------------
    // (a) Trigger CAS login.
    //     cas.getLoginUrl() builds {CAS_BASE}/sso/login?client_id=...; the CAS
    //     server authenticates the user and 302-redirects back to our registered
    //     callback_url with ?token=<JWT>.
    //
    //     NOTE: this used to live at GET /login. /login is now the LOCAL
    //     username/password form (see LoginController), which also links here.
    // ------------------------------------------------------------------
    @GetMapping("/cas/login")
    public void casLogin(javax.servlet.http.HttpServletResponse response) throws java.io.IOException {
        response.sendRedirect(cas.getLoginUrl());
    }

    // ------------------------------------------------------------------
    // (b) Handle the callback and validate the token SERVER-TO-SERVER.
    //     cas.validateToken(token) POSTs to {CAS_BASE}/api/validate-token with the
    //     client_id + client_secret and returns the user Map on success, or null
    //     on failure. The token is single-use, so we validate exactly once, then
    //     store the resulting user in our OWN session.
    // ------------------------------------------------------------------
    @GetMapping(value = "/callback", produces = "text/html")
    public String callback(@RequestParam(value = "token", required = false) String token,
                           javax.servlet.http.HttpServletResponse response,
                           HttpServletRequest request) throws java.io.IOException {
        if (token == null || token.isEmpty()) {
            response.setStatus(400);
            return errorPage("Missing \"token\" query parameter.");
        }

        Map<String, Object> user = cas.validateToken(token);

        if (user == null) {
            // Validation failed (expired / already used / bad signature, etc.)
            response.setStatus(401);
            return errorPage("Token validation failed. Please try logging in again.");
        }

        // Success: create our own session. We do NOT keep the JWT around for auth.
        // We store under "cas_user" so the package's CasAuthFilter sees the session.
        request.getSession(true).setAttribute(SESSION_USER_KEY, user);
        response.sendRedirect("/");
        return null;
    }

    // ------------------------------------------------------------------
    // Protected route - guarded by the package's CasAuthFilter (see
    // CasConfiguration). If there is no session the filter redirects to /login.
    // ------------------------------------------------------------------
    @GetMapping(value = "/profile", produces = "text/html")
    public String profile(HttpServletRequest request) {
        // Reaching here means the filter already validated the session.
        Map<String, Object> user = currentUser(request);
        return page(
                "<h1>Protected profile</h1>" +
                "<p>This route is guarded by the package's <code>CasAuthFilter</code>.</p>" +
                "<pre>" + escape(prettyUser(user)) + "</pre>" +
                "<p><a href=\"/\">&larr; Home</a></p>");
    }

    // ------------------------------------------------------------------
    // (d) Logout. Clear our own session and (best-effort) notify the CAS server.
    // ------------------------------------------------------------------
    @GetMapping("/logout")
    public void logout(javax.servlet.http.HttpServletResponse response,
                       HttpServletRequest request) throws java.io.IOException {
        try {
            cas.logout(null); // optional: tells the CAS server about the logout
        } catch (Exception ignored) {
            /* logout is best-effort; ignore failures */
        }
        HttpSession session = request.getSession(false);
        if (session != null) {
            session.invalidate();
        }
        response.sendRedirect("/");
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    @SuppressWarnings("unchecked")
    private Map<String, Object> currentUser(HttpServletRequest request) {
        HttpSession session = request.getSession(false);
        if (session == null) return null;
        Object user = session.getAttribute(SESSION_USER_KEY);
        return (user instanceof Map) ? (Map<String, Object>) user : null;
    }

    /** True when the session user came from the LOCAL store (vs. CAS). */
    private static boolean isLocalUser(Map<String, Object> user) {
        return "local".equals(user.get("auth_method"));
    }

    private static String displayName(Map<String, Object> user) {
        Object name = user.get("username");
        if (name == null) name = user.get("email");
        if (name == null) name = user.get("id");
        return String.valueOf(name);
    }

    /** Render the user Map as simple, readable lines (no JSON dependency needed). */
    private static String prettyUser(Map<String, Object> user) {
        StringBuilder sb = new StringBuilder();
        for (Map.Entry<String, Object> e : user.entrySet()) {
            sb.append(e.getKey()).append(": ").append(e.getValue()).append('\n');
        }
        return sb.toString().trim();
    }

    private static String errorPage(String message) {
        return page(
                "<h1 class=\"err\">Authentication error</h1>" +
                "<p class=\"err\">" + escape(message) + "</p>" +
                "<p><a class=\"btn login\" href=\"/login\">Try again</a></p>");
    }

    private static String page(String body) {
        return "<!doctype html><html><head><meta charset=\"utf-8\">" +
               "<title>One System CAS - Java sample</title>" +
               "<style>" +
               "body{font-family:system-ui,sans-serif;max-width:640px;margin:64px auto;padding:0 16px;line-height:1.5}" +
               "a.btn{display:inline-block;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600}" +
               "a.login{background:#2563eb;color:#fff}" +
               "a.logout{background:#ef4444;color:#fff}" +
               "pre{background:#f4f4f5;padding:16px;border-radius:8px;overflow:auto}" +
               ".err{color:#b91c1c}" +
               ".hint{color:#6b7280;font-size:.9rem}" +
               "</style></head><body>" + body + "</body></html>";
    }

    private static String escape(String str) {
        if (str == null) return "";
        return str.replace("&", "&amp;")
                  .replace("<", "&lt;")
                  .replace(">", "&gt;")
                  .replace("\"", "&quot;")
                  .replace("'", "&#39;");
    }
}

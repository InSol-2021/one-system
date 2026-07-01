package com.onesystem.examples.cas;

import com.fasterxml.jackson.databind.ObjectMapper;
import org.springframework.http.MediaType;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RestController;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.util.LinkedHashMap;
import java.util.Map;

/**
 * Local username/password login - the app's OWN authentication, alongside CAS SSO.
 *
 * <p>This controller owns the {@code /login} route, which serves TWO contracts:</p>
 *
 * <ol>
 *   <li><b>Browser login.</b> {@code GET /login} renders a small form;
 *       {@code POST /login} (a normal form submit) validates the credentials
 *       against the local SQLite store and, on success, establishes the app's
 *       own session - the SAME {@code cas_user} session attribute the CAS flow
 *       uses - then redirects to {@code /}. On failure it re-renders the form
 *       with an error.</li>
 *
 *   <li><b>CAS link-validation contract.</b> The CAS server POSTs
 *       {@code {username, password, client_validation: true}} to {@code /login}
 *       to check whether a credential pair is valid on this client. We detect
 *       that call by the presence of {@code client_validation} (and/or an
 *       {@code Accept: application/json} header) and respond with JSON
 *       {@code {"success": true}} (HTTP 200) for valid credentials or
 *       {@code {"success": false}} (HTTP 401) for invalid - WITHOUT creating a
 *       browser session.</li>
 * </ol>
 */
@RestController
public class LoginController {

    /** The session attribute key shared with the CAS flow (see CasController). */
    private static final String SESSION_USER_KEY = "cas_user";

    private final LocalUserStore users;
    private final ObjectMapper json = new ObjectMapper();

    public LoginController(LocalUserStore users) {
        this.users = users;
    }

    // ------------------------------------------------------------------
    // GET /login - the local username/password form (+ a CAS SSO link).
    // ------------------------------------------------------------------
    @GetMapping(value = "/login", produces = "text/html")
    public String loginForm() {
        return formPage(null);
    }

    // ------------------------------------------------------------------
    // POST /login - serves BOTH the browser form login and the CAS
    // link-validation contract. The two are distinguished by the request:
    //   - validation call  -> has a `client_validation` field, or asks for JSON
    //   - browser form post -> neither
    // ------------------------------------------------------------------
    @PostMapping("/login")
    public Object login(HttpServletRequest request, HttpServletResponse response) throws IOException {
        Credentials creds = readCredentials(request);

        if (isValidationCall(request, creds)) {
            // CAS link-validation: JSON only, NEVER a session.
            boolean ok = users.verifyCredentials(creds.username, creds.password);
            writeJson(response, ok ? 200 : 401, Map.of("success", ok));
            return null;
        }

        // Browser form login.
        if (users.verifyCredentials(creds.username, creds.password)) {
            // Establish the app's OWN session - the same attribute the CAS flow
            // uses - so the rest of the app treats local + CAS users uniformly.
            Map<String, Object> user = new LinkedHashMap<>();
            user.put("username", creds.username);
            user.put("auth_method", "local");
            request.getSession(true).setAttribute(SESSION_USER_KEY, user);
            response.sendRedirect("/");
            return null;
        }

        // Failure: re-render the form with an error (and don't echo the password).
        response.setStatus(401);
        response.setContentType("text/html;charset=UTF-8");
        return formPage("Invalid username or password.");
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * A call is the CAS link-validation contract when it carries a
     * {@code client_validation} field, or when it asks for a JSON response.
     * In either case we must answer with JSON and NOT create a session.
     */
    private static boolean isValidationCall(HttpServletRequest request, Credentials creds) {
        if (creds.clientValidationPresent) {
            return true;
        }
        String accept = request.getHeader("Accept");
        return accept != null && accept.contains(MediaType.APPLICATION_JSON_VALUE);
    }

    /** Pull username/password (and the client_validation flag) from form OR JSON. */
    private Credentials readCredentials(HttpServletRequest request) throws IOException {
        Credentials c = new Credentials();
        String contentType = request.getContentType();

        if (contentType != null && contentType.contains(MediaType.APPLICATION_JSON_VALUE)) {
            byte[] raw = request.getInputStream().readAllBytes();
            if (raw.length > 0) {
                @SuppressWarnings("unchecked")
                Map<String, Object> body = json.readValue(
                        new String(raw, StandardCharsets.UTF_8), Map.class);
                c.username = asString(body.get("username"));
                c.password = asString(body.get("password"));
                c.clientValidationPresent = body.containsKey("client_validation");
            }
        } else {
            // application/x-www-form-urlencoded (or query params).
            c.username = request.getParameter("username");
            c.password = request.getParameter("password");
            c.clientValidationPresent = request.getParameterMap().containsKey("client_validation");
        }
        return c;
    }

    private void writeJson(HttpServletResponse response, int status, Map<String, ?> body)
            throws IOException {
        response.setStatus(status);
        response.setContentType("application/json;charset=UTF-8");
        response.getWriter().write(json.writeValueAsString(body));
    }

    private static String asString(Object o) {
        return o == null ? null : String.valueOf(o);
    }

    /** Simple holder for the parsed request fields. */
    private static final class Credentials {
        String username;
        String password;
        boolean clientValidationPresent;
    }

    // ------------------------------------------------------------------
    // The login form page (clean, minimal). `error` is null on first render.
    // ------------------------------------------------------------------
    private static String formPage(String error) {
        String errorHtml = (error == null) ? "" :
                "<p class=\"err\">" + escape(error) + "</p>";
        return "<!doctype html><html><head><meta charset=\"utf-8\">" +
               "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">" +
               "<title>Sign in - One System CAS Java sample</title>" +
               "<style>" +
               "body{font-family:system-ui,sans-serif;max-width:380px;margin:64px auto;padding:0 16px;line-height:1.5}" +
               "h1{font-size:1.4rem}" +
               "label{display:block;font-weight:600;margin:14px 0 4px}" +
               "input{width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:1rem}" +
               "button{width:100%;margin-top:20px;padding:11px 18px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-weight:600;font-size:1rem;cursor:pointer}" +
               ".err{color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;padding:10px 12px;border-radius:8px}" +
               ".divider{margin:24px 0 16px;border:0;border-top:1px solid #e5e7eb}" +
               ".sso{display:block;text-align:center;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600;border:1px solid #d1d5db;color:#111827}" +
               ".hint{color:#6b7280;font-size:.85rem}" +
               "</style></head><body>" +
               "<h1>Sign in</h1>" +
               errorHtml +
               "<form method=\"post\" action=\"/login\">" +
               "  <label for=\"username\">Username</label>" +
               "  <input id=\"username\" name=\"username\" autocomplete=\"username\" autofocus required>" +
               "  <label for=\"password\">Password</label>" +
               "  <input id=\"password\" name=\"password\" type=\"password\" autocomplete=\"current-password\" required>" +
               "  <button type=\"submit\">Sign in</button>" +
               "</form>" +
               "<hr class=\"divider\">" +
               "<a class=\"sso\" href=\"/cas/login\">Sign in with One System CAS (SSO)</a>" +
               "<p class=\"hint\">Demo accounts: <code>rajan / rajan123</code> or <code>demo / demo123</code>.</p>" +
               "</body></html>";
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

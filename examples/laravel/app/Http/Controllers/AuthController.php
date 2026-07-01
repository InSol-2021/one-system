<?php

namespace App\Http\Controllers;

use App\Support\LocalUserStore;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

// The package's public facade. It proxies to CasSystem\LaravelClient\Services\CasAuthService.
// (See the package README -> "Manual Authentication" and "API Reference".)
use CasSystem\LaravelClient\Facades\CasClient;

/**
 * Authentication for this sample. There are TWO ways in, sharing ONE app session:
 *
 *   1. LOCAL username/password  — backed by SQLite (App\Support\LocalUserStore).
 *      GET  /login   renders a small login form
 *      POST /login   validates against SQLite, then starts the app session
 *
 *   2. CAS single sign-on       — unchanged, via the package's CasClient facade.
 *      GET  /cas/login  redirects to {CAS_SERVER_URL}/sso/login
 *      GET  /callback   validates the single-use token server-to-server
 *
 * The SAME POST /login route ALSO answers the CAS server's link-validation
 * contract: when the body contains "client_validation" the route validates the
 * supplied username/password and replies with JSON {"success": true|false}
 * WITHOUT creating a browser session (see localLogin()).
 *
 * We deliberately use the package's CasClient FACADE (its documented public API)
 * for the CAS half rather than re-implementing any HTTP calls ourselves:
 *
 *   - CasClient::getLoginUrl($returnUrl)  -> builds {CAS_SERVER_URL}/sso/login?...
 *   - CasClient::validateToken($token)    -> POSTs server-to-server to the CAS
 *                                            server and returns the user array, or null.
 *   - CasClient::logout($token)           -> tells the CAS server to drop the token.
 *
 * Both auth methods store the signed-in user the same way: session('user') holds
 * an array (with at least 'username'), and session('auth_method') records 'local'
 * or 'cas'. The home view reads those without caring how the user signed in.
 */
class AuthController extends Controller
{
    /**
     * Home page. Shows the authenticated user (local OR CAS), or login options.
     * GET /
     */
    public function home(Request $request)
    {
        return view('home', [
            'user'       => $request->session()->get('user'),
            'authMethod' => $request->session()->get('auth_method'),
        ]);
    }

    /**
     * Render the LOCAL login form.
     * GET /login
     */
    public function showLogin(Request $request)
    {
        // Already signed in? Go straight home.
        if ($request->session()->get('user')) {
            return redirect()->route('home');
        }

        return view('login');
    }

    /**
     * Handle a LOCAL login POST.
     * POST /login
     *
     * This one route serves TWO callers, distinguished by the request body:
     *
     *   (A) The CAS server's link-validation call. It POSTs
     *       {"username","password","client_validation":true} (JSON or form) and
     *       expects JSON back. We validate and reply:
     *         200 {"success": true}   for valid credentials
     *         401 {"success": false}  for invalid credentials
     *       and DO NOT touch the browser session.
     *
     *   (B) A human submitting the browser form. On success we start the app's
     *       OWN session and redirect home; on failure we re-render the form.
     */
    public function localLogin(Request $request, LocalUserStore $store)
    {
        $username = (string) $request->input('username', '');
        $password = (string) $request->input('password', '');

        $user = $store->validate($username, $password);

        // (A) CAS link-validation contract: presence of client_validation (or a
        //     JSON Accept) means "just tell me yes/no" — no browser session.
        if ($this->isValidationCall($request)) {
            if ($user) {
                return response()->json(['success' => true], 200);
            }

            return response()->json(['success' => false], 401);
        }

        // (B) Browser login.
        if (! $user) {
            // Re-render the form with an error. Redirect explicitly to the login
            // route (not back()) so it works regardless of the Referer header.
            return redirect()->route('login')
                ->withInput(['username' => $username])
                ->with('error', 'Invalid username or password.');
        }

        // Valid -> establish the app's OWN local session (the same session the
        // CAS callback uses). Store a CAS-shaped user array so the home view is
        // agnostic about how the visitor signed in.
        $request->session()->put('user', [
            'id'       => $user['id'],
            'username' => $user['username'],
            'email'    => null,
        ]);
        $request->session()->put('auth_method', 'local');
        $request->session()->put('authenticated', true);
        $request->session()->regenerate(); // fresh session id after auth

        return redirect()->route('home')
            ->with('success', 'Logged in locally as ' . $user['username'] . '.');
    }

    /**
     * Decide whether this POST /login is the CAS server's validation call rather
     * than a human submitting the form. True when the body carries the
     * client_validation flag, or the caller explicitly wants JSON back.
     */
    private function isValidationCall(Request $request): bool
    {
        return $request->has('client_validation')
            || $request->boolean('client_validation')
            || $request->expectsJson()
            || $request->isJson();
    }

    /**
     * Trigger CAS login (single sign-on).
     * GET /cas/login
     *
     * We ask the package for the CAS login URL and redirect the browser there.
     * The CAS server authenticates the user and 302s back to our CAS_CALLBACK_URL
     * with ?token=<JWT> appended.
     */
    public function casLogin(Request $request)
    {
        // Pass our own /callback as the return URL so CAS redirects back to us.
        $loginUrl = CasClient::getLoginUrl(route('callback'));

        return redirect($loginUrl);
    }

    /**
     * Handle the CAS callback and validate the token via the package.
     * GET /callback?token=<JWT>
     */
    public function callback(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect()->route('home')
                ->with('error', 'No authentication token was provided by the CAS server.');
        }

        // Validate the single-use token SERVER-TO-SERVER through the package.
        // This is the only place the JWT is checked; the client_secret used for
        // the call lives in config/cas-client.php (env), never in the browser.
        $user = CasClient::validateToken($token);

        if (! $user) {
            return redirect()->route('home')
                ->with('error', 'Token validation failed (invalid or expired token).');
        }

        // Token is valid -> create our app's OWN session, same as local login.
        $request->session()->put('user', $user);
        $request->session()->put('auth_method', 'cas');
        $request->session()->put('authenticated', true);
        $request->session()->regenerate(); // fresh session id after auth

        return redirect()->route('home')
            ->with('success', 'Logged in via One System CAS as ' . ($user['username'] ?? $user['email'] ?? 'user') . '.');
    }

    /**
     * Logout (works for BOTH local and CAS sessions).
     * POST /logout
     *
     * Clears the app session and (best-effort) notifies the CAS server.
     */
    public function logout(Request $request)
    {
        // Best-effort: if this was a CAS session, tell the CAS server to forget
        // it. Harmless for local sessions (token is simply null).
        CasClient::logout($request->session()->get('cas_token'));

        // Tear down our own session.
        $request->session()->forget(['user', 'cas_token', 'auth_method', 'authenticated']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out.');
    }
}

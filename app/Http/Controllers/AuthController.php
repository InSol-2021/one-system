<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AuditLog;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\SsoService;

class AuthController extends Controller
{
    private $authService;
    private $ssoService;

    public function __construct(AuthService $authService, SsoService $ssoService)
    {
        $this->authService = $authService;
        $this->ssoService = $ssoService;
    }

    public function showLogin()
    {
        if ($this->isUserAuthenticated()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    /**
     * Check if user is currently authenticated
     */
    private function isUserAuthenticated()
    {
        if (Auth::check()) {
            return true;
        }

        if (session('user_id') && session('username')) {
            $user = User::where('id', session('user_id'))
                ->where('is_active', true)
                ->first();

            if ($user) {
                Auth::login($user);
                return true;
            } else {
                session()->flush();
            }
        }

        return false;
    }

    /**
     * Redirect user to appropriate dashboard based on role
     */
    private function redirectToDashboard()
    {
        $user = Auth::user() ?? User::find(session('user_id'));

        if (!$user) {
            session()->flush();
            return redirect()->route('login');
        }

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'user':
                return redirect()->route('user.dashboard');
            default:
                return redirect()->route('user.dashboard');
        }
    }

    public function login(Request $request)
    {
        session()->forget(['2fa_required', 'temp_user_id', 'temp_username', 'temp_role']);

        $request->validate([
           'login' => 'required',
           'password' => 'required'
        ]);

        $loginInput = $request->login ?? $request->email;
        $result = $this->authService->login($loginInput, $request->password, $request);

        if ($result['status'] === '2fa_required') {
             return redirect()->route('auth.2fa')->with('message', '2FA verification required');
        }

        if ($result['status'] === 'success') {
             $user = $result['user'];
             
             if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'role' => $user->role,
                        'full_name' => $user->full_name,
                    ]
                ]);
            }

            if (session()->has('sso_client_id')) {
                $clientId = session()->pull('sso_client_id');
                if (!Auth::check()) {
                    Auth::login($user);
                }
                
                $ssoResult = $this->ssoService->generateWebSsoToken($user, $clientId, $request);

                if ($ssoResult['status'] === 'success') {
                    return redirect($ssoResult['redirect_url']);
                }
                
                 return redirect()->route('user.dashboard')->withErrors(['error' => 'SSO Error: ' . ($ssoResult['message'] ?? 'Unknown error')]);
            }

            return $this->redirectToDashboard();
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return back()->withErrors(['error' => 'Invalid username or password'])->withInput();
    }

    public function register(RegisterRequest $request)
    {
        // RegisterRequest enforces the strong password, username regex,
        // uniqueness, and reCAPTCHA rules. Use validated() data only.
        $validated = $request->validated();

        $existingUser = User::where('username', $validated['username'])
            ->orWhere('email', $validated['email'])
            ->first();

        if ($existingUser) {
            return response()->json(['error' => 'User already exists'], 400);
        }

        // Pass an explicit whitelist of fields, never $request->all(), to avoid
        // mass-assignment of role/is_active/2FA fields.
        $user = $this->authService->register([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'first_name' => $request->input('first_name', ''),
            'last_name' => $request->input('last_name', ''),
        ], $request);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]
        ], 201);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true]);
        }

        return redirect('/auth/login')->with('message', 'You have been logged out successfully.');
    }

    public function user()
    {
        $userId = auth()->id() ?? session('user_id');
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user() ?? User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email
        ]);
    }

    /**
     * Generate SSO Token using Client Credentials (client_id + client_secret + username)
     */
    public function generateSSOToken(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'username' => 'required|string',
        ]);

        $result = $this->ssoService->generateToken($request->client_id, $request->client_secret, $request->username, $request);

        if ($result['status'] === 'error') {
            return response()->json(['error' => $result['message']], $result['code']);
        }

        return response()->json([
            'redirect_url' => $result['redirect_url'],
            'token' => $result['token']
        ]);
    }

    /**
     * Validate SSO Token using Client Credentials (client_id + client_secret)
     */
    public function validateSsoToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        try {
            $result = $this->ssoService->validateToken($request->token, $request->client_id, $request->client_secret, $request);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 401);
        }
    }

    public function ssoCallback(Request $request)
    {
        return view('auth.sso-callback');
    }

    public function processSSOCallback(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $result = $this->ssoService->processCallback($request->token, $request);

        if ($result['status'] === 'error') {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 401);
        }

        $user = $result['user'];
        $ssoToken = $result['ssoToken'];

        // Prevent session fixation: rotate the session id before establishing
        // the authenticated identity from a successful SSO callback.
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        Auth::login($user);

        session(['user_id' => $user->id, 'username' => $user->username, 'role' => $user->role]);

        AuditLog::create([
            'user_id' => $user->id,
            'client_system_id' => $ssoToken->client_system_id,
            'event_type' => 'sso_callback_success',
            'action' => 'sso_callback_success',
            'description' => 'SSO callback successful',
            'details' => ['token_id' => $ssoToken->id, 'ip' => $request->ip()],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'success' => true
        ]);

        // Determine redirect URL based on user role
        $redirectUrl = match ($user->role ?? 'user') {
            'admin' => '/admin/dashboard',
            default => '/user/dashboard',
        };

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful',
            'redirect_url' => $redirectUrl,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]
        ]);
    }

    public function ssoLogin(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
        ]);

        $clientId = $request->client_id;

        if ($this->isUserAuthenticated()) {
            $user = Auth::user();
            $result = $this->ssoService->generateWebSsoToken($user, $clientId, $request);

            if ($result['status'] === 'success') {
                return redirect($result['redirect_url']);
            }

            return redirect()->route('login')->withErrors(['error' => $result['message']]);
        }

        session(['sso_client_id' => $clientId]);
        return redirect()->route('login');
    }
}

<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function login($loginInput, $password, $request)
    {
        $user = null;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)
                ->where('is_active', true)
                ->first();
        } else {
            $user = User::where('username', $loginInput)
                ->where('is_active', true)
                ->first();
        }

        if (!$user) {
            // Try lenient search. Group the OR so the is_active=true filter
            // applies to BOTH identifiers; otherwise an inactive account whose
            // email matches could still log in.
            $user = User::where('is_active', true)
                ->where(function ($query) use ($loginInput) {
                    $query->where('email', $loginInput)
                        ->orWhere('username', $loginInput);
                })
                ->first();
        }

        if ($user && $this->verifyPassword($password, $user->password)) {
            // Check for 2FA
            if ($user->two_factor_enabled && $user->two_factor_secret) {
                session([
                    'temp_user_id' => $user->id,
                    'temp_username' => $user->username,
                    'temp_role' => $user->role,
                    '2fa_required' => true
                ]);

                return ['status' => '2fa_required'];
            }

            $this->completeLogin($user, $request);

            return ['status' => 'success', 'user' => $user];
        }

        return ['status' => 'failed'];
    }

    public function completeLogin(User $user, $request)
    {
        $user->update(['last_login' => now()]);

        // Prevent session fixation: rotate the session id (and CSRF token)
        // immediately after successful credential verification, before
        // populating the authenticated identity into the session.
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        Auth::login($user);

        session(['user_id' => $user->id, 'username' => $user->username, 'role' => $user->role]);

        AuditLog::create([
            'user_id' => $user->id,
            'event_type' => 'login',
            'action' => 'user_login',
            'description' => "User {$user->username} logged in",
            'details' => ['ip' => $request->ip()],
            'success' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function register(array $data, $request)
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'is_active' => true,
        ]);

        // Prevent session fixation on the new authenticated session.
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        Auth::login($user);

        session(['user_id' => $user->id, 'username' => $user->username, 'role' => $user->role]);

        AuditLog::create([
            'user_id' => $user->id,
            'event_type' => 'register',
            'action' => 'user_registration',
            'description' => "New user {$user->username} registered",
            'details' => ['ip' => $request->ip()],
            'success' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $user;
    }

    public function logout($request)
    {
        $userId = auth()->id() ?? session('user_id');
        if ($userId) {
            $this->logAuditEvent($userId, 'logout', ['ip' => $request->ip()]);
        }

        Auth::logout();
        session()->flush();
    }

    private function verifyPassword($password, $hash)
    {
        if (preg_match('/^\$2[ayb]\$/', $hash)) {
            return Hash::check($password, $hash);
        }

        return $this->verifyScryptPassword($password, $hash);
    }

    private function verifyScryptPassword($password, $hash)
    {
        $parts = explode('.', $hash);
        if (count($parts) !== 2) {
            return false;
        }

        if (!ctype_xdigit($parts[0])) {
            return false;
        }

        $storedHash = hex2bin($parts[0]);
        $salt = $parts[1];

        // PHP does not ship a global scrypt() function and the project does not
        // polyfill one. Guard against a missing implementation so a legacy
        // (non-bcrypt) hash cannot fatal the login request — fail closed by
        // treating verification as invalid.
        if (!function_exists('scrypt')) {
            Log::warning('Legacy scrypt password verification attempted but scrypt() is unavailable.');
            return false;
        }

        try {
            $hashedPassword = scrypt($password, $salt, 65536, 8, 1, 64);
        } catch (\Throwable $e) {
            Log::warning('Legacy scrypt password verification failed: ' . $e->getMessage());
            return false;
        }

        return hash_equals($storedHash, $hashedPassword);
    }

    private function logAuditEvent($userId, $action, $details, $clientSystemId = null)
    {
        AuditLog::create([
            'user_id' => $userId,
            'client_system_id' => $clientSystemId,
            'event_type' => $action,
            'action' => $action,
            'description' => ucfirst($action) . ' event',
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'success' => true,
        ]);
    }
}

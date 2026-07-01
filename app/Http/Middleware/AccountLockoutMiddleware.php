<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class AccountLockoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Implements account lockout after 5 failed attempts within 15 minutes
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Key on the actual login identifier the user submitted (the web flow
        // posts `login`); fall back to email/username so every auth entry
        // point is covered.
        $identifier = $request->input('login')
            ?? $request->input('email')
            ?? $request->input('username');
        $ip = $request->ip();

        if (!$identifier) {
            return $next($request);
        }

        $identifierKey = mb_strtolower(trim((string) $identifier));
        $lockoutKey = 'account-lockout:' . $identifierKey;
        $attemptsKey = 'failed-attempts:' . $identifierKey;

        if (Cache::has($lockoutKey)) {
            $lockoutUntil = Cache::get($lockoutKey);
            $remainingMinutes = ceil((strtotime($lockoutUntil) - time()) / 60);

            AuditLog::create([
                'user_id' => null,
                'action' => 'login_attempt_while_locked',
                'details' => json_encode([
                    'login' => $identifierKey,
                    'ip_address' => $ip,
                    'lockout_until' => $lockoutUntil,
                    'remaining_minutes' => $remainingMinutes
                ]),
                'ip_address' => $ip,
                'created_at' => now()
            ]);

            return response()->json([
                'error' => "Account temporarily locked due to multiple failed login attempts. Please try again in {$remainingMinutes} minutes.",
                'lockout_until' => $lockoutUntil,
                'remaining_minutes' => $remainingMinutes
            ], 423);
        }

        $response = $next($request);

        if ($this->isFailedLogin($response)) {
            $this->handleFailedAttempt($identifierKey, $ip);
        } else {
            Cache::forget($attemptsKey);
        }

        return $response;
    }

    /**
     * Detect a failed login from the actual auth outcome rather than relying
     * solely on a 401 status or a specific JSON error string.
     *
     * - API/JSON flow: failure returns HTTP 401.
     * - Web flow: failure is a 3xx redirect carrying an `error` validation bag
     *   (success redirects to a dashboard with no error bag).
     */
    private function isFailedLogin(Response $response): bool
    {
        $status = $response->getStatusCode();

        if ($status === 401) {
            return true;
        }

        if ($status >= 200 && $status < 300) {
            return false;
        }

        if ($status >= 300 && $status < 400) {
            $errors = session()->get('errors');

            return $errors !== null && $errors->getBag('default')->has('error');
        }

        return false;
    }

    /**
     * Handle a failed login attempt, keyed on the login identifier.
     */
    private function handleFailedAttempt(string $identifier, string $ip): void
    {
        $attemptsKey = 'failed-attempts:' . $identifier;
        $lockoutKey = 'account-lockout:' . $identifier;

        $attempts = Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, 900);

        if ($attempts >= 5) {
            $lockoutUntil = now()->addMinutes(30)->toDateTimeString();
            Cache::put($lockoutKey, $lockoutUntil, 1800);
            Cache::forget($attemptsKey);

            AuditLog::create([
                'user_id' => null,
                'action' => 'account_locked_failed_attempts',
                'details' => json_encode([
                    'login' => $identifier,
                    'ip_address' => $ip,
                    'failed_attempts' => $attempts,
                    'lockout_until' => $lockoutUntil,
                    'lockout_duration_minutes' => 30
                ]),
                'ip_address' => $ip,
                'created_at' => now()
            ]);

            Log::warning("Account locked for excessive failed login attempts", [
                'login' => $identifier,
                'ip_address' => $ip,
                'attempts' => $attempts,
                'lockout_until' => $lockoutUntil
            ]);
        } else {
            AuditLog::create([
                'user_id' => null,
                'action' => 'login_failed_attempt',
                'details' => json_encode([
                    'login' => $identifier,
                    'ip_address' => $ip,
                    'attempt_number' => $attempts,
                    'remaining_attempts' => 5 - $attempts
                ]),
                'ip_address' => $ip,
                'created_at' => now()
            ]);
        }
    }
}

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
        $email = $request->input('email');
        $ip = $request->ip();

        if (!$email) {
            return $next($request);
        }

        $lockoutKey = 'account-lockout:' . $email;
        $attemptsKey = 'failed-attempts:' . $email;

        if (Cache::has($lockoutKey)) {
            $lockoutUntil = Cache::get($lockoutKey);
            $remainingMinutes = ceil((strtotime($lockoutUntil) - time()) / 60);

            AuditLog::create([
                'user_id' => null,
                'action' => 'login_attempt_while_locked',
                'details' => json_encode([
                    'email' => $email,
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

        if ($response->getStatusCode() === 401 ||
            (method_exists($response, 'getData') &&
             isset($response->getData()->error) &&
             strpos($response->getData()->error, 'Invalid credentials') !== false)) {

            $this->handleFailedAttempt($email, $ip);
        } else {
            Cache::forget($attemptsKey);
        }

        return $response;
    }

    /**
     * Handle a failed login attempt
     */
    private function handleFailedAttempt(string $email, string $ip): void
    {
        $attemptsKey = 'failed-attempts:' . $email;
        $lockoutKey = 'account-lockout:' . $email;

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
                    'email' => $email,
                    'ip_address' => $ip,
                    'failed_attempts' => $attempts,
                    'lockout_until' => $lockoutUntil,
                    'lockout_duration_minutes' => 30
                ]),
                'ip_address' => $ip,
                'created_at' => now()
            ]);

            Log::warning("Account locked for excessive failed login attempts", [
                'email' => $email,
                'ip_address' => $ip,
                'attempts' => $attempts,
                'lockout_until' => $lockoutUntil
            ]);
        } else {
            AuditLog::create([
                'user_id' => null,
                'action' => 'login_failed_attempt',
                'details' => json_encode([
                    'email' => $email,
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

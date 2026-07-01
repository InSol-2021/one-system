<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RateLimitLogin
{
    /**
     * Handle an incoming request.
     *
     * Limits failed login attempts to 5 per minute, keyed on the client IP
     * combined with the submitted login identifier. Successful logins clear
     * the counter so legitimate users are not throttled.
     */
    public function handle(Request $request, Closure $next): HttpResponse
    {
        $key = $this->resolveKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'error' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
                'retry_after' => $seconds
            ], 429);
        }

        $response = $next($request);

        if ($this->isFailedLogin($response)) {
            // Only count failed attempts toward the limit.
            RateLimiter::hit($key, 60);
        } else {
            // Clear the counter on a successful (or non-failed) login.
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Build the throttle key from the client IP and login identifier.
     */
    private function resolveKey(Request $request): string
    {
        $identifier = $request->input('login')
            ?? $request->input('email')
            ?? $request->input('username')
            ?? '';

        return 'login-attempts:' . $request->ip() . '|' . mb_strtolower(trim((string) $identifier));
    }

    /**
     * Determine whether the auth response represents a failed login.
     */
    private function isFailedLogin(HttpResponse $response): bool
    {
        $status = $response->getStatusCode();

        // API/JSON failures return 401; the web flow redirects (302) back with
        // validation errors on failure. A successful login redirects without
        // an "error" bag, or returns 2xx JSON.
        if ($status === 401) {
            return true;
        }

        if ($status >= 200 && $status < 300) {
            return false;
        }

        if ($status >= 300 && $status < 400) {
            $errors = $response->headers->get('location') !== null
                ? session()->get('errors')
                : null;

            return $errors !== null && $errors->getBag('default')->has('error');
        }

        return false;
    }
}

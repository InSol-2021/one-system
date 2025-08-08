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
     * Limits login attempts to 5 per minute per IP address
     */
    public function handle(Request $request, Closure $next): HttpResponse
    {
        $key = 'login-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'error' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}

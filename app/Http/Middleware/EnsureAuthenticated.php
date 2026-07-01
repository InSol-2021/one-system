<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    /**
     * Ensure the request is from an authenticated, active user.
     *
     * Resolves the acting user from the Auth guard or from the legacy
     * session(user_id) value. If a valid ACTIVE user is found, the Auth guard
     * is logged in for the request and the session user_id/username/role keys
     * are repopulated so downstream code that reads either source agrees.
     *
     * On failure: redirect to the login route for web requests, or return a
     * 401 JSON response for API/expectsJson requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return $this->unauthenticated($request);
        }

        // Ensure the Auth guard is logged in for this request.
        if (! Auth::check() || (int) Auth::id() !== (int) $user->getKey()) {
            Auth::login($user);
        }

        // Repopulate the legacy session model so both auth sources agree.
        $request->session()->put('user_id', $user->getKey());
        $request->session()->put('username', $user->username);
        $request->session()->put('role', $user->role);

        return $next($request);
    }

    /**
     * Resolve the active acting user from the Auth guard or session(user_id).
     */
    protected function resolveUser(Request $request): ?User
    {
        if (Auth::check()) {
            $authUser = Auth::user();

            if ($authUser instanceof User && $authUser->is_active) {
                return $authUser;
            }
        }

        $userId = $request->session()->get('user_id');

        if ($userId) {
            $user = User::where('id', $userId)
                ->where('is_active', true)
                ->first();

            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Build the response for an unauthenticated request.
     */
    protected function unauthenticated(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Ensure the resolved user has the admin role.
     *
     * Assumes cas.auth (EnsureAuthenticated) has already run and established
     * both the Auth guard and the session user_id. Aborts with 403 unless the
     * resolved user exists and has the admin role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get('user_id');

        $user = $userId ? User::find($userId) : Auth::user();

        abort_unless($user instanceof User && $user->role === 'admin', 403);

        return $next($request);
    }
}

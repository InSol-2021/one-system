<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait AuthorizesAdmin
{
    /**
     * Authorize that the acting user is an admin.
     *
     * Resolves the acting user from session(user_id), falling back to the Auth
     * guard, and aborts with 403 unless that user exists and has the admin
     * role. Never falls back to "first admin user".
     *
     * Call this at the TOP of mount() in every admin Livewire component, and
     * again in sensitive state-changing actions.
     */
    protected function authorizeAdmin(): void
    {
        $uid = session('user_id');

        $u = $uid ? User::find($uid) : Auth::user();

        abort_unless($u instanceof User && $u->role === 'admin', 403);
    }
}

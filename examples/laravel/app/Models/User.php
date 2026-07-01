<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// The package ships a trait that adds the CAS columns (cas_user, cas_username,
// cas_token, cas_token_expires_at) to a User model. We include it so the model
// matches what the package expects, even though this DB-free sample stores the
// CAS user in the session and does not create local User records by default
// (see config/cas-client.php -> user.create_local_users = false).
use CasSystem\LaravelClient\Traits\CasUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use CasUserTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

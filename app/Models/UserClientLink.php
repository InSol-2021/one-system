<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClientLink extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_user.user_client_links';

    protected $fillable = [
        'user_id',
        'client_system_id',
        'linked_username',
        'encrypted_password',
        'is_active',
        'last_used',
        'permissions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used' => 'datetime',
        'permissions' => 'array',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Client system relationship
     */
    public function clientSystem()
    {
        return $this->belongsTo(ClientSystem::class, 'client_system_id');
    }

    /**
     * Scope for active links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed()
    {
        $this->update(['last_used' => now()]);
    }
}
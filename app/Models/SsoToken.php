<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsoToken extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_user.sso_tokens';

    protected $fillable = [
        'token',
        'token_hash',
        'user_id',
        'client_system_id',
        'user_role',
        'expires_at',
        'is_active',
        'is_used',
        'last_used_at',
        'user_agent',
        'ip_address',
        'user_data',
        'payload',
    ];

    protected $hidden = [
        'token',
        'token_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'user_data' => 'array',
        'payload' => 'array',
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
     * Scope for active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    /**
     * Check if token is expired
     */
    public function isExpired()
    {
        return $this->expires_at < now();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed()
    {
        $this->update([
            'last_used_at' => now(),
        ]);
    }

    /**
     * Deactivate token
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }
}
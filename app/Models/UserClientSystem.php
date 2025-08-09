<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClientSystem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_user.user_client_links';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'client_system_id',
        'linked_username',
        'encrypted_password',
        'is_active',
        'last_used',
        'permissions',
        'show_in_dashboard',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'encrypted_password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'show_in_dashboard' => 'boolean',
        'permissions' => 'array',
        'last_used' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the client system link.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the client system that this link refers to.
     */
    public function clientSystem()
    {
        return $this->belongsTo(ClientSystem::class, 'client_system_id');
    }

    /**
     * Scope to get only active systems.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get systems for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
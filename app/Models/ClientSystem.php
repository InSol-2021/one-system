<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSystem extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     */
    protected $connection = 'cas_system';

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_admin.client_systems';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'client_id',
        'client_secret',
        'webhook_secret',
        'description',
        'callback_url',
        'allowed_scopes',
        'is_active',
        'credentials_viewed',
        'credentials_viewed_at',
        'credentials_viewed_by',
        'credentials_shown',
        'credentials_regenerated_at',
        'credentials_regenerated_by',
        'server_config',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'client_secret',
        'webhook_secret',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'allowed_scopes' => 'array',
        'server_config' => 'array',
        'is_active' => 'boolean',
        'credentials_viewed' => 'boolean',
        'credentials_shown' => 'boolean',
        'credentials_viewed_at' => 'datetime',
        'credentials_regenerated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user client systems for this client system.
     */
    public function userClientSystems()
    {
        return $this->hasMany(UserClientSystem::class, 'client_system_id');
    }

    /**
     * Get the users linked to this client system.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_client_links', 'client_system_id', 'user_id')
            ->withPivot(['linked_username', 'is_active', 'last_used'])
            ->withTimestamps();
    }

    /**
     * Scope to get only active client systems.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the client system is active.
     */
    public function isActive()
    {
        return $this->is_active === true;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ClientSystem extends Model
{
    use HasFactory;

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

    /**
     * Determine whether the given value already looks like a bcrypt hash.
     */
    protected function looksHashed($value): bool
    {
        return is_string($value)
            && (str_starts_with($value, '$2y$') || str_starts_with($value, '$2a$'));
    }

    /**
     * Hash the client secret on assignment unless it already looks hashed
     * (prevents double-hashing of already-stored values).
     */
    public function setClientSecretAttribute($value): void
    {
        if ($value === null || $value === '' || $this->looksHashed($value)) {
            $this->attributes['client_secret'] = $value;

            return;
        }

        $this->attributes['client_secret'] = Hash::make($value);
    }

    /**
     * Hash the webhook secret on assignment unless it already looks hashed
     * (prevents double-hashing of already-stored values).
     */
    public function setWebhookSecretAttribute($value): void
    {
        if ($value === null || $value === '' || $this->looksHashed($value)) {
            $this->attributes['webhook_secret'] = $value;

            return;
        }

        $this->attributes['webhook_secret'] = Hash::make($value);
    }

    /**
     * Verify a plaintext client secret against the stored value.
     *
     * Works before and after the hashing migration: if the stored value is a
     * bcrypt hash, use Hash::check; otherwise fall back to a constant-time
     * comparison against the legacy plaintext value.
     */
    public function verifyClientSecret(?string $plain): bool
    {
        return $this->verifySecret($this->attributes['client_secret'] ?? null, $plain);
    }

    /**
     * Verify a plaintext webhook secret against the stored value.
     */
    public function verifyWebhookSecret(?string $plain): bool
    {
        return $this->verifySecret($this->attributes['webhook_secret'] ?? null, $plain);
    }

    /**
     * Shared verification helper for hashed-or-legacy-plaintext secrets.
     */
    protected function verifySecret(?string $stored, ?string $plain): bool
    {
        if ($stored === null || $stored === '' || $plain === null || $plain === '') {
            return false;
        }

        if ($this->looksHashed($stored)) {
            return Hash::check($plain, $stored);
        }

        return hash_equals($stored, $plain);
    }
}
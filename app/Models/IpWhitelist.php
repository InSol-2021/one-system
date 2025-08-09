<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpWhitelist extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_admin.ip_whitelist';

    protected $fillable = [
        'ip_address',
        'subnet_mask',
        'description',
        'status',
        'is_active',
        'created_by',
        'updated_by',
        'expires_at',
        'last_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * User who added this entry
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active entries
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope for inactive entries
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for recent entries
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if entry is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Get formatted IP rule
     */
    public function getFormattedRuleAttribute()
    {
        return $this->ip_address . ($this->subnet_mask ? '/' . $this->subnet_mask : '');
    }

    /**
     * Check if IP matches this rule
     */
    public function matchesIp($testIp)
    {
        if (!$this->subnet_mask) {
            return $testIp === $this->ip_address;
        }

        $network = ip2long($this->ip_address);
        $mask = ~((1 << (32 - $this->subnet_mask)) - 1);
        $testLong = ip2long($testIp);

        return ($network & $mask) === ($testLong & $mask);
    }
}
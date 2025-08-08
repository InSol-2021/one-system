<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     */
    protected $connection = 'cas_system';

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_audit.audit_logs';

    protected $fillable = [
        'user_id',
        'client_system_id',
        'event_type',
        'action',
        'description',
        'details',
        'success',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'success' => 'boolean',
        'details' => 'array',
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
     * Scope for successful events
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope for failed events
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope for specific event type
     */
    public function scopeEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
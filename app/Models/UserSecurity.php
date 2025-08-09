<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSecurity extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_user.user_security';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_backup_codes',
        'two_factor_setup_at',
        'password_reset_required',
        'failed_login_attempts',
        'locked_until',
        'security_preferences',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'two_factor_enabled' => 'boolean',
        'two_factor_backup_codes' => 'array',
        'two_factor_setup_at' => 'datetime',
        'password_reset_required' => 'boolean',
        'locked_until' => 'datetime',
        'security_preferences' => 'array',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get user security settings for a specific user
     */
    public static function forUser($userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'two_factor_enabled' => false,
                'failed_login_attempts' => 0,
                'password_reset_required' => false,
            ]
        );
    }

    /**
     * Check if user has 2FA enabled
     */
    public function has2faEnabled(): bool
    {
        return $this->two_factor_enabled && !empty($this->two_factor_secret);
    }

    /**
     * Check if 2FA is enabled - accessor for is_2fa_enabled
     */
    public function getIs2faEnabledAttribute()
    {
        return $this->two_factor_enabled && !empty($this->two_factor_secret);
    }

    /**
     * Get the Google2FA secret - alias for compatibility
     */
    public function getGoogle2faSecretAttribute()
    {
        return $this->two_factor_secret;
    }

    /**
     * Get backup codes - alias for compatibility
     */
    public function getBackupCodesAttribute()
    {
        return $this->two_factor_backup_codes ?? [];
    }

    /**
     * Check if user is locked out
     */
    public function isLockedOut(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes(): array
    {
        $backupCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $backupCodes[] = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
        }

        $this->update(['two_factor_backup_codes' => $backupCodes]);

        return $backupCodes;
    }

    /**
     * Enable 2FA for the user
     */
    public function enable2fa(string $secret, array $backupCodes = null): void
    {
        $this->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
            'two_factor_backup_codes' => $backupCodes ?? $this->generateBackupCodes(),
            'two_factor_setup_at' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Disable 2FA for the user
     */
    public function disable2fa(): void
    {
        $this->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_backup_codes' => null,
            'two_factor_setup_at' => null,
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }
}

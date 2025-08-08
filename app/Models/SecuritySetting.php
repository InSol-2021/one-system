<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SecuritySetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cas_admin.security_settings';

    /**
     * The connection name for the model.
     */
    protected $connection = 'cas_system';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'category',
        'is_sensitive',
        'requires_restart',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_sensitive' => 'boolean',
        'requires_restart' => 'boolean',
    ];

    /**
     * Get the default settings
     */
    public static function getDefaults(): array
    {
        return [
            'enable_forgot_password' => true,
            'password_reset_expiry' => 60,
            'max_reset_attempts' => 3,
            'enable_2fa' => false,
            '2fa_method' => 'totp',
            'require_2fa_admin' => false,
            'enable_account_lockout' => true,
            'max_login_attempts' => 5,
            'lockout_duration' => 30,
            'enable_session_timeout' => true,
            'session_timeout_minutes' => 120,
            'force_password_change' => false,
            'password_change_days' => 90,
            'min_password_length' => 8,
            'require_special_chars' => true,
            'require_numbers' => true,
            'require_uppercase' => true,
        ];
    }

    /**
     * Get the current settings as an object with dynamic properties
     */
    public static function current(): object
    {
        $settings = self::all();
        $result = (object) self::getDefaults();

        foreach ($settings as $setting) {
            $value = $setting->setting_value;

            switch ($setting->setting_type) {
                case 'boolean':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'integer':
                    $value = (int) $value;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
                default:
                    break;
            }

            $result->{$setting->setting_key} = $value;
        }

        return $result;
    }

    /**
     * Initialize default settings if they don't exist
     */
    public static function initializeDefaults(): void
    {
        $defaults = self::getDefaults();

        foreach ($defaults as $key => $value) {
            $existing = self::where('setting_key', $key)->first();

            if (!$existing) {
                $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : 'string');
                $category = self::getCategoryForKey($key);

                self::create([
                    'setting_key' => $key,
                    'setting_value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                    'setting_type' => $type,
                    'description' => self::getDescriptionForKey($key),
                    'category' => $category,
                    'is_sensitive' => in_array($key, ['smtp_password']),
                    'requires_restart' => false,
                ]);
            }
        }
    }

    /**
     * Get category for a setting key
     */
    private static function getCategoryForKey(string $key): string
    {
        if (str_contains($key, 'smtp') || str_contains($key, 'email') || str_contains($key, 'forgot_password')) {
            return 'email';
        }
        if (str_contains($key, '2fa')) {
            return 'authentication';
        }
        if (str_contains($key, 'password') || str_contains($key, 'lockout') || str_contains($key, 'session')) {
            return 'security';
        }
        return 'general';
    }

    /**
     * Get description for a setting key
     */
    private static function getDescriptionForKey(string $key): string
    {
        $descriptions = [
            'enable_forgot_password' => 'Enable password reset functionality',
            'password_reset_expiry' => 'Password reset token expiry time in minutes',
            'max_reset_attempts' => 'Maximum password reset attempts per hour',
            'enable_2fa' => 'Enable two-factor authentication',
            '2fa_method' => 'Two-factor authentication method',
            'require_2fa_admin' => 'Require 2FA for admin users',
            'enable_account_lockout' => 'Enable account lockout after failed attempts',
            'max_login_attempts' => 'Maximum login attempts before lockout',
            'lockout_duration' => 'Account lockout duration in minutes',
            'enable_session_timeout' => 'Enable automatic session timeout',
            'session_timeout_minutes' => 'Session timeout in minutes',
            'force_password_change' => 'Force periodic password changes',
            'password_change_days' => 'Days between forced password changes',
            'min_password_length' => 'Minimum password length',
            'require_special_chars' => 'Require special characters in passwords',
            'require_numbers' => 'Require numbers in passwords',
            'require_uppercase' => 'Require uppercase letters in passwords',
        ];

        return $descriptions[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Update a setting value
     */
    public static function updateSetting(string $key, $value): bool
    {
        $setting = self::where('setting_key', $key)->first();

        if ($setting) {
            $setting->setting_value = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
            return $setting->save();
        } else {
            $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : 'string');
            $category = self::getCategoryForKey($key);

            $setting = self::create([
                'setting_key' => $key,
                'setting_value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                'setting_type' => $type,
                'description' => self::getDescriptionForKey($key),
                'category' => $category,
                'is_sensitive' => in_array($key, ['smtp_password']),
                'requires_restart' => false,
            ]);

            return $setting ? true : false;
        }
    }

    /**
     * Update multiple settings at once
     */
    public static function updateSettings(array $settings): bool
    {
        $success = true;

        foreach ($settings as $key => $value) {
            if (!self::updateSetting($key, $value)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Check if 2FA is required for admin users
     */
    public function is2faRequiredForAdmin(): bool
    {
        return $this->enable_2fa && $this->require_2fa_admin;
    }

    /**
     * Check if account lockout is enabled
     */
    public function isAccountLockoutEnabled(): bool
    {
        return $this->enable_account_lockout;
    }

    /**
     * Get password complexity requirements
     */
    public function getPasswordRequirements(): array
    {
        return [
            'min_length' => $this->min_password_length,
            'require_special_chars' => $this->require_special_chars,
            'require_numbers' => $this->require_numbers,
            'require_uppercase' => $this->require_uppercase,
        ];
    }
}

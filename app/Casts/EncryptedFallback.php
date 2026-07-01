<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Encrypts a scalar attribute at rest using the application key.
 *
 * On get(): attempts to decrypt the stored value. If the value was never
 * encrypted (legacy plaintext rows), Crypt::decrypt throws a DecryptException
 * and the raw value is returned unchanged. This makes the cast safe to apply
 * to columns that may still contain pre-migration plaintext.
 *
 * On set(): always encrypts, so all newly written values are encrypted at rest.
 */
class EncryptedFallback implements CastsAttributes
{
    /**
     * Decrypt the stored value, falling back to the raw value if it is not
     * (yet) encrypted.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decrypt($value);
        } catch (DecryptException $e) {
            // Legacy plaintext value that predates encryption-at-rest.
            return $value;
        }
    }

    /**
     * Encrypt the value before persisting it.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return [$key => null];
        }

        return [$key => Crypt::encrypt($value)];
    }
}

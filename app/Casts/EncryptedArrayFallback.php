<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Array variant of EncryptedFallback: stores an array as JSON, encrypted at
 * rest using the application key.
 *
 * On get(): attempts to decrypt then json_decode the stored value. If the
 * value is not encrypted (legacy plaintext JSON rows), the DecryptException is
 * caught and the raw value is json_decoded directly so existing data still
 * reads. A value that is neither valid ciphertext nor JSON is returned as-is.
 *
 * On set(): json_encode then encrypt, so all newly written values are
 * encrypted at rest.
 */
class EncryptedArrayFallback implements CastsAttributes
{
    /**
     * Decrypt + decode the stored value, falling back to raw JSON for legacy
     * (unencrypted) rows.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value === null ? null : [];
        }

        try {
            $decrypted = Crypt::decrypt($value);
        } catch (DecryptException $e) {
            // Legacy plaintext (likely raw JSON) that predates encryption-at-rest.
            $decrypted = $value;
        }

        if (is_array($decrypted)) {
            return $decrypted;
        }

        $decoded = json_decode((string) $decrypted, true);

        return $decoded ?? [];
    }

    /**
     * JSON-encode then encrypt the value before persisting it.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return [$key => null];
        }

        return [$key => Crypt::encrypt(json_encode($value))];
    }
}

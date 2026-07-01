<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Encrypt any pre-existing plaintext 2FA secrets / backup codes in place.
     *
     * Reads raw column values via the query builder (bypassing the model casts)
     * and re-writes them encrypted. Values that are already encrypted are
     * detected by attempting Crypt::decrypt and skipped, making this migration
     * idempotent and safe to re-run.
     */
    public function up(): void
    {
        $targets = [
            'cas_user.users' => ['two_factor_secret', 'two_factor_backup_codes'],
            'cas_user.user_security' => ['two_factor_secret', 'two_factor_backup_codes'],
        ];

        foreach ($targets as $table => $columns) {
            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns) {
                $updates = [];

                foreach ($columns as $column) {
                    $value = $row->{$column} ?? null;

                    if ($value === null || $value === '') {
                        continue;
                    }

                    if ($this->isAlreadyEncrypted($value)) {
                        continue;
                    }

                    $updates[$column] = Crypt::encrypt($value);
                }

                if (! empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            });
        }
    }

    /**
     * Determine whether a stored value is already encrypted by Laravel's Crypt.
     */
    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decrypt($value);

            return true;
        } catch (DecryptException $e) {
            return false;
        }
    }

    /**
     * No-op: decryption back to plaintext at rest is intentionally not provided.
     */
    public function down(): void
    {
        // Intentionally left blank. Reverting would re-expose secrets at rest.
    }
};

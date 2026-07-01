<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Hash any legacy plaintext client_secret / webhook_secret values in place.
     *
     * Idempotent: values that already look like a bcrypt hash (start with the
     * $2y$ / $2a$ prefix) are left untouched, so re-running is a no-op.
     */
    public function up(): void
    {
        DB::table('cas_admin.client_systems')
            ->orderBy('id')
            ->each(function ($system) {
                $updates = [];

                foreach (['client_secret', 'webhook_secret'] as $column) {
                    $value = $system->{$column} ?? null;

                    if ($value === null || $value === '' || $this->looksHashed($value)) {
                        continue;
                    }

                    $updates[$column] = Hash::make($value);
                }

                if (!empty($updates)) {
                    $updates['updated_at'] = now();

                    DB::table('cas_admin.client_systems')
                        ->where('id', $system->id)
                        ->update($updates);
                }
            });
    }

    /**
     * Hashing is irreversible; nothing to roll back.
     */
    public function down(): void
    {
        // No-op: original plaintext secrets are not recoverable.
    }

    /**
     * Determine whether a value already looks like a bcrypt hash.
     */
    private function looksHashed($value): bool
    {
        return is_string($value)
            && (str_starts_with($value, '$2y$') || str_starts_with($value, '$2a$'));
    }
};

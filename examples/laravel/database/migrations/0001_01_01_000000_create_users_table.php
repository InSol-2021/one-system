<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Base users table. Optional for this sample: the demo stores the CAS user in
 * the session and does not create local users by default. It is provided only
 * so that, if you enable CAS_CREATE_LOCAL_USERS=true, the package's callback can
 * create/update a local record. The package also ships its own migration that
 * adds the cas_* columns on top of this table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

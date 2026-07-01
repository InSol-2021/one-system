<?php

use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Database configuration
|--------------------------------------------------------------------------
|
| This sample keeps SESSION/CACHE on the file driver (see config/session.php
| and config/cache.php) so the CAS flow stays DB-free. The only database here
| is a small SQLite file used by the LOCAL username/password store
| (App\Support\LocalUserStore), which lets a visitor sign in with a local
| account instead of — or in addition to — CAS single sign-on.
|
| LocalUserStore talks to this file directly via PDO (pdo_sqlite), so it works
| even without running `php artisan migrate`. This config block simply makes the
| default connection and the file location explicit and overridable.
|
*/

return [
    'default' => env('DB_CONNECTION', 'sqlite'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            // The local-auth SQLite file. The Dockerfile makes this dir writable.
            'database' => env('DB_DATABASE', database_path('app.db')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];

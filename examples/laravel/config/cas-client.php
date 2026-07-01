<?php

/*
|--------------------------------------------------------------------------
| CAS client configuration
|--------------------------------------------------------------------------
|
| This file is a copy of the package's own config/cas-client.php (from
| cas-system/laravel-client). The package merges its defaults at runtime,
| but shipping this copy makes the settings explicit and tweakable for the
| sample. We did NOT modify the package itself.
|
| The package reads CAS_SERVER_URL / CAS_CLIENT_ID / CAS_CLIENT_SECRET /
| CAS_CALLBACK_URL from the environment — see .env.example.
|
*/

return [
    // CAS server origin used for SERVER-TO-SERVER token validation (the internal
    // back-channel). (The task refers to this as CAS_BASE_URL; the package env
    // key is CAS_SERVER_URL — set both to the same value in .env.)
    'server_url' => env('CAS_SERVER_URL', env('CAS_BASE_URL', 'http://127.0.0.1:8001')),

    // PUBLIC, browser-facing CAS origin used ONLY to build the /sso/login
    // redirect the user's BROWSER follows. In split-horizon deployments the
    // browser reaches CAS at a public host (CAS_PUBLIC_URL) while this app's
    // container validates tokens against the internal server_url above. Falls
    // back to server_url when CAS_PUBLIC_URL is empty, so local single-url dev
    // is unchanged.
    'public_url' => env('CAS_PUBLIC_URL', env('CAS_SERVER_URL', env('CAS_BASE_URL'))),

    // Credentials registered for THIS client app in the CAS server.
    'client_id' => env('CAS_CLIENT_ID'),
    'client_secret' => env('CAS_CLIENT_SECRET'),

    // Where the CAS server redirects after a successful login.
    'callback_url' => env('CAS_CALLBACK_URL', env('APP_URL').'/callback'),

    // Security settings. This sample keeps signature validation OFF for
    // simplicity; flip CAS_ENABLE_SIGNATURE_VALIDATION=true (and set
    // CAS_SIGNATURE_SECRET) if your CAS server requires signed requests.
    'enable_signature_validation' => env('CAS_ENABLE_SIGNATURE_VALIDATION', false),
    'signature_secret' => env('CAS_SIGNATURE_SECRET', 'default-signature-secret'),
    'verify_ssl' => env('CAS_VERIFY_SSL', true),

    'timeout' => env('CAS_TIMEOUT', 30),

    'routes' => [
        // We define our own /login, /callback, /logout routes in routes/web.php,
        // so the package's auto-registered /cas/* routes are disabled here.
        'enabled' => env('CAS_ROUTES_ENABLED', false),
        'prefix' => env('CAS_ROUTES_PREFIX', 'cas'),
        'middleware' => ['web'],
        'user_dashboard' => env('CAS_USER_DASHBOARD', '/'),
    ],

    'user' => [
        // This sample is DB-free and stores the CAS user only in the session,
        // so we do NOT create local User records.
        'create_local_users' => env('CAS_CREATE_LOCAL_USERS', false),
        'model' => env('CAS_USER_MODEL', App\Models\User::class),
        'defaults' => [
            'user_type' => 'Guest',
        ],
    ],

    'cache' => [
        'enabled' => env('CAS_CACHE_ENABLED', true),
        'ttl' => env('CAS_CACHE_TTL', 3600),
        'prefix' => 'cas_',
    ],

    'logging' => [
        'enabled' => env('CAS_LOGGING_ENABLED', true),
        'channel' => env('CAS_LOG_CHANNEL', 'single'),
        'level' => env('CAS_LOG_LEVEL', 'info'),
    ],
];

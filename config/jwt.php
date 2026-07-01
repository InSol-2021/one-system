<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Signing Secret
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign and verify SSO JWTs (HS256). This MUST be
    | provided via the JWT_SECRET environment variable. There is intentionally
    | no fallback value: application code that reads config('jwt.secret') must
    | fail closed (throw) when this is empty so a missing key never silently
    | degrades to a guessable default.
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Time To Live (seconds)
    |--------------------------------------------------------------------------
    |
    | Lifetime of issued SSO tokens, in seconds. Defaults to 28800 (8 hours).
    |
    */

    'ttl' => (int) env('JWT_TTL', 28800),

];

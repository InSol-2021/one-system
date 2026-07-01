<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Application bootstrap (Laravel 11 style)
|--------------------------------------------------------------------------
|
| The CAS client package (cas-system/laravel-client) auto-registers its
| service provider via composer "extra.laravel.providers" and binds the
| "cas-client" service + the CasClient facade. We do not need to register
| anything manually here.
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // The package exposes the 'cas.auth' and 'cas.role' middleware aliases
        // automatically through its service provider.
        //
        // POST /login is dual-purpose: a browser login AND the CAS server's
        // link-validation endpoint (it POSTs {username,password,client_validation}
        // server-to-server, with no CSRF token and no browser cookies). Exclude
        // that path from CSRF so the validation call works; the handler still
        // checks the credentials against the SQLite store before answering.
        $middleware->validateCsrfTokens(except: [
            'login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

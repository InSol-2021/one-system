<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IpWhitelistMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '*'));

        // Stateless API / SSO endpoints are authenticated by client credentials or
        // tokens, not session cookies, so they must be exempt from web CSRF (these
        // routes live in web.php and would otherwise inherit the web group's CSRF).
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'sso/*',
        ]);

        $middleware->alias([
            'cas.auth' => \App\Http\Middleware\EnsureAuthenticated::class,
            'cas.admin' => \App\Http\Middleware\EnsureAdmin::class,
            'ip.whitelist' => IpWhitelistMiddleware::class,
            'rate_limit_login' => \App\Http\Middleware\RateLimitLogin::class,
            'account_lockout' => \App\Http\Middleware\AccountLockoutMiddleware::class,
        ]);

        $middleware->group('api.protected', [
            IpWhitelistMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (! config('app.debug') && ($request->expectsJson() || $request->is('api/*'))) {
                $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                    ? $e->getStatusCode()
                    : 500;

                return response()->json(['message' => 'Server Error.'], $status);
            }

            return null;
        });
    })->create();

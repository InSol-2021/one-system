<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Admin\ClientSystemController;
use App\Http\Controllers\Public\DocumentationController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\IpWhitelistController;
use App\Http\Controllers\User\UserDashboardController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('home');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post')
    ->middleware(['rate_limit_login', 'account_lockout']);

// 2FA Routes
Route::get('/auth/2fa', [App\Http\Controllers\Auth2FAController::class, 'show2FA'])->name('auth.2fa');
Route::post('/auth/2fa/verify', [App\Http\Controllers\Auth2FAController::class, 'verify2FA'])->name('auth.2fa.verify');
Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/register', [AuthController::class, 'register']);
Route::post('/api/logout', [AuthController::class, 'logout']);
Route::get('/api/user', [AuthController::class, 'user']);

// SSO Routes
Route::post('/api/sso/token', [AuthController::class, 'generateSSOToken'])->middleware('ip.whitelist');
Route::post('/api/validate-token', [AuthController::class, 'validateSsoToken'])->middleware('ip.whitelist');
Route::get('/sso/login', [AuthController::class, 'ssoLogin'])->name('sso.login');
Route::get('/auth/sso/callback', [AuthController::class, 'ssoCallback'])->name('sso.callback');
Route::post('/api/sso/process', [AuthController::class, 'processSSOCallback']);

// User Dashboard Routes
Route::middleware(['cas.auth'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/profile', function() { return view('user.user-profile-livewire'); })->name('user.profile');
    Route::get('/user/profile-livewire', function() { return view('user.user-profile-livewire'); })->name('user.profile.livewire');
});
Route::get('/api/user/dashboard', [UserDashboardController::class, 'dashboard']);


Route::post('/api/user/link-client-system', [UserDashboardController::class, 'linkClientSystem']);
Route::post('/api/user/login-client-system/{clientSystemId}', [UserDashboardController::class, 'loginToClientSystem']);
Route::delete('/api/user/unlink-client-system/{clientSystemId}', [UserDashboardController::class, 'unlinkClientSystem']);

// Admin routes (authenticated admins only)
Route::middleware(['cas.auth', 'cas.admin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', function() { return view('admin.dashboard-livewire'); })->name('admin.dashboard');
    Route::get('/admin', function() { return redirect('/admin/dashboard'); });

    // Client System Management
    Route::get('/admin/client-systems', function() { return view('admin.client-systems-livewire'); })->name('admin.client-systems.livewire');

    // Legacy API routes
    Route::get('/api/client-systems', [ClientSystemController::class, 'index']);
    Route::post('/api/client-systems', [ClientSystemController::class, 'store']);
    Route::put('/api/client-systems/{id}', [ClientSystemController::class, 'update']);
    Route::delete('/api/client-systems/{id}', [ClientSystemController::class, 'destroy']);
    Route::post('/api/client-systems/{id}/mark-credentials-viewed', [ClientSystemController::class, 'markCredentialsViewed']);
    Route::post('/api/client-systems/{id}/regenerate-credentials', [ClientSystemController::class, 'regenerateCredentials']);

    // IP Whitelist Management
    Route::get('/admin/ip-whitelist', function() { return view('admin.ip-whitelist-livewire'); })->name('admin.ip-whitelist.livewire');

    // Legacy API routes for IP whitelist
    Route::get('/api/ip-whitelist', [IpWhitelistController::class, 'list']);
    Route::post('/api/ip-whitelist', [IpWhitelistController::class, 'store']);
    Route::put('/api/ip-whitelist/{id}', [IpWhitelistController::class, 'update']);
    Route::delete('/api/ip-whitelist/{id}', [IpWhitelistController::class, 'destroy']);

    // User Management - Livewire Implementation
    Route::get('/admin/users', function() { return view('admin.users-livewire'); })->name('admin.users.livewire');

    // Audit Logs - Livewire Implementation
    Route::get('/admin/audit-logs', function() { return view('admin.audit-logs-livewire'); })->name('admin.audit-logs.livewire');

    // SSO Settings - Livewire Implementation
    Route::get('/admin/sso-settings', function() { return view('admin.sso-settings-livewire'); })->name('admin.sso-settings.livewire');

    // Admin Profile - Livewire Implementation
    Route::get('/admin/profile', function() { return view('admin.profile-livewire'); })->name('admin.profile.livewire');

    // Admin Security Settings - Livewire Implementation
    Route::get('/admin/security-settings', function() { return view('admin.security-settings-livewire'); })->name('admin.security-settings.livewire');

    // Legacy API routes for audit logs (export declared before {id} to avoid wildcard shadowing)
    Route::get('/api/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/api/audit-logs/export', [AuditLogController::class, 'export']);
    Route::get('/api/audit-logs/{id}', [AuditLogController::class, 'show'])->whereNumber('id');
    Route::get('/api/audit-logs-stats', [AuditLogController::class, 'stats']);
});

// Logout Routes (POST only — GET logout removed to prevent CSRF/forced logout)
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/auth/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/auth/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/auth/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Documentation Routes
Route::get('/docs', [DocumentationController::class, 'index'])->name('docs');
Route::get('/docs/system-architecture', function() { return view('public.documentation.system-architecture'); })->name('docs.architecture');

// Specific documentation section routes
Route::get('/docs/laravel', [DocumentationController::class, 'laravel'])->name('docs.laravel');
Route::get('/docs/dotnet', [DocumentationController::class, 'dotnet'])->name('docs.dotnet');
Route::get('/docs/nodejs', [DocumentationController::class, 'nodejs'])->name('docs.nodejs');
Route::get('/docs/java', [DocumentationController::class, 'java'])->name('docs.java');
Route::get('/docs/python', [DocumentationController::class, 'python'])->name('docs.python');
Route::get('/docs/javascript', [DocumentationController::class, 'javascript'])->name('docs.javascript');
Route::get('/docs/react', [DocumentationController::class, 'react'])->name('docs.react');
Route::get('/docs/nextjs', [DocumentationController::class, 'nextjs'])->name('docs.nextjs');
Route::get('/docs/angular', [DocumentationController::class, 'angular'])->name('docs.angular');
Route::get('/docs/vue', [DocumentationController::class, 'vue'])->name('docs.vue');
Route::get('/docs/rust', [DocumentationController::class, 'rust'])->name('docs.rust');
Route::get('/docs/api', [DocumentationController::class, 'api'])->name('docs.api.overview');
Route::get('/docs/security', [DocumentationController::class, 'security'])->name('docs.security');
Route::get('/docs/deployment', [DocumentationController::class, 'deployment'])->name('docs.deployment');
Route::get('/docs/troubleshooting', [DocumentationController::class, 'troubleshooting'])->name('docs.troubleshooting');
Route::get('/docs/examples', [DocumentationController::class, 'examples'])->name('docs.examples');
Route::get('/docs/quick-start', function() { return view('public.documentation.quick-start'); })->name('docs.quick-start');
Route::get('/docs/admin-panel', function() { return view('public.documentation.admin-panel'); })->name('docs.admin-panel');
Route::get('/docs/client-registration', function() { return view('public.documentation.client-registration'); })->name('docs.client-registration');
Route::get('/docs/user-management', function() { return view('public.documentation.user-management'); })->name('docs.user-management');
Route::get('/docs/two-factor-auth', function() { return view('public.documentation.two-factor-auth'); })->name('docs.two-factor-auth');
Route::get('/docs/changelog', function() { return view('public.documentation.changelog'); })->name('docs.changelog');
Route::get('/docs/webhooks', function() { return view('public.documentation.webhooks'); })->name('docs.webhooks');
Route::get('/docs/sdks', function() { return view('public.documentation.sdks'); })->name('docs.sdks');
Route::get('/docs/user-guide', function() { return view('public.documentation.user-guide'); })->name('docs.user-guide');
Route::get('/docs/security-guide', function() { return view('public.documentation.security-guide'); })->name('docs.security-guide');

Route::get('/docs/api/{endpoint}', [DocumentationController::class, 'apiEndpoint'])->name('docs.api');

Route::get('/docs/{section}', [DocumentationController::class, 'section'])->name('docs.section');

Route::get('/downloads/one-system-client-package.zip', function() {
    $file = public_path('downloads/one-system-client-package.zip');
    if (!file_exists($file)) {
        abort(404, 'Package not found');
    }
    return response()->download($file);
})->name('downloads.laravel-package');

// SDK Package Downloads
$sdkPackages = [
    'nodejs-cas-client' => 'downloads.nodejs-package',
    'python-cas-client' => 'downloads.python-package',
    'java-cas-client' => 'downloads.java-package',
    'dotnet-cas-client' => 'downloads.dotnet-package',
    'javascript-cas-client' => 'downloads.javascript-package',
    'react-cas-client' => 'downloads.react-package',
    'nextjs-cas-client' => 'downloads.nextjs-package',
    'angular-cas-client' => 'downloads.angular-package',
    'vue-cas-client' => 'downloads.vue-package',
];

foreach ($sdkPackages as $package => $routeName) {
    Route::get("/downloads/{$package}.zip", function() use ($package) {
        $file = public_path("downloads/{$package}.zip");
        if (!file_exists($file)) {
            abort(404, 'Package not found');
        }
        return response()->download($file);
    })->name($routeName);
}

Route::get('/health/database', function() {
    try {
        // Connectivity probe only — do not expose entity counts publicly.
        DB::table('cas_user.users')->limit(1)->count();

        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        Log::error('Health check (database) failed', ['exception' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'database' => 'disconnected',
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('health.database');

Route::get('/health', function() {
    try {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        Log::error('Health check failed', ['exception' => $e->getMessage()]);
        return response()->json([
            'status' => 'unhealthy',
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('health');

Route::get('/health/full', function() {
    try {
        // Connectivity probe only — do not expose version/memory/environment or entity counts publicly.
        DB::table('cas_user.users')->limit(1)->count();

        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        Log::error('Health check (full) failed', ['exception' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'database' => 'disconnected',
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('health.full');

Route::get('/health/redis', function() {
    try {
        $cacheStore = config('cache.default');
        $redisConfigured = $cacheStore === 'redis' || in_array('redis', array_keys(config('cache.stores', [])));

        if (!$redisConfigured) {
            return response()->json([
                'status' => 'not_configured',
                'redis' => 'not_configured',
                'timestamp' => now()->toISOString()
            ]);
        }

        $testKey = 'health_check_' . time();
        $testValue = 'test_value_' . uniqid();

        Cache::put($testKey, $testValue, 60);
        $retrievedValue = Cache::get($testKey);
        Cache::forget($testKey);

        $testPassed = ($retrievedValue === $testValue);

        return response()->json([
            'status' => $testPassed ? 'healthy' : 'warning',
            'redis' => $testPassed ? 'connected' : 'degraded',
            'timestamp' => now()->toISOString()
        ]);

    } catch (\Exception $e) {
        Log::error('Health check (redis) failed', ['exception' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'redis' => 'failed',
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('health.redis');

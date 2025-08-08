<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SignatureService;

class SignatureValidationMiddleware
{
    protected $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    /**
     * Handle an incoming request and validate signature.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldSkipValidation($request)) {
            return $next($request);
        }

        if (!$this->requiresSignatureValidation($request)) {
            return $next($request);
        }

        $signature = $request->header('X-CAS-Signature');
        $timestamp = $request->header('X-CAS-Timestamp');
        $clientId = $request->header('X-CAS-Client-ID');

        if (!$signature || !$timestamp || !$clientId) {
            $this->logFailedValidation($request, 'Missing signature headers');
            return response()->json([
                'error' => 'Invalid request signature',
                'message' => 'Missing required signature headers'
            ], 401);
        }

        $currentTime = time();
        $requestTime = (int) $timestamp;

        if (abs($currentTime - $requestTime) > 300) {
            $this->logFailedValidation($request, 'Request timestamp expired');
            return response()->json([
                'error' => 'Request expired',
                'message' => 'Request timestamp is too old or too far in the future'
            ], 401);
        }

        $clientSystem = DB::table('client_systems')
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->first();

        if (!$clientSystem) {
            $this->logFailedValidation($request, 'Unknown client system');
            return response()->json([
                'error' => 'Unknown client',
                'message' => 'Client system not registered'
            ], 401);
        }

        if (!$clientSystem->signature_validation_enabled) {
            $this->logAuditEvent($request, 'signature_validation_skipped', 'Signature validation disabled for client', [
                'client_id' => $clientId,
                'client_system_id' => $clientSystem->id
            ]);
            return $next($request);
        }

        $body = $request->getContent();
        $isValid = $this->signatureService->validateRequestSignature(
            $request->method(),
            $request->path(),
            $body,
            $signature,
            $requestTime,
            $clientSystem->client_secret
        );

        if (!$isValid) {
            $this->logFailedValidation($request, 'Invalid signature', $clientSystem->id);
            return response()->json([
                'error' => 'Invalid signature',
                'message' => 'Request signature validation failed'
            ], 401);
        }

        $this->logSuccessfulValidation($request, $clientSystem->id);

        $request->attributes->set('client_system', $clientSystem);
        $request->attributes->set('signature_validated', true);

        return $next($request);
    }

    /**
     * Check if signature validation should be skipped for this request
     */
    protected function shouldSkipValidation(Request $request): bool
    {
        $skipPaths = [
            '/dashboard',
            '/docs',
            '/login',
            '/logout',
            '/register',
            '/css/',
            '/js/',
            '/images/',
            '/favicon.ico'
        ];

        $path = $request->path();

        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, trim($skipPath, '/'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this request requires signature validation
     */
    protected function requiresSignatureValidation(Request $request): bool
    {
        $requiresPaths = [
            'api/sso/',
            'api/validate-token',
            'api/client-callback'
        ];

        $path = $request->path();

        foreach ($requiresPaths as $requirePath) {
            if (str_starts_with($path, $requirePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log failed signature validation
     */
    protected function logFailedValidation(Request $request, string $reason, ?int $clientSystemId = null)
    {
        $this->logAuditEvent($request, 'signature_validation_failed', $reason, [
            'client_system_id' => $clientSystemId,
            'signature_present' => $request->hasHeader('X-CAS-Signature'),
            'timestamp_present' => $request->hasHeader('X-CAS-Timestamp'),
            'client_id_present' => $request->hasHeader('X-CAS-Client-ID')
        ]);
    }

    /**
     * Log successful signature validation
     */
    protected function logSuccessfulValidation(Request $request, int $clientSystemId)
    {
        $this->logAuditEvent($request, 'signature_validation_success', 'Request signature validated successfully', [
            'client_system_id' => $clientSystemId
        ]);
    }

    /**
     * Log audit event
     */
    protected function logAuditEvent(Request $request, string $action, string $description, array $metadata = [])
    {
        try {
            DB::table('audit_logs')->insert([
                'user_id' => null,
                'action' => $action,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => json_encode(array_merge($metadata, [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'headers' => [
                        'x-cas-client-id' => $request->header('X-CAS-Client-ID'),
                        'x-cas-timestamp' => $request->header('X-CAS-Timestamp')
                    ]
                ])),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log audit event', [
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }
}

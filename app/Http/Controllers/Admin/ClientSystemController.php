<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ClientSystem;
use App\Models\AuditLog;
use App\Models\IpWhitelist;
use App\Models\SsoToken;
use App\Models\User;

class ClientSystemController extends Controller
{
    /**
     * Resolve the acting user and ensure they are an authenticated admin.
     *
     * Defense-in-depth on top of route middleware: returns a JSON error
     * response when the session does not map to an active admin user, or null
     * when the request may proceed.
     */
    protected function denyUnlessAdmin(): ?\Illuminate\Http\JsonResponse
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return null;
    }

    public function index()
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $userId = session('user_id');

        try {
            $clientSystems = ClientSystem::select(
                    'id',
                    'name',
                    'domain',
                    'callback_url',
                    'client_id',
                    'status',
                    'last_accessed',
                    'credentials_shown',
                    'credentials_viewed_at',
                    'credentials_regenerated_at',
                    'created_at'
                )
                ->orderBy('created_at', 'desc')
                ->get();

            $clientSystemsWithStatus = $clientSystems->map(function ($system) {
                return [
                    'id' => $system->id,
                    'name' => $system->name,
                    'domain' => $system->domain,
                    'callback_url' => $system->callback_url,
                    'client_id' => $system->client_id,
                    'status' => $system->status,
                    'last_accessed' => $system->last_accessed,
                    'created_at' => $system->created_at,
                    'security_status' => [
                        'credentials_shown' => (bool) $system->credentials_shown,
                        'credentials_viewed_at' => $system->credentials_viewed_at,
                        'credentials_regenerated_at' => $system->credentials_regenerated_at,
                        'can_view_credentials' => !$system->credentials_shown,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'client_systems' => $clientSystemsWithStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch client systems', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch client systems'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $userId = session('user_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|url',
            'callback_url' => 'required|url',
        ]);

        $clientId = 'client_' . bin2hex(random_bytes(8));
        $plainClientSecret = bin2hex(random_bytes(32));
        $plainWebhookSecret = bin2hex(random_bytes(32));

        try {
            // Plaintext secrets are passed to the model, whose mutators hash
            // them at rest. The plaintext is returned to the admin once below.
            $clientSystem = ClientSystem::create([
                'name' => $request->name,
                'domain' => $request->domain,
                'callback_url' => $request->callback_url,
                'client_id' => $clientId,
                'client_secret' => $plainClientSecret,
                'signature_validation_enabled' => true,
                'webhook_secret' => $plainWebhookSecret,
                'status' => 'active',
                'is_active' => true,
                'credentials_shown' => false,
            ]);

            AuditLog::create([
                'user_id' => $userId,
                'client_system_id' => $clientSystem->id,
                'event_type' => 'client_system_created',
                'action' => 'create_client_system',
                'description' => "Created client system: {$request->name}",
                'details' => [
                    'client_system_name' => $request->name,
                    'domain' => $request->domain,
                    'client_id' => $clientSystem->client_id,
                    'credentials_generated' => true
                ],
                'success' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client system created successfully',
                'client_system' => [
                    'id' => $clientSystem->id,
                    'name' => $clientSystem->name,
                    'domain' => $clientSystem->domain,
                    'callback_url' => $clientSystem->callback_url,
                    'client_id' => $clientSystem->client_id,
                    'status' => $clientSystem->status,
                    'created_at' => $clientSystem->created_at,
                ],
                'sensitive_credentials' => [
                    'client_secret' => $plainClientSecret,
                    'webhook_secret' => $plainWebhookSecret,
                    'show_once' => true,
                    'warning' => 'These credentials will only be shown once. Copy them now!'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Client system creation failed', [
                'error' => $e->getMessage(),
                'generated_client_id' => $clientId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create client system'
            ], 500);
        }
    }

    /**
     * Mark credentials as viewed (security measure)
     */
    public function markCredentialsViewed(Request $request, $id)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $userId = session('user_id');

        try {
            $updated = ClientSystem::query()
                ->where('id', $id)
                ->update([
                    'credentials_shown' => true,
                    'credentials_viewed_at' => now(),
                    'credentials_viewed_by' => $userId,
                    'updated_at' => now()
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client system not found'
                ], 404);
            }

            AuditLog::query()->insert([
                'user_id' => $userId,
                'client_system_id' => $id,
                'event_type' => 'security_event',
                'action' => 'credentials_viewed',
                'description' => 'Client system credentials marked as viewed',
                'details' => json_encode([
                    'viewed_at' => now(),
                    'security_note' => 'Credentials will no longer be accessible'
                ]),
                'success' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Credentials marked as viewed'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark credentials as viewed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark credentials as viewed'
            ], 500);
        }
    }

    /**
     * Regenerate client credentials (emergency use only)
     */
    public function regenerateCredentials(Request $request, $id)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $userId = session('user_id');

        $request->validate([
            'confirm_regeneration' => 'required|boolean|accepted',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $clientSystem = ClientSystem::query()->find($id);

            if (!$clientSystem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client system not found'
                ], 404);
            }

            $newClientSecret = bin2hex(random_bytes(32));
            $newWebhookSecret = bin2hex(random_bytes(32));

            // Assign via the model so the mutators hash the secrets at rest;
            // the plaintext is returned to the admin once below.
            $clientSystem->client_secret = $newClientSecret;
            $clientSystem->webhook_secret = $newWebhookSecret;
            $clientSystem->credentials_shown = false; // Reset to allow one-time viewing
            $clientSystem->credentials_regenerated_at = now();
            $clientSystem->credentials_regenerated_by = $userId;
            $clientSystem->save();

            SsoToken::query()
                ->where('client_system_id', $id)
                ->update(['is_active' => false]);

            AuditLog::query()->insert([
                'user_id' => $userId,
                'client_system_id' => $id,
                'event_type' => 'critical_security_event',
                'action' => 'credentials_regenerated',
                'description' => "Client system credentials regenerated: {$clientSystem->name}",
                'details' => json_encode([
                    'reason' => $request->reason,
                    'regenerated_at' => now(),
                    'all_tokens_invalidated' => true,
                    'old_client_id' => $clientSystem->client_id
                ]),
                'success' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Credentials regenerated successfully',
                'warning' => 'All existing SSO tokens have been invalidated',
                'client_system' => [
                    'id' => $clientSystem->id,
                    'name' => $clientSystem->name,
                    'client_id' => $clientSystem->client_id,
                ],
                'sensitive_credentials' => [
                    'client_secret' => $newClientSecret,
                    'webhook_secret' => $newWebhookSecret,
                    'show_once' => true,
                    'warning' => 'These new credentials will only be shown once. Copy them now!'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to regenerate credentials', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate credentials'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $userId = session('user_id');

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'domain' => 'sometimes|url',
            'callback_url' => 'sometimes|url',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $updateData = $request->only(['name', 'domain', 'callback_url', 'status']);

        $updateData['updated_at'] = now();

        $updated = ClientSystem::query()
            ->where('id', $id)
            ->update($updateData);

        if (!$updated) {
            return response()->json(['error' => 'Client system not found'], 404);
        }

        AuditLog::query()->insert([
            'user_id' => $userId,
            'action' => 'update_client',
            'details' => json_encode(['client_system_id' => $id, 'updated_fields' => array_keys($updateData)]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clientSystem = ClientSystem::query()->find($id);

        return response()->json($clientSystem);
    }

    public function destroy($id)
    {
        if ($denied = $this->denyUnlessAdmin()) {
            return $denied;
        }

        $deleted = ClientSystem::query()
            ->where('id', $id)
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'Client system not found'], 404);
        }

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;
use App\Models\IpWhitelist;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelistController extends Controller
{
    /**
     * Defense-in-depth admin gate. Routes are gated by middleware, but each
     * public action re-verifies that the acting session user is an active admin.
     * Returns null when authorized, or a JSON error response when not.
     */
    private function ensureAdmin(): ?Response
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $user = User::find($userId);

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        return null;
    }

    /**
     * Get all IP whitelist entries
     */
    public function list()
    {
        if ($denied = $this->ensureAdmin()) {
            return $denied;
        }

        try {
            $entries = IpWhitelist::with('addedBy:id,username')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'ip_whitelist' => $entries
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch IP whitelist', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch IP whitelist'
            ], 500);
        }
    }

    /**
     * Alias for list(). Kept so the GET listing route resolves whether it is
     * bound to index() (legacy binding) or list() (after the route fix).
     */
    public function index()
    {
        return $this->list();
    }

    /**
     * Add IP to whitelist
     */
    public function store(Request $request)
    {
        if ($denied = $this->ensureAdmin()) {
            return $denied;
        }

        $request->validate([
            'ip_address' => 'required|ip',
            'subnet_mask' => 'nullable|integer|min:1|max:32',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $entry = IpWhitelist::create([
                'ip_address' => $request->ip_address,
                'subnet_mask' => $request->subnet_mask,
                'description' => $request->description,
                'status' => 'active',
                'added_by' => session('user_id'),
            ]);

            AuditLog::create([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'add_ip_whitelist',
                'description' => "Added IP to whitelist: {$request->ip_address}",
                'details' => json_encode([
                    'ip_address' => $request->ip_address,
                    'subnet_mask' => $request->subnet_mask,
                    'description' => $request->description
                ]),
                'success' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'entry' => $entry
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to add IP to whitelist', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add IP to whitelist'
            ], 500);
        }
    }

    /**
     * Update IP whitelist entry
     */
    public function update(Request $request, $id)
    {
        if ($denied = $this->ensureAdmin()) {
            return $denied;
        }

        $request->validate([
            'ip_address' => 'sometimes|ip',
            'subnet_mask' => 'nullable|integer|min:1|max:32',
            'description' => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ]);

        try {
            $updateData = $request->only(['ip_address', 'subnet_mask', 'description', 'status']);
            $updateData['updated_at'] = now();

            $updated = IpWhitelist::query()
                ->where('id', $id)
                ->update($updateData);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'IP whitelist entry not found'
                ], 404);
            }

            $entry = IpWhitelist::query()->find($id);

            AuditLog::query()->insert([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'update_ip_whitelist',
                'description' => "Updated IP whitelist entry: {$entry->ip_address}",
                'details' => json_encode($updateData),
                'success' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'entry' => $entry
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update IP whitelist entry', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update IP whitelist entry'
            ], 500);
        }
    }

    /**
     * Delete IP whitelist entry
     */
    public function destroy($id)
    {
        if ($denied = $this->ensureAdmin()) {
            return $denied;
        }

        try {
            $entry = IpWhitelist::query()->find($id);

            if (!$entry) {
                return response()->json([
                    'success' => false,
                    'message' => 'IP whitelist entry not found'
                ], 404);
            }

            IpWhitelist::query()
                ->where('id', $id)
                ->delete();

            AuditLog::query()->insert([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'delete_ip_whitelist',
                'description' => "Removed IP from whitelist: {$entry->ip_address}",
                'details' => json_encode([
                    'ip_address' => $entry->ip_address,
                    'subnet_mask' => $entry->subnet_mask,
                    'description' => $entry->description
                ]),
                'success' => true,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'IP whitelist entry deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete IP whitelist entry', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete IP whitelist entry'
            ], 500);
        }
    }

    /**
     * Add current IP to whitelist
     */
    public function addCurrentIp(Request $request)
    {
        if ($denied = $this->ensureAdmin()) {
            return $denied;
        }

        $request->validate([
            'description' => 'nullable|string|max:255',
        ]);

        $currentIp = $request->ip();

        try {
            $existing = IpWhitelist::query()
                ->where('ip_address', $currentIp)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'IP address already exists in whitelist',
                    'ip' => $currentIp
                ], 409);
            }

            $entryId = IpWhitelist::query()->insertGetId([
                'ip_address' => $currentIp,
                'subnet_mask' => 'full',
                'description' => $request->description ?: "Auto-added current IP: {$currentIp}",
                'is_active' => true,
                'created_by' => session('user_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $entry = IpWhitelist::query()->find($entryId);

            AuditLog::query()->insert([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'add_current_ip',
                'description' => "Added current IP to whitelist: {$currentIp}",
                'details' => json_encode([
                    'ip_address' => $currentIp,
                    'auto_added' => true
                ]),
                'success' => true,
                'ip_address' => $currentIp,
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'entry' => $entry,
                'message' => "Successfully added current IP ({$currentIp}) to whitelist"
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to add current IP to whitelist', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add current IP to whitelist'
            ], 500);
        }
    }

    /**
     * Test IP access
     */
    public function testAccess(Request $request)
    {
        if ($denied = $this->ensureAdmin()) {
            return $denied;
        }

        $request->validate([
            'test_ip' => 'required|ip',
        ]);

        $testIp = $request->test_ip;
        $currentIp = $request->ip();

        try {
            $whitelistEntries = DB::table('cas_admin.ip_whitelist')->where('status', 'active')->get();

            $isAllowed = false;
            $matchedRule = null;

            foreach ($whitelistEntries as $entry) {
                if ($this->matchesIpRule($testIp, $entry->ip_address, $entry->subnet_mask)) {
                    $isAllowed = true;
                    $matchedRule = $entry;
                    break;
                }
            }

            return response()->json([
                'success' => true,
                'test_ip' => $testIp,
                'current_ip' => $currentIp,
                'is_allowed' => $isAllowed,
                'matched_rule' => $matchedRule ? [
                    'ip_address' => $matchedRule->ip_address,
                    'subnet_mask' => $matchedRule->subnet_mask,
                    'description' => $matchedRule->description,
                    'rule' => $matchedRule->ip_address . ($matchedRule->subnet_mask ? '/' . $matchedRule->subnet_mask : '')
                ] : null,
                'total_rules' => count($whitelistEntries)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to test IP access', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to test IP access'
            ], 500);
        }
    }

    /**
     * Check if IP matches whitelist rule (supports CIDR notation)
     */
    private function matchesIpRule(string $ip, string $whitelistIp, ?string $subnetMask): bool
    {
        if ($ip === $whitelistIp) {
            return true;
        }

        if ($subnetMask) {
            $cidr = $whitelistIp . '/' . $subnetMask;
            return $this->ipInCidr($ip, $cidr);
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range
     */
    private function ipInCidr(string $ip, string $cidr): bool
    {
        list($subnet, $mask) = explode('/', $cidr);

        if (!filter_var($ip, FILTER_VALIDATE_IP) || !filter_var($subnet, FILTER_VALIDATE_IP)) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    /**
     * Get IP whitelist statistics
     */
    public function stats()
    {
        if ($denied = $this->ensureAdmin()) {
            return $denied;
        }

        try {
            $stats = [
                'total_entries' => IpWhitelist::query()->count(),
                'active_entries' => IpWhitelist::query()->where('status', 'active')->count(),
                'inactive_entries' => IpWhitelist::query()->where('status', 'inactive')->count(),
                'recent_additions' => IpWhitelist::query()
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get IP whitelist stats', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get IP whitelist stats'
            ], 500);
        }
    }
}

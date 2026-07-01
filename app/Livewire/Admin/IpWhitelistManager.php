<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\IpWhitelist;
use App\Livewire\Concerns\AuthorizesAdmin;

class IpWhitelistManager extends Component
{
    use AuthorizesAdmin;

    // Component state
    public $ipEntries = [];
    public $loading = true;
    public $processing = false;

    // Form data
    public $showCreateForm = false;
    public $ip_address = '';
    public $subnet_mask = '';
    public $description = '';

    // Editing
    public $editingEntryId = null;
    public $editIpAddress = '';
    public $editSubnetMask = '';
    public $editDescription = '';
    public $editIsActive = true;

    // Testing
    public $showTestModal = false;
    public $testIp = '';
    public $testResult = null;

    // Messages
    public $message = '';
    public $messageType = 'success';

    public function mount()
    {
        $this->authorizeAdmin();
        $this->loadIpEntries();
    }

    public function loadIpEntries()
    {
        try {
            $this->loading = true;

            $entries = IpWhitelist::orderBy('created_at', 'desc')->get();

            $this->ipEntries = $entries->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'ip_address' => $entry->ip_address,
                    'subnet_mask' => $entry->subnet_mask === 'full' ? '' : $entry->subnet_mask,
                    'description' => $entry->description,
                    'is_active' => $entry->is_active,
                    'created_at' => $entry->created_at,
                    'rule_display' => $entry->ip_address . ($entry->subnet_mask && $entry->subnet_mask !== 'full' ? '/' . $entry->subnet_mask : ''),
                ];
            })->toArray();

        } catch (\Exception $e) {
            $this->showMessage('Failed to load IP whitelist: ' . $e->getMessage(), 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function refreshComponent()
    {
        $this->loadIpEntries();
        $this->dispatch('ip-whitelist-updated');
    }

    public function createIpEntry()
    {
        $this->authorizeAdmin();

        if ($this->processing) {
            return;
        }

        $this->processing = true;

        $this->validate([
            'ip_address' => 'required|ip',
            'subnet_mask' => 'nullable|integer|min:1|max:32',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            IpWhitelist::create([
                'ip_address' => $this->ip_address,
                'subnet_mask' => empty($this->subnet_mask) ? 'full' : $this->subnet_mask,
                'description' => $this->description ?: "IP whitelist entry: {$this->ip_address}",
                'is_active' => true,
                'created_by' => session('user_id'),
            ]);

            AuditLog::create([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'add_ip_whitelist',
                'description' => "Added IP to whitelist: {$this->ip_address}",
                'details' => [
                    'ip_address' => $this->ip_address,
                    'subnet_mask' => $this->subnet_mask,
                    'description' => $this->description
                ],
                'success' => true,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $this->resetCreateForm();
            $this->showCreateForm = false;
            $this->refreshComponent();
            $this->showMessage('IP address added to whitelist successfully', 'success');

        } catch (\Exception $e) {
            $this->showMessage('Failed to add IP to whitelist: ' . $e->getMessage(), 'error');
        } finally {
            $this->processing = false;
        }
    }

    public function addCurrentIp()
    {
        $this->authorizeAdmin();

        if ($this->processing) {
            return;
        }

        $this->processing = true;

        $currentIp = request()->ip();

        try {
            $existing = IpWhitelist::where('ip_address', $currentIp)->first();

            if ($existing) {
                $this->showMessage('Current IP address already exists in whitelist', 'error');
                return;
            }

            IpWhitelist::create([
                'ip_address' => $currentIp,
                'subnet_mask' => 'full',
                'description' => "Auto-added current IP: {$currentIp}",
                'is_active' => true,
                'created_by' => session('user_id'),
            ]);

            AuditLog::create([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'add_current_ip',
                'description' => "Added current IP to whitelist: {$currentIp}",
                'details' => [
                    'ip_address' => $currentIp,
                    'auto_added' => true
                ],
                'success' => true,
                'ip_address' => $currentIp,
                'user_agent' => request()->userAgent(),
            ]);

            $this->refreshComponent();
            $this->showMessage("Successfully added current IP ({$currentIp}) to whitelist", 'success');

        } catch (\Exception $e) {
            $this->showMessage('Failed to add current IP to whitelist: ' . $e->getMessage(), 'error');
        } finally {
            $this->processing = false;
        }
    }

    public function startEdit($entryId)
    {
        $entry = collect($this->ipEntries)->firstWhere('id', $entryId);

        if ($entry) {
            $this->editingEntryId = $entryId;
            $this->editIpAddress = $entry['ip_address'];
            $this->editSubnetMask = $entry['subnet_mask'];
            $this->editDescription = $entry['description'];
            $this->editIsActive = $entry['is_active'];
        }
    }

    public function updateIpEntry()
    {
        $this->authorizeAdmin();

        $this->validate([
            'editIpAddress' => 'required|ip',
            'editSubnetMask' => 'nullable|integer|min:1|max:32',
            'editDescription' => 'nullable|string|max:255',
            'editIsActive' => 'required|boolean',
        ]);

        try {
            $ipEntry = IpWhitelist::find($this->editingEntryId);

            if (!$ipEntry) {
                $this->showMessage('IP entry not found', 'error');
                return;
            }

            $ipEntry->update([
                'ip_address' => $this->editIpAddress,
                'subnet_mask' => $this->editSubnetMask ?: 'full',
                'description' => $this->editDescription,
                'is_active' => $this->editIsActive,
            ]);

            AuditLog::create([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'update_ip_whitelist',
                'description' => "Updated IP whitelist entry: {$this->editIpAddress}",
                'details' => [
                    'ip_address' => $this->editIpAddress,
                    'subnet_mask' => $this->editSubnetMask,
                    'description' => $this->editDescription,
                    'is_active' => $this->editIsActive
                ],
                'success' => true,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $this->cancelEdit();
            $this->refreshComponent();
            $this->showMessage('IP whitelist entry updated successfully', 'success');

        } catch (\Exception $e) {
            $this->showMessage('Failed to update IP whitelist entry: ' . $e->getMessage(), 'error');
        }
    }

    public function toggleEntryStatus($entryId)
    {
        $this->authorizeAdmin();

        try {
            $ipEntry = IpWhitelist::find($entryId);

            if (!$ipEntry) {
                $this->showMessage('IP entry not found', 'error');
                return;
            }

            $newStatus = !$ipEntry->is_active;
            $ipEntry->update(['is_active' => $newStatus]);

            $this->refreshComponent();
            $this->showMessage("IP whitelist entry " . ($newStatus ? 'enabled' : 'disabled') . " successfully", 'success');

        } catch (\Exception $e) {
            $this->showMessage('Failed to toggle active status: ' . $e->getMessage(), 'error');
        }
    }

    public function deleteIpEntry($entryId)
    {
        $this->authorizeAdmin();

        try {
            $ipEntry = IpWhitelist::find($entryId);

            if (!$ipEntry) {
                $this->showMessage('IP entry not found', 'error');
                return;
            }

            AuditLog::create([
                'user_id' => session('user_id'),
                'event_type' => 'ip_whitelist',
                'action' => 'delete_ip_whitelist',
                'description' => "Removed IP from whitelist: {$ipEntry->ip_address}",
                'details' => [
                    'ip_address' => $ipEntry->ip_address,
                    'subnet_mask' => $ipEntry->subnet_mask,
                    'description' => $ipEntry->description,
                    'is_active' => $ipEntry->is_active
                ],
                'success' => true,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $ipEntry->delete();

            $this->refreshComponent();
            $this->showMessage('IP whitelist entry deleted successfully', 'success');

        } catch (\Exception $e) {
            $this->showMessage('Failed to delete IP whitelist entry: ' . $e->getMessage(), 'error');
        }
    }

    public function testIpAccess()
    {
        $this->validate([
            'testIp' => 'required|ip',
        ]);

        try {
            $whitelistEntries = IpWhitelist::where('is_active', true)->get();

            $isAllowed = false;
            $matchedRule = null;

            foreach ($whitelistEntries as $entry) {
                if ($this->matchesIpRule($this->testIp, $entry->ip_address, $entry->subnet_mask)) {
                    $isAllowed = true;
                    $matchedRule = [
                        'ip_address' => $entry->ip_address,
                        'subnet_mask' => $entry->subnet_mask,
                        'description' => $entry->description,
                        'rule' => $entry->ip_address . ($entry->subnet_mask ? '/' . $entry->subnet_mask : '')
                    ];
                    break;
                }
            }

            $this->testResult = [
                'test_ip' => $this->testIp,
                'current_ip' => request()->ip(),
                'is_allowed' => $isAllowed,
                'matched_rule' => $matchedRule,
                'total_rules' => count($whitelistEntries)
            ];

        } catch (\Exception $e) {
            $this->showMessage('Failed to test IP access: ' . $e->getMessage(), 'error');
        }
    }

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

    public function resetCreateForm()
    {
        $this->ip_address = '';
        $this->subnet_mask = '';
        $this->description = '';
    }

    public function cancelCreate()
    {
        $this->showCreateForm = false;
        $this->resetCreateForm();
    }

    public function cancelEdit()
    {
        $this->editingEntryId = null;
        $this->editIpAddress = '';
        $this->editSubnetMask = '';
        $this->editDescription = '';
        $this->editIsActive = true;
    }

    public function cancelTest()
    {
        $this->showTestModal = false;
        $this->testIp = '';
        $this->testResult = null;
    }

    public function showMessage($message, $type = 'success')
    {
        $this->message = $message;
        $this->messageType = $type;

        $this->dispatch('hide-message');
    }

    public function hideMessage()
    {
        $this->message = '';
    }

    public function render()
    {
        return view('admin.livewire.ip-whitelist-manager');
    }
}

<div class="min-h-screen bg-[var(--color-canvas)]">
    <!-- Header -->
    <div class="border-b border-[var(--color-line)] bg-[var(--color-surface)]">
        <div class="os-container py-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="os-eyebrow">Access control</p>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight text-[var(--color-ink)]">IP whitelist management</h1>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Control access to your CAS system by IP address.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="addCurrentIp"
                            class="os-btn os-btn-secondary"
                            wire:loading.attr="disabled"
                            wire:target="addCurrentIp">
                        <i class="fa-solid fa-plus" wire:loading.remove wire:target="addCurrentIp"></i>
                        <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="addCurrentIp"></i>
                        <span wire:loading.remove wire:target="addCurrentIp">Add current IP</span>
                        <span wire:loading wire:target="addCurrentIp">Adding...</span>
                    </button>
                    <button wire:click="$set('showTestModal', true)"
                            class="os-btn os-btn-secondary">
                        <i class="fa-solid fa-circle-check"></i>
                        Test IP
                    </button>
                    <button wire:click="$set('showCreateForm', true)"
                            class="os-btn os-btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        Add IP rule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="os-container py-6">
        <div class="os-alert">
            <span class="os-icon-tile os-icon-tile-ink h-9 w-9 text-sm">
                <i class="fa-solid fa-circle-info"></i>
            </span>
            <div>
                <p class="text-sm font-medium text-[var(--color-ink-2)]">Your current IP address</p>
                <p class="font-mono text-sm text-[var(--color-muted)]">{{ request()->ip() }}</p>
            </div>
        </div>
    </div>

    @if($loading)
        <div class="flex h-64 items-center justify-center gap-3">
            <i class="fa-solid fa-circle-notch fa-spin text-2xl text-[var(--color-accent)]"></i>
            <span class="text-[var(--color-muted)]">Loading IP whitelist...</span>
        </div>
    @endif

    @if(!$loading)
        <div class="os-container pb-10">
            @if(empty($ipEntries))
                <div class="os-card os-card-pad text-center">
                    <div class="mx-auto">
                        <span class="os-icon-tile os-icon-tile-ink mx-auto h-12 w-12 text-lg">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-sm font-medium text-[var(--color-ink)]">No IP whitelist entries</h3>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Start by adding your current IP address or creating specific rules.</p>
                    <div class="mt-6 flex justify-center gap-3">
                        <button wire:click="addCurrentIp" class="os-btn os-btn-secondary">
                            Add current IP
                        </button>
                        <button wire:click="$set('showCreateForm', true)" class="os-btn os-btn-primary">
                            Add IP rule
                        </button>
                    </div>
                </div>
            @else
                <div class="os-card overflow-hidden">
                    <ul class="divide-y divide-[var(--color-line)]">
                        @foreach($ipEntries as $entry)
                            <li wire:key="ip-entry-{{ $entry['id'] }}" class="px-6 py-4">
                                @if($editingEntryId === $entry['id'])
                                    <!-- Edit Form -->
                                    <form wire:submit.prevent="updateIpEntry">
                                        <div class="mb-4 grid gap-4 md:grid-cols-4">
                                            <div>
                                                <label class="os-label">IP address</label>
                                                <input type="text" wire:model="editIpAddress" required
                                                       class="os-input">
                                                @error('editIpAddress') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="os-label">Subnet mask</label>
                                                <input type="number" wire:model="editSubnetMask" min="1" max="32"
                                                       class="os-input"
                                                       placeholder="24">
                                                @error('editSubnetMask') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="os-label">Status</label>
                                                <select wire:model="editStatus" required
                                                        class="os-input">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                            <div class="flex items-end">
                                                <div class="flex gap-2">
                                                    <button type="submit" class="os-btn os-btn-primary">
                                                        Save
                                                    </button>
                                                    <button type="button" wire:click="cancelEdit" class="os-btn os-btn-ghost">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="os-label">Description</label>
                                            <input type="text" wire:model="editDescription"
                                                   class="os-input">
                                            @error('editDescription') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                        </div>
                                    </form>
                                @else
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="font-mono text-lg font-semibold text-[var(--color-ink)]">{{ $entry['rule_display'] }}</span>
                                                @if($entry['is_active'])
                                                    <span class="os-badge os-alert-success">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="os-badge">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm text-[var(--color-muted)]">{{ $entry['description'] }}</p>
                                            <p class="text-xs text-[var(--color-faint)]">Added {{ \Carbon\Carbon::parse($entry['created_at'])->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button wire:click="startEdit({{ $entry['id'] }})"
                                                    wire:key="edit-btn-{{ $entry['id'] }}"
                                                    class="os-btn os-btn-ghost text-sm">
                                                <i class="fa-solid fa-pen"></i>
                                                Edit
                                            </button>
                                            <button wire:click="toggleEntryStatus({{ $entry['id'] }})"
                                                    wire:key="toggle-btn-{{ $entry['id'] }}"
                                                    class="os-btn os-btn-ghost text-sm"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleEntryStatus({{ $entry['id'] }})">
                                                <i class="fa-solid fa-bolt" wire:loading.remove wire:target="toggleEntryStatus({{ $entry['id'] }})"></i>
                                                <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="toggleEntryStatus({{ $entry['id'] }})"></i>
                                                <span wire:loading.remove wire:target="toggleEntryStatus({{ $entry['id'] }})">{{ $entry['is_active'] ? 'Disable' : 'Enable' }}</span>
                                                <span wire:loading wire:target="toggleEntryStatus({{ $entry['id'] }})">Processing...</span>
                                            </button>
                                            <button wire:click="deleteIpEntry({{ $entry['id'] }})"
                                                    wire:key="delete-btn-{{ $entry['id'] }}"
                                                    wire:confirm="Are you sure you want to delete this IP whitelist entry?"
                                                    class="os-btn os-btn-ghost text-sm text-[var(--color-danger)]"
                                                    wire:loading.attr="disabled"
                                                    wire:target="deleteIpEntry({{ $entry['id'] }})">
                                                <i class="fa-solid fa-trash-can" wire:loading.remove wire:target="deleteIpEntry({{ $entry['id'] }})"></i>
                                                <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="deleteIpEntry({{ $entry['id'] }})"></i>
                                                <span wire:loading.remove wire:target="deleteIpEntry({{ $entry['id'] }})">Delete</span>
                                                <span wire:loading wire:target="deleteIpEntry({{ $entry['id'] }})">Deleting...</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    @if($showCreateForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--color-ink)]/40 p-4" wire:click.self="cancelCreate">
            <div class="os-card os-card-pad w-full max-w-md shadow-[var(--shadow-lg)]">
                <h3 class="text-lg font-semibold text-[var(--color-ink)]">Add IP whitelist entry</h3>
                <form wire:submit.prevent="createIpEntry" class="mt-4">
                    <div class="space-y-4">
                        <div>
                            <label class="os-label">IP address</label>
                            <input type="text" wire:model="ip_address" required
                                   class="os-input"
                                   placeholder="192.168.1.100">
                            @error('ip_address') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="os-label">Subnet mask (optional)</label>
                            <input type="number" wire:model="subnet_mask" min="1" max="32"
                                   class="os-input"
                                   placeholder="24">
                            @error('subnet_mask') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                            <p class="mt-1 text-xs text-[var(--color-faint)]">Leave empty for single IP, use 24 for /24 subnet, etc.</p>
                        </div>
                        <div>
                            <label class="os-label">Description</label>
                            <input type="text" wire:model="description"
                                   class="os-input"
                                   placeholder="Office network">
                            @error('description') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="os-btn os-btn-primary flex-1">
                            Add to whitelist
                        </button>
                        <button type="button" wire:click="cancelCreate" class="os-btn os-btn-ghost">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showTestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--color-ink)]/40 p-4" wire:click.self="cancelTest">
            <div class="os-card os-card-pad w-full max-w-lg shadow-[var(--shadow-lg)]">
                <h3 class="text-lg font-semibold text-[var(--color-ink)]">Test IP access</h3>
                <form wire:submit.prevent="testIpAccess" class="mt-4">
                    <div class="mb-4">
                        <label class="os-label">IP address to test</label>
                        <input type="text" wire:model="testIp" required
                               class="os-input"
                               placeholder="192.168.1.100">
                        @error('testIp') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4 flex gap-2">
                        <button type="submit" class="os-btn os-btn-primary flex-1">
                            Test access
                        </button>
                        <button type="button" wire:click="cancelTest" class="os-btn os-btn-ghost">
                            Cancel
                        </button>
                    </div>
                </form>

                @if($testResult)
                    <div class="border-t border-[var(--color-line)] pt-4">
                        <h4 class="font-medium text-[var(--color-ink)]">Test results</h4>
                        <div class="mt-3 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-muted)]">Test IP:</span>
                                <span class="font-mono text-sm text-[var(--color-ink-2)]">{{ $testResult['test_ip'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-muted)]">Your IP:</span>
                                <span class="font-mono text-sm text-[var(--color-ink-2)]">{{ $testResult['current_ip'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-[var(--color-muted)]">Access status:</span>
                                @if($testResult['is_allowed'])
                                    <span class="os-badge os-alert-success">
                                        <i class="fa-solid fa-check"></i> Allowed
                                    </span>
                                @else
                                    <span class="os-badge os-alert-danger">
                                        <i class="fa-solid fa-xmark"></i> Blocked
                                    </span>
                                @endif
                            </div>
                            @if($testResult['matched_rule'])
                                <div class="os-alert os-alert-success flex-col items-start">
                                    <h5 class="text-sm font-medium">Matched rule</h5>
                                    <p class="font-mono text-sm">{{ $testResult['matched_rule']['rule'] }}</p>
                                    <p class="text-xs">{{ $testResult['matched_rule']['description'] }}</p>
                                </div>
                            @endif
                            <div class="text-xs text-[var(--color-faint)]">
                                Tested against {{ $testResult['total_rules'] }} active rule(s)
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($message)
        <div class="fixed bottom-4 right-4 z-50" x-data="{ show: true }" x-show="show" x-transition
             x-init="setTimeout(() => show = false, 5000)">
            <div class="os-alert shadow-[var(--shadow-lg)] {{ $messageType === 'success' ? 'os-alert-success' : 'os-alert-danger' }}">
                @if($messageType === 'success')
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                @else
                    <i class="fa-solid fa-circle-xmark mt-0.5"></i>
                @endif
                <span class="flex-1">{{ $message }}</span>
                <button wire:click="hideMessage" class="ml-2 opacity-70 hover:opacity-100">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('hide-message', () => {
        setTimeout(() => {
            $wire.hideMessage();
        }, 5000);
    });
</script>
@endscript

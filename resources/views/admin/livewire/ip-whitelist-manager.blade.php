<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">IP Whitelist Management</h1>
                    <p class="text-gray-600 mt-1">Control access to your CAS system by IP address</p>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="addCurrentIp"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
                            wire:loading.attr="disabled"
                            wire:target="addCurrentIp">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="addCurrentIp">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading wire:target="addCurrentIp">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span wire:loading.remove wire:target="addCurrentIp">Add Current IP</span>
                        <span wire:loading wire:target="addCurrentIp">Adding...</span>
                    </button>
                    <button wire:click="$set('showTestModal', true)"
                            class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Test IP
                    </button>
                    <button wire:click="$set('showCreateForm', true)"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add IP Rule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">Your current IP address</p>
                    <p class="text-sm text-blue-600 font-mono">{{ request()->ip() }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($loading)
        <div class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading IP whitelist...</span>
        </div>
    @endif

    @if(!$loading)
        <div class="max-w-7xl mx-auto px-4 pb-8">
            @if(empty($ipEntries))
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No IP whitelist entries</h3>
                    <p class="mt-1 text-sm text-gray-500">Start by adding your current IP address or creating specific rules.</p>
                    <div class="mt-6 flex justify-center space-x-3">
                        <button wire:click="addCurrentIp"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Add Current IP
                        </button>
                        <button wire:click="$set('showCreateForm', true)"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Add IP Rule
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @foreach($ipEntries as $entry)
                            <li wire:key="ip-entry-{{ $entry['id'] }}" class="px-6 py-4">
                                @if($editingEntryId === $entry['id'])
                                    <!-- Edit Form -->
                                    <form wire:submit.prevent="updateIpEntry">
                                        <div class="grid md:grid-cols-4 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                                                <input type="text" wire:model="editIpAddress" required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                @error('editIpAddress') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Subnet Mask</label>
                                                <input type="number" wire:model="editSubnetMask" min="1" max="32"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                       placeholder="24">
                                                @error('editSubnetMask') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                <select wire:model="editStatus" required
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                            <div class="flex items-end">
                                                <div class="flex space-x-2">
                                                    <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                                        Save
                                                    </button>
                                                    <button type="button" wire:click="cancelEdit" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded text-sm">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <input type="text" wire:model="editDescription"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @error('editDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </form>
                                @else
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center">
                                                <span class="text-lg font-mono font-semibold text-gray-900">{{ $entry['rule_display'] }}</span>
                                                @if($entry['is_active'])
                                                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">{{ $entry['description'] }}</p>
                                            <p class="text-xs text-gray-500">Added {{ \Carbon\Carbon::parse($entry['created_at'])->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="startEdit({{ $entry['id'] }})"
                                                    wire:key="edit-btn-{{ $entry['id'] }}"
                                                    class="text-blue-600 hover:text-blue-900 text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button wire:click="toggleEntryStatus({{ $entry['id'] }})"
                                                    wire:key="toggle-btn-{{ $entry['id'] }}"
                                                    class="text-gray-600 hover:text-gray-900 text-sm"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleEntryStatus({{ $entry['id'] }})">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="toggleEntryStatus({{ $entry['id'] }})">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                                <svg class="w-4 h-4 inline mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading wire:target="toggleEntryStatus({{ $entry['id'] }})">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                <span wire:loading.remove wire:target="toggleEntryStatus({{ $entry['id'] }})">{{ $entry['is_active'] ? 'Disable' : 'Enable' }}</span>
                                                <span wire:loading wire:target="toggleEntryStatus({{ $entry['id'] }})">Processing...</span>
                                            </button>
                                            <button wire:click="deleteIpEntry({{ $entry['id'] }})"
                                                    wire:key="delete-btn-{{ $entry['id'] }}"
                                                    wire:confirm="Are you sure you want to delete this IP whitelist entry?"
                                                    class="text-red-600 hover:text-red-900 text-sm"
                                                    wire:loading.attr="disabled"
                                                    wire:target="deleteIpEntry({{ $entry['id'] }})">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="deleteIpEntry({{ $entry['id'] }})">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                <svg class="w-4 h-4 inline mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading wire:target="deleteIpEntry({{ $entry['id'] }})">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
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
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="cancelCreate">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Add IP Whitelist Entry</h3>
                <form wire:submit.prevent="createIpEntry">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                            <input type="text" wire:model="ip_address" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="192.168.1.100">
                            @error('ip_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subnet Mask (Optional)</label>
                            <input type="number" wire:model="subnet_mask" min="1" max="32"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="24">
                            @error('subnet_mask') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-1">Leave empty for single IP, use 24 for /24 subnet, etc.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" wire:model="description"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Office network">
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-6">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                            Add to Whitelist
                        </button>
                        <button type="button" wire:click="cancelCreate"
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showTestModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="cancelTest">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold mb-4">Test IP Access</h3>
                <form wire:submit.prevent="testIpAccess">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">IP Address to Test</label>
                        <input type="text" wire:model="testIp" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="192.168.1.100">
                        @error('testIp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex space-x-2 mb-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                            Test Access
                        </button>
                        <button type="button" wire:click="cancelTest"
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>

                @if($testResult)
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-3">Test Results</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Test IP:</span>
                                <span class="text-sm font-mono">{{ $testResult['test_ip'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Your IP:</span>
                                <span class="text-sm font-mono">{{ $testResult['current_ip'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Access Status:</span>
                                @if($testResult['is_allowed'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ Allowed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ✗ Blocked
                                    </span>
                                @endif
                            </div>
                            @if($testResult['matched_rule'])
                                <div class="bg-green-50 border border-green-200 rounded-md p-3">
                                    <h5 class="text-sm font-medium text-green-800">Matched Rule</h5>
                                    <p class="text-sm text-green-700 font-mono">{{ $testResult['matched_rule']['rule'] }}</p>
                                    <p class="text-xs text-green-600">{{ $testResult['matched_rule']['description'] }}</p>
                                </div>
                            @endif
                            <div class="text-xs text-gray-500">
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
            <div class="px-6 py-3 rounded-lg shadow-lg {{ $messageType === 'success' ? 'bg-green-500' : 'bg-red-500' }} text-white">
                <div class="flex items-center">
                    @if($messageType === 'success')
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                    <span>{{ $message }}</span>
                    <button wire:click="hideMessage" class="ml-4">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
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

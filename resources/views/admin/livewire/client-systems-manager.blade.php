<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Client Systems Management</h1>
                <button wire:click="$set('showCreateForm', true)"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Client System
                </button>
            </div>
        </div>
    </div>

    @if($loading)
        <div class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading client systems...</span>
        </div>
    @endif

    @if(!$loading)
        <div class="max-w-7xl mx-auto px-4 py-8">
            @if(empty($clientSystems))
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No client systems</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first client system.</p>
                    <div class="mt-6">
                        <button wire:click="$set('showCreateForm', true)"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Client System
                        </button>
                    </div>
                </div>
            @else
                <div class="grid gap-6">
                    @foreach($clientSystems as $system)
                        <div wire:key="client-system-{{ $system['id'] }}" class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                            @if($editingSystemId === $system['id'])
                                <!-- Edit Form -->
                                <div class="p-6">
                                    <form wire:submit.prevent="updateClientSystem">
                                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                                <input type="text" wire:model="editName" required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                @error('editName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                <select wire:model="editStatus" required
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea wire:model="editDescription" rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                            @error('editDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Callback URL</label>
                                            <input type="url" wire:model="editCallbackUrl" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @error('editCallbackUrl') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="flex space-x-2">
                                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                                Save Changes
                                            </button>
                                            <button type="button" wire:click="cancelEdit" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900">{{ $system['name'] }}</h3>
                                            <p class="text-gray-600">{{ $system['callback_url'] ?? 'No callback URL set' }}</p>
                                            <p class="text-sm text-gray-500">Client ID: {{ $system['client_id'] }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($system['is_active'])
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Inactive</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Callback URL</label>
                                            <p class="text-sm text-gray-900 break-all">{{ $system['callback_url'] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Last Accessed</label>
                                            <p class="text-sm text-gray-900">
                                                {{ $system['last_accessed'] ? \Carbon\Carbon::parse($system['last_accessed'])->diffForHumans() : 'Never' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="border-t pt-4 mb-4">
                                        <h4 class="font-medium text-gray-900 mb-2">Security Status</h4>
                                        <div class="grid md:grid-cols-3 gap-4 text-sm">
                                            <div class="flex items-center">
                                                <span class="text-gray-600">Credentials:</span>
                                                @if($system['security_status']['credentials_viewed'])
                                                    <span class="ml-2 text-green-600 font-medium">
                                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Secured
                                                    </span>
                                                @else
                                                    <span class="ml-2 text-orange-600 font-medium">
                                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Not Viewed
                                                    </span>
                                                @endif
                                            </div>
                                            @if($system['security_status']['credentials_viewed_at'])
                                                <div>
                                                    <span class="text-gray-600">Viewed:</span>
                                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($system['security_status']['credentials_viewed_at'])->diffForHumans() }}</span>
                                                </div>
                                            @endif
                                            @if($system['security_status']['credentials_regenerated_at'])
                                                <div>
                                                    <span class="text-gray-600">Regenerated:</span>
                                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($system['security_status']['credentials_regenerated_at'])->diffForHumans() }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button wire:key="edit-btn-{{ $system['id'] }}"
                                                wire:click="startEdit({{ $system['id'] }})"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-2 text-blue-600 hover:bg-blue-50 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button wire:key="toggle-btn-{{ $system['id'] }}"
                                                wire:click="toggleSystemStatus({{ $system['id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:target="toggleSystemStatus"
                                                class="px-3 py-2 text-gray-600 hover:bg-gray-50 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            {{ $system['is_active'] ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button wire:key="regen-btn-{{ $system['id'] }}"
                                                wire:click="startRegenerate({{ $system['id'] }})"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-2 text-orange-600 hover:bg-orange-50 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                            </svg>
                                            Regenerate Credentials
                                        </button>
                                        <button wire:key="delete-btn-{{ $system['id'] }}"
                                                wire:click="deleteClientSystem({{ $system['id'] }})"
                                                wire:confirm="Are you sure you want to delete this client system? This action cannot be undone."
                                                wire:loading.attr="disabled"
                                                class="px-3 py-2 text-red-600 hover:bg-red-50 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if($showCreateForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="cancelCreate">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Create New Client System</h3>
                <form wire:submit.prevent="createClientSystem">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" wire:model="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Customer Portal">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" wire:model="description"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Brief description of this system">
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Callback URL</label>
                            <input type="url" wire:model="callback_url" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="https://portal.example.com/cas/callback">
                            @error('callback_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-6">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                            Create System
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

    @if($showCredentials)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $createdSystemName }} Created Successfully!</h3>
                    <p class="text-red-600 font-medium mt-2">These credentials will only be shown once. Copy them now!</p>
                </div>

                <div class="space-y-4">
                    <!-- Client ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" value="{{ $newCredentials['client_id'] }}" readonly
                                   class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md font-mono text-sm">
                            <button onclick="copyToClipboard('{{ $newCredentials['client_id'] }}', 'Client ID')"
                                    class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Client Secret -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" value="{{ $newCredentials['client_secret'] }}" readonly
                                   class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md font-mono text-sm">
                            <button onclick="copyToClipboard('{{ $newCredentials['client_secret'] }}', 'Client Secret')"
                                    class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Webhook Secret -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" value="{{ $newCredentials['webhook_secret'] }}" readonly
                                   class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md font-mono text-sm">
                            <button onclick="copyToClipboard('{{ $newCredentials['webhook_secret'] }}', 'Webhook Secret')"
                                    class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mt-6">
                    <div class="flex">
                        <svg class="flex-shrink-0 h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800">Important Security Notice</h4>
                            <ul class="text-sm text-yellow-700 mt-1 list-disc list-inside space-y-1">
                                <li>Store these credentials securely in your customer portal configuration</li>
                                <li>Never share these credentials or commit them to version control</li>
                                <li>These credentials will not be shown again after closing this dialog</li>
                                <li>Use the "Regenerate Credentials" feature if you lose them</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center mt-6">
                    <button wire:click="closeCredentials"
                            class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                        I have copied the credentials
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Regenerate Credentials Modal -->
    @if($showRegenerateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="cancelRegenerate">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4 text-red-600">Regenerate Credentials</h3>
                <p class="text-gray-600 mb-4">
                    This will generate new credentials for <strong>{{ $regenerateSystemName }}</strong> and invalidate all existing SSO tokens.
                </p>
                <form wire:submit.prevent="regenerateCredentials">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason for regeneration</label>
                        <textarea wire:model="regenerateReason" required rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Please explain why you need to regenerate credentials..."></textarea>
                        @error('regenerateReason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit"
                                class="flex-1 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition-colors">
                            Regenerate Credentials
                        </button>
                        <button type="button" wire:click="cancelRegenerate"
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Messages -->
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

<script>
async function copyToClipboard(text, label) {
    try {
        await navigator.clipboard.writeText(text);
        Livewire.dispatch('show-message', { message: `${label} copied to clipboard!`, type: 'success' });
    } catch (error) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        Livewire.dispatch('show-message', { message: `${label} copied to clipboard!`, type: 'success' });
    }
}
</script>

@script
<script>
    $wire.on('show-message', (event) => {
        $wire.showMessage(event.message, event.type);
    });

    $wire.on('hide-message', () => {
        setTimeout(() => {
            $wire.hideMessage();
        }, 5000);
    });
</script>
@endscript

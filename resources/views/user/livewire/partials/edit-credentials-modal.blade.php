@if($showLinkModal)
<div class="fixed inset-0 z-50 overflow-y-auto" wire:key="edit-modal-{{ $selectedSystemId }}">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeLinkModal"></div>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <x-icon name="key" class="w-5 h-5 mr-3" />
                        {{ $selectedSystemName ? 'Update Credentials' : 'Link System' }}
                    </h3>
                    <button wire:click="closeLinkModal" class="text-white hover:text-gray-200 transition-colors duration-200">
                        <x-icon name="times" class="w-6 h-6" />
                    </button>
                </div>
                <p class="text-blue-100 text-sm mt-1">
                    {{ $selectedSystemName }}
                </p>
            </div>

            <form wire:submit.prevent="saveCredentials" class="bg-white">
                <div class="px-6 py-6 space-y-6">
                    <div>
                        <label for="modalUsername" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icon name="user" class="w-4 h-4 mr-2 text-blue-600" />Username
                        </label>
                        <input type="text"
                               id="modalUsername"
                               wire:model.defer="modalUsername"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="Enter your username for this system"
                               required>
                        @error('modalUsername')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <x-icon name="exclamation" class="w-4 h-4 mr-1" />{{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="modalPassword" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icon name="lock" class="w-4 h-4 mr-2 text-blue-600" />Password
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="modalPassword"
                                   wire:model.defer="modalPassword"
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   placeholder="Enter your password for this system"
                                   required>
                            <button type="button"
                                    onclick="togglePasswordVisibility('modalPassword')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <x-icon name="eye" class="w-4 h-4" id="modalPassword-eye" />
                            </button>
                        </div>
                        @error('modalPassword')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <x-icon name="exclamation" class="w-4 h-4 mr-1" />{{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <x-icon name="shield" class="w-5 h-5 text-blue-600 mt-0.5 mr-3" />
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 mb-1">Security Notice</h4>
                                <p class="text-sm text-blue-700">
                                    Your credentials are encrypted and stored securely. We validate them against the actual system before storage.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($message)
                        <div class="flex items-center p-4 rounded-xl {{ $messageType === 'error' ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                            <x-icon name="{{ $messageType === 'error' ? 'exclamation' : 'shield' }}" class="w-5 h-5 {{ $messageType === 'error' ? 'text-red-600' : 'text-green-600' }} mr-3" />
                            <span class="text-sm {{ $messageType === 'error' ? 'text-red-700' : 'text-green-700' }}">{{ $message }}</span>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button"
                            wire:click="closeLinkModal"
                            class="w-full sm:w-auto px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                        <x-icon name="times" class="w-4 h-4 mr-2" />Cancel
                    </button>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveCredentials"
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center disabled:opacity-50">

                        <div wire:loading.remove wire:target="saveCredentials" class="flex items-center">
                            <x-icon name="save" class="w-4 h-4 mr-2" />
                            {{ $selectedSystemName && collect($this->clientSystems)->firstWhere('id', $selectedSystemId)['is_linked'] ? 'Update Credentials' : 'Link System' }}
                        </div>

                        <div wire:loading wire:target="saveCredentials" class="flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            <span>Processing...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById(inputId + '-eye');

    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21 M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
    } else {
        input.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
}
</script>
@endif

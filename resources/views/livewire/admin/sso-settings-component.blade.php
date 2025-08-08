<div class="relative">
    <div wire:loading.delay wire:target="saveSettings,resetToDefaults" class="absolute inset-0 bg-white bg-opacity-75 z-40 flex items-center justify-center">
        <x-loading-overlay>Saving settings...</x-loading-overlay>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 10.586l2.293-2.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">SSO Token Configuration</h2>
        <p class="text-gray-600">Configure SSO token settings including expiration, security, and validation parameters.</p>
    </div>

    <form wire:submit.prevent="saveSettings" class="space-y-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-4 2 1-4a6 6 0 1111-4z" clip-rule="evenodd"></path>
                    </svg>
                    Token Configuration
                </h3>
                <p class="text-sm text-gray-500 mt-1">Basic token generation and lifecycle settings</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Token Expiry (minutes)</label>
                        <input type="number" wire:model="token_expiry_minutes" min="5" max="1440"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">How long tokens remain valid (5-1440 minutes)</p>
                        @error('token_expiry_minutes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Concurrent Tokens</label>
                        <input type="number" wire:model="max_concurrent_tokens" min="1" max="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Maximum active tokens per user (1-20)</p>
                        @error('max_concurrent_tokens') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Token Issuer</label>
                        <input type="text" wire:model="token_issuer" maxlength="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">JWT issuer claim (iss)</p>
                        @error('token_issuer') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Token Audience</label>
                        <input type="text" wire:model="token_audience" maxlength="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">JWT audience claim (aud)</p>
                        @error('token_audience') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                    </svg>
                    Token Refresh Settings
                </h3>
                <p class="text-sm text-gray-500 mt-1">Configure automatic token refresh and renewal</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="flex items-center">
                    <input type="checkbox" wire:model="enable_token_refresh"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">
                        Enable automatic token refresh
                    </label>
                </div>

                @if($enable_token_refresh)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Refresh Threshold (minutes)</label>
                        <input type="number" wire:model="token_refresh_threshold" min="1" max="60"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Refresh token when this many minutes remain before expiry</p>
                        @error('token_refresh_threshold') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Security Settings
                </h3>
                <p class="text-sm text-gray-500 mt-1">Cryptographic and validation security options</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Signature Algorithm</label>
                        <select wire:model="signature_algorithm"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="HS256">HMAC SHA-256 (HS256)</option>
                            <option value="HS384">HMAC SHA-384 (HS384)</option>
                            <option value="HS512">HMAC SHA-512 (HS512)</option>
                            <option value="RS256">RSA SHA-256 (RS256)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Cryptographic algorithm for token signing</p>
                        @error('signature_algorithm') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Failed Attempts</label>
                        <input type="number" wire:model="max_failed_attempts" min="1" max="10"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Failed authentication attempts before lockout</p>
                        @error('max_failed_attempts') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lockout Duration (minutes)</label>
                        <input type="number" wire:model="lockout_duration" min="5" max="1440"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">How long to lock accounts after failed attempts</p>
                        @error('lockout_duration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="require_ip_validation"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">
                            Require IP address validation for tokens
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" wire:model="enable_audit_logging"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">
                            Enable comprehensive audit logging
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-900 mb-2">Current Configuration Summary</h4>
            <div class="text-xs text-blue-700 space-y-1">
                <p><span class="font-medium">Token Expiry:</span> {{ $token_expiry_minutes }} minutes ({{ number_format($token_expiry_minutes / 60, 1) }} hours)</p>
                <p><span class="font-medium">Refresh:</span> {{ $enable_token_refresh ? 'Enabled' : 'Disabled' }}{{ $enable_token_refresh ? " (threshold: {$token_refresh_threshold} min)" : '' }}</p>
                <p><span class="font-medium">Security:</span> {{ $signature_algorithm }} signing, IP validation {{ $require_ip_validation ? 'required' : 'optional' }}</p>
                <p><span class="font-medium">Rate Limiting:</span> {{ $max_failed_attempts }} attempts, {{ $lockout_duration }} min lockout</p>
            </div>
        </div>

        <div class="flex justify-between pt-6">
            <button type="button" wire:click="resetToDefaults"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                Reset to Defaults
            </button>

            <div class="space-x-3">
                <button type="button" onclick="window.location.reload()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Save Settings
                </button>
            </div>
        </div>
    </form>
</div>

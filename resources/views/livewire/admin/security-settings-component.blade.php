<div class="relative">

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
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Security Settings</h2>
        <p class="text-gray-600">Configure password recovery, two-factor authentication, and email settings.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-4 2 1-4a6 6 0 1111-4z" clip-rule="evenodd"></path>
                        </svg>
                        Password Recovery
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Configure forgot password functionality and email delivery</p>
                </div>

                <form wire:submit.prevent="saveSettings" class="p-6 space-y-6">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="enable_forgot_password"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">
                            Enable forgot password functionality
                        </label>
                    </div>

                    @if($enable_forgot_password)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reset Link Expiry (minutes)</label>
                                <input type="number" wire:model="password_reset_expiry" min="15" max="1440"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">How long reset links remain valid (15-1440 minutes)</p>
                                @error('password_reset_expiry') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Reset Attempts</label>
                                <input type="number" wire:model="max_reset_attempts" min="1" max="10"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Failed attempts before account lockout</p>
                                @error('max_reset_attempts') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lockout Duration (minutes)</label>
                                <input type="number" wire:model="lockout_duration" min="5" max="1440"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">How long accounts remain locked after failed attempts</p>
                                @error('lockout_duration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="require_email_verification"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">
                                Require email verification for password resets
                            </label>
                        </div>
                    @endif
                </form>
            </div>

            @if($enable_forgot_password)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                    Email Configuration
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">SMTP settings for sending password reset emails</p>
                            </div>
                            <button wire:click="testEmailConfiguration" type="button"
                                    wire:loading.attr="disabled" wire:target="testEmailConfiguration"
                                    class="px-3 py-1 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="testEmailConfiguration">Test Configuration</span>
                                <span wire:loading wire:target="testEmailConfiguration" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Testing...
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                                <input type="text" wire:model="smtp_host" placeholder="smtp.gmail.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('smtp_host') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                                <input type="number" wire:model="smtp_port" placeholder="587"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('smtp_port') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                                <input type="text" wire:model="smtp_username" placeholder="your-email@gmail.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('smtp_username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                                <input type="password" wire:model="smtp_password" placeholder="App password or SMTP password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('smtp_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                                <select wire:model="smtp_encryption"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                                <input type="email" wire:model="from_email" placeholder="noreply@yourcompany.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('from_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                                <input type="text" wire:model="from_name" placeholder="CAS System"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('from_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <button wire:click="saveSettings" type="button"
                        wire:loading.attr="disabled" wire:target="saveSettings"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="saveSettings">Save Security Settings</span>
                    <span wire:loading wire:target="saveSettings" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Two-Factor Authentication
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Secure your account with Google Authenticator</p>
                </div>

                <div class="p-6">
                    @if(!$is_2fa_enabled)
                        @if(!$google2fa_secret)
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <p class="text-sm text-gray-600 mb-4">2FA is not enabled on your account</p>
                                <button wire:click="generate2FA" type="button"
                                        wire:loading.attr="disabled" wire:target="generate2FA"
                                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="generate2FA">Setup 2FA</span>
                                    <span wire:loading wire:target="generate2FA" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Setting up...
                                    </span>
                                </button>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900 mb-2">Scan QR Code with Google Authenticator</p>
                                    <div class="inline-block p-4 bg-white border rounded-lg">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qr_code_url) }}"
                                             alt="QR Code" class="w-32 h-32"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div style="display:none;" class="w-32 h-32 flex items-center justify-center bg-gray-100 text-xs text-gray-500">
                                            QR Code unavailable<br>Use manual entry below
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Or manually enter this code: <br><code class="text-xs bg-gray-100 px-1 py-0.5 rounded font-mono">{{ $google2fa_secret }}</code></p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                                    <input type="text" wire:model="verification_code" placeholder="Enter 6-digit code" maxlength="6"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    @error('verification_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex space-x-2">
                                    <button wire:click="enable2FA" type="button"
                                            wire:loading.attr="disabled" wire:target="enable2FA"
                                            class="flex-1 px-3 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="enable2FA">Enable 2FA</span>
                                        <span wire:loading wire:target="enable2FA" class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Enabling...
                                        </span>
                                    </button>
                                    <button wire:click="$set('google2fa_secret', '')" type="button"
                                            wire:loading.attr="disabled" wire:target="enable2FA"
                                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="space-y-4">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-green-500 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm font-medium text-green-800">2FA is enabled</p>
                                <p class="text-xs text-gray-600">Your account is secured with 2FA</p>
                            </div>

                            @if($backup_codes)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <h4 class="text-xs font-medium text-yellow-800 mb-2">Backup Codes</h4>
                                    <div class="grid grid-cols-2 gap-1 text-xs font-mono">
                                        @foreach($backup_codes as $code)
                                            <div class="bg-white px-2 py-1 rounded text-center">{{ $code }}</div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-yellow-700 mt-2">Save these codes in a secure place</p>
                                </div>
                            @endif

                            <div class="flex space-x-2">
                                <button wire:click="regenerateBackupCodes" type="button"
                                        wire:loading.attr="disabled" wire:target="regenerateBackupCodes"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="regenerateBackupCodes">New Codes</span>
                                    <span wire:loading wire:target="regenerateBackupCodes" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-blue-700" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Generating...
                                    </span>
                                </button>
                                <button wire:click="disable2FA" type="button"
                                        wire:loading.attr="disabled" wire:target="disable2FA"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="disable2FA">Disable 2FA</span>
                                    <span wire:loading wire:target="disable2FA" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-red-700" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Disabling...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

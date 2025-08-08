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
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Admin Profile Settings</h2>
        <p class="text-gray-600">Manage your admin account information and security settings.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        Profile Information
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Update your admin account details</p>
                </div>

                <form wire:submit.prevent="updateProfile" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" wire:model="username"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" wire:model="email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" wire:model="first_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" wire:model="last_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                wire:loading.attr="disabled" wire:target="updateProfile"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="updateProfile">Update Profile</span>
                            <span wire:loading wire:target="updateProfile" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow rounded-lg mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                Change Password
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Update your account password for security</p>
                        </div>
                        <button wire:click="togglePasswordSection"
                                class="px-3 py-1 text-sm font-medium {{ $showPasswordSection ? 'text-red-600 hover:text-red-700' : 'text-blue-600 hover:text-blue-700' }} transition-colors">
                            {{ $showPasswordSection ? 'Cancel' : 'Change Password' }}
                        </button>
                    </div>
                </div>

                @if($showPasswordSection)
                    <form wire:submit.prevent="changePassword" class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" wire:model="current_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" wire:model="new_password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" wire:model="new_password_confirmation"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-yellow-800">Password Requirements</h4>
                                    <div class="text-xs text-yellow-700 mt-1">
                                        <ul class="list-disc ml-5 space-y-1">
                                            <li>At least 8 characters long</li>
                                            <li>Contains uppercase and lowercase letters</li>
                                            <li>Contains at least one number</li>
                                            <li>Contains at least one special character</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="togglePasswordSection"
                                    wire:loading.attr="disabled" wire:target="changePassword"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                            <button type="submit"
                                    wire:loading.attr="disabled" wire:target="changePassword"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="changePassword">Change Password</span>
                                <span wire:loading wire:target="changePassword" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Changing...
                                </span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="text-sm">Click "Change Password" to update your account password</p>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 1l3 3h4v12H3V4h4l3-3zm0 2.414L8.586 5H5v10h10V5h-3.586L10 3.414zM8 7a2 2 0 114 0v1H8V7zm0 3h4v3H8v-3z" clip-rule="evenodd"></path>
                                </svg>
                                Two-Factor Authentication
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($two_factor_enabled)
                                    2FA is currently <span class="text-green-600 font-medium">enabled</span> for enhanced security
                                @else
                                    Add an extra layer of security to your account
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($two_factor_enabled)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 10.586l2.293-2.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Disabled
                                </span>
                            @endif
                            <button wire:click="toggle2FASection"
                                    class="px-3 py-1 text-sm font-medium {{ $show2FASection ? 'text-red-600 hover:text-red-700' : 'text-blue-600 hover:text-blue-700' }} transition-colors">
                                {{ $show2FASection ? 'Cancel' : 'Manage 2FA' }}
                            </button>
                        </div>
                    </div>
                </div>

                @if($show2FASection)
                    <div class="p-6">
                        @if(!$two_factor_enabled)
                            <div class="space-y-6">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800">About Two-Factor Authentication</h4>
                                            <div class="text-xs text-blue-700 mt-1">
                                                <p>Two-factor authentication adds an extra layer of security by requiring a second form of verification when signing in.</p>
                                                <ul class="list-disc ml-5 mt-2 space-y-1">
                                                    <li>Works with Google Authenticator, Authy, or other TOTP apps</li>
                                                    <li>You'll receive 8 backup codes for emergency access</li>
                                                    <li>Required for each sign-in attempt</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button wire:click="enable2FA"
                                            wire:loading.attr="disabled" wire:target="enable2FA"
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="enable2FA">Enable 2FA</span>
                                        <span wire:loading wire:target="enable2FA" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Enabling...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-6">
                                @if($qr_code_url || $two_factor_secret)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- QR Code -->
                                        @if($qr_code_url)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 mb-3">QR Code</h4>
                                                <div class="text-center bg-white border border-gray-200 rounded-lg p-4">
                                                    <img src="{{ $qr_code_url }}" alt="2FA QR Code" class="mx-auto mb-2">
                                                    <p class="text-xs text-gray-500">Scan with your authenticator app</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($two_factor_secret)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 mb-3">Manual Setup</h4>
                                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                    <p class="text-xs text-gray-600 mb-2">Secret Key:</p>
                                                    <code class="text-xs bg-white border rounded px-2 py-1 block break-all">{{ $two_factor_secret }}</code>
                                                    <p class="text-xs text-gray-500 mt-2">Enter this key manually if you can't scan the QR code</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if(!empty($backup_codes))
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 mb-3">Backup Codes</h4>
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                            <div class="flex">
                                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="ml-3 text-sm text-yellow-800">Save these backup codes in a secure location. Each code can only be used once.</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            @foreach($backup_codes as $code)
                                                <code class="text-xs bg-white border rounded px-2 py-1 text-center">{{ $code }}</code>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex justify-between pt-4 border-t">
                                    @if($two_factor_enabled)
                                        <button wire:click="regenerateBackupCodes"
                                                wire:loading.attr="disabled" wire:target="regenerateBackupCodes"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span wire:loading.remove wire:target="regenerateBackupCodes">Regenerate Backup Codes</span>
                                            <span wire:loading wire:target="regenerateBackupCodes">Regenerating...</span>
                                        </button>

                                        <button wire:click="disable2FA"
                                                wire:loading.attr="disabled" wire:target="disable2FA"
                                                onclick="return confirm('Are you sure you want to disable 2FA? This will reduce your account security.')"
                                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span wire:loading.remove wire:target="disable2FA">Disable 2FA</span>
                                            <span wire:loading wire:target="disable2FA" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Disabling...
                                            </span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="text-sm">
                            @if($two_factor_enabled)
                                Two-factor authentication is active and protecting your account.
                            @else
                                Click "Manage 2FA" to set up two-factor authentication.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h4 class="text-sm font-medium text-blue-900 mb-4">Account Information</h4>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-medium text-blue-800">Role:</span>
                        <span class="text-blue-700 ml-2">Administrator</span>
                    </div>
                    <div>
                        <span class="font-medium text-blue-800">Account Status:</span>
                        <span class="text-green-700 ml-2">Active</span>
                    </div>
                    <div>
                        <span class="font-medium text-blue-800">Last Login:</span>
                        <span class="text-blue-700 ml-2">{{ now()->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Security Tips</h4>
                <div class="space-y-2 text-xs text-gray-600">
                    <p>• Use a strong, unique password</p>
                    <p>• Change your password regularly</p>
                    <p>• Keep your profile information up to date</p>
                    <p>• Log out when finished working</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="relative">

    @if (session()->has('message'))
        <div class="os-alert os-alert-success mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-sm font-medium">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="os-alert os-alert-danger mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 10.586l2.293-2.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="mb-8">
        <div class="os-eyebrow mb-2">Account security</div>
        <h2 class="text-2xl font-semibold tracking-tight text-[var(--color-ink)]">Security settings</h2>
        <p class="mt-1.5 text-sm text-[var(--color-muted)]">Configure password recovery, two-factor authentication, and email settings.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="os-card">
                <div class="px-6 py-5 border-b border-[var(--color-line)]">
                    <h3 class="text-base font-semibold text-[var(--color-ink)] flex items-center gap-2.5">
                        <span class="os-icon-tile os-icon-tile-ink !h-9 !w-9 !text-base">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-4 2 1-4a6 6 0 1111-4z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        Password recovery
                    </h3>
                    <p class="text-sm text-[var(--color-muted)] mt-1.5">Configure forgot password functionality and email delivery</p>
                </div>

                <form wire:submit.prevent="saveSettings" class="p-6 space-y-6">
                    <label class="flex items-center gap-2.5 text-sm text-[var(--color-ink-2)]">
                        <input type="checkbox" wire:model="enable_forgot_password"
                               class="h-4 w-4 rounded border-[var(--color-line-strong)] text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                        Enable forgot password functionality
                    </label>

                    @if($enable_forgot_password)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="os-label">Reset link expiry (minutes)</label>
                                <input type="number" wire:model="password_reset_expiry" min="15" max="1440"
                                       class="os-input">
                                <p class="text-xs text-[var(--color-muted)] mt-1.5">How long reset links remain valid (15-1440 minutes)</p>
                                @error('password_reset_expiry') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">Max reset attempts</label>
                                <input type="number" wire:model="max_reset_attempts" min="1" max="10"
                                       class="os-input">
                                <p class="text-xs text-[var(--color-muted)] mt-1.5">Failed attempts before account lockout</p>
                                @error('max_reset_attempts') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">Lockout duration (minutes)</label>
                                <input type="number" wire:model="lockout_duration" min="5" max="1440"
                                       class="os-input">
                                <p class="text-xs text-[var(--color-muted)] mt-1.5">How long accounts remain locked after failed attempts</p>
                                @error('lockout_duration') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <label class="flex items-center gap-2.5 text-sm text-[var(--color-ink-2)]">
                            <input type="checkbox" wire:model="require_email_verification"
                                   class="h-4 w-4 rounded border-[var(--color-line-strong)] text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                            Require email verification for password resets
                        </label>
                    @endif
                </form>
            </div>

            @if($enable_forgot_password)
                <div class="os-card">
                    <div class="px-6 py-5 border-b border-[var(--color-line)]">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base font-semibold text-[var(--color-ink)] flex items-center gap-2.5">
                                    <span class="os-icon-tile os-icon-tile-ink !h-9 !w-9 !text-base">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                        </svg>
                                    </span>
                                    Email configuration
                                </h3>
                                <p class="text-sm text-[var(--color-muted)] mt-1.5">SMTP settings for sending password reset emails</p>
                            </div>
                            <button wire:click="testEmailConfiguration" type="button"
                                    wire:loading.attr="disabled" wire:target="testEmailConfiguration"
                                    class="os-btn os-btn-ghost !px-3 !py-1.5 !text-sm text-[var(--color-accent)] disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="testEmailConfiguration">Test configuration</span>
                                <span wire:loading wire:target="testEmailConfiguration" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24">
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
                                <label class="os-label">SMTP host</label>
                                <input type="text" wire:model="smtp_host" placeholder="smtp.gmail.com"
                                       class="os-input">
                                @error('smtp_host') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">SMTP port</label>
                                <input type="number" wire:model="smtp_port" placeholder="587"
                                       class="os-input">
                                @error('smtp_port') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">SMTP username</label>
                                <input type="text" wire:model="smtp_username" placeholder="your-email@gmail.com"
                                       class="os-input">
                                @error('smtp_username') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">SMTP password</label>
                                <input type="password" wire:model="smtp_password" placeholder="App password or SMTP password"
                                       class="os-input">
                                @error('smtp_password') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">Encryption</label>
                                <select wire:model="smtp_encryption" class="os-input">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>

                            <div>
                                <label class="os-label">From email</label>
                                <input type="email" wire:model="from_email" placeholder="noreply@yourcompany.com"
                                       class="os-input">
                                @error('from_email') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">From name</label>
                                <input type="text" wire:model="from_name" placeholder="CAS System"
                                       class="os-input">
                                @error('from_name') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <button wire:click="saveSettings" type="button"
                        wire:loading.attr="disabled" wire:target="saveSettings"
                        class="os-btn os-btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="saveSettings">Save security settings</span>
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
            <div class="os-card">
                <div class="px-6 py-5 border-b border-[var(--color-line)]">
                    <h3 class="text-base font-semibold text-[var(--color-ink)] flex items-center gap-2.5">
                        <span class="os-icon-tile !h-9 !w-9 !text-base">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        Two-factor authentication
                    </h3>
                    <p class="text-sm text-[var(--color-muted)] mt-1.5">Secure your account with Google Authenticator</p>
                </div>

                <div class="p-6">
                    @if(!$is_2fa_enabled)
                        @if(!$google2fa_secret)
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-[var(--color-faint)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <p class="text-sm text-[var(--color-muted)] mb-4">2FA is not enabled on your account</p>
                                <button wire:click="generate2FA" type="button"
                                        wire:loading.attr="disabled" wire:target="generate2FA"
                                        class="os-btn os-btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="generate2FA">Set up 2FA</span>
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
                                    <p class="text-sm font-medium text-[var(--color-ink)] mb-3">Scan QR code with Google Authenticator</p>
                                    <div class="inline-block p-4 bg-[var(--color-surface)] border border-[var(--color-line)] rounded-[var(--radius-md)]">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qr_code_url) }}"
                                             alt="QR Code" class="w-32 h-32"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div style="display:none;" class="w-32 h-32 flex items-center justify-center bg-[var(--color-surface-2)] text-xs text-[var(--color-muted)]">
                                            QR Code unavailable<br>Use manual entry below
                                        </div>
                                    </div>
                                    <p class="text-xs text-[var(--color-muted)] mt-3">Or manually enter this code: <br><code class="os-code-inline">{{ $google2fa_secret }}</code></p>
                                </div>

                                <div>
                                    <label class="os-label">Verification code</label>
                                    <input type="text" wire:model="verification_code" placeholder="Enter 6-digit code" maxlength="6"
                                           class="os-input">
                                    @error('verification_code') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex gap-2">
                                    <button wire:click="enable2FA" type="button"
                                            wire:loading.attr="disabled" wire:target="enable2FA"
                                            class="os-btn os-btn-primary os-btn-block flex-1 disabled:opacity-50 disabled:cursor-not-allowed">
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
                                            class="os-btn os-btn-secondary disabled:opacity-50 disabled:cursor-not-allowed">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="space-y-4">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-[var(--color-success)] mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm font-medium text-[var(--color-ink)]">2FA is enabled</p>
                                <p class="text-xs text-[var(--color-muted)]">Your account is secured with 2FA</p>
                            </div>

                            @if($backup_codes)
                                <div class="os-alert os-alert-warning flex-col items-stretch gap-2">
                                    <h4 class="text-xs font-semibold">Backup codes</h4>
                                    <div class="grid grid-cols-2 gap-1 text-xs font-mono">
                                        @foreach($backup_codes as $code)
                                            <div class="bg-[var(--color-surface)] border border-[var(--color-line)] px-2 py-1 rounded-[var(--radius-xs)] text-center text-[var(--color-ink-2)]">{{ $code }}</div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs">Save these codes in a secure place</p>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <button wire:click="regenerateBackupCodes" type="button"
                                        wire:loading.attr="disabled" wire:target="regenerateBackupCodes"
                                        class="os-btn os-btn-secondary flex-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="regenerateBackupCodes">New codes</span>
                                    <span wire:loading wire:target="regenerateBackupCodes" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Generating...
                                    </span>
                                </button>
                                <button wire:click="disable2FA" type="button"
                                        wire:loading.attr="disabled" wire:target="disable2FA"
                                        class="os-btn os-btn-secondary flex-1 !border-[#fecaca] !text-[var(--color-danger)] hover:!bg-[var(--color-danger-soft)] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="disable2FA">Disable 2FA</span>
                                    <span wire:loading wire:target="disable2FA" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24">
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

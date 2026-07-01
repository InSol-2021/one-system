<div class="min-h-screen bg-[var(--color-canvas)]">
    {{-- Page header --}}
    <div class="border-b border-[var(--color-line)] bg-[var(--color-surface)]">
        <div class="os-container py-8">
            <p class="os-eyebrow">Account</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-[var(--color-ink)]">Profile settings</h1>
            <p class="mt-1.5 text-sm text-[var(--color-muted)]">Manage your account, security, and connected systems.</p>
        </div>
    </div>

    <div class="os-container py-8">
        <div class="os-card mb-8 overflow-hidden">
            {{-- Tabs --}}
            <div class="border-b border-[var(--color-line)]">
                <nav class="flex gap-1 px-4 sm:gap-2 sm:px-6">
                    <button
                        wire:click="setActiveTab('profile')"
                        class="inline-flex items-center gap-2 border-b-2 px-3 py-4 text-sm font-medium transition-colors {{ $activeTab === 'profile' ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink-2)]' }}"
                    >
                        <i class="fa-regular fa-user"></i>
                        Profile information
                    </button>
                    <button
                        wire:click="setActiveTab('security')"
                        class="inline-flex items-center gap-2 border-b-2 px-3 py-4 text-sm font-medium transition-colors {{ $activeTab === 'security' ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink-2)]' }}"
                    >
                        <i class="fa-solid fa-lock"></i>
                        Security settings
                    </button>
                    <button
                        wire:click="setActiveTab('systems')"
                        class="inline-flex items-center gap-2 border-b-2 px-3 py-4 text-sm font-medium transition-colors {{ $activeTab === 'systems' ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink-2)]' }}"
                    >
                        <i class="fa-solid fa-grip"></i>
                        Connected systems
                        @if(count($linkedSystems) > 0)
                            <span class="os-badge os-badge-accent">{{ count($linkedSystems) }}</span>
                        @endif
                    </button>
                </nav>
            </div>

            <div class="p-6">
                @if($activeTab === 'profile')
                    <div class="space-y-6">
                        <div class="mb-6 flex items-center gap-3">
                            <span class="os-icon-tile"><i class="fa-regular fa-user"></i></span>
                            <div>
                                <h3 class="text-lg font-semibold text-[var(--color-ink)]">Personal information</h3>
                                <p class="text-sm text-[var(--color-muted)]">Update your basic profile details.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="os-label">First name</label>
                                <input
                                    type="text"
                                    wire:model="first_name"
                                    class="os-input"
                                    placeholder="Enter your first name"
                                >
                                @error('first_name') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">Last name</label>
                                <input
                                    type="text"
                                    wire:model="last_name"
                                    class="os-input"
                                    placeholder="Enter your last name"
                                >
                                @error('last_name') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="os-label">Email address</label>
                                <input
                                    type="email"
                                    wire:model="email"
                                    class="os-input"
                                    placeholder="Enter your email address"
                                >
                                @error('email') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-[var(--color-line)] pt-6">
                            <p class="text-sm text-[var(--color-muted)]">Last updated: {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}</p>
                            <button
                                wire:click="updateProfile"
                                wire:loading.attr="disabled"
                                class="os-btn os-btn-primary disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="updateProfile">Update profile</span>
                                <span wire:loading wire:target="updateProfile">Updating...</span>
                            </button>
                        </div>
                    </div>

                @elseif($activeTab === 'security')
                    <div class="space-y-6">
                        {{-- Password security --}}
                        <div class="os-card os-card-pad">
                            <div class="mb-6 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="os-icon-tile os-icon-tile-ink"><i class="fa-solid fa-lock"></i></span>
                                    <div>
                                        <h3 class="text-base font-semibold text-[var(--color-ink)]">Password security</h3>
                                        <p class="text-sm text-[var(--color-muted)]">Keep your account secure with a strong password.</p>
                                    </div>
                                </div>
                                @if(!$showPasswordForm)
                                    <button
                                        wire:click="$set('showPasswordForm', true)"
                                        class="os-btn os-btn-secondary"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                        Change password
                                    </button>
                                @endif
                            </div>

                            @if($showPasswordForm)
                                <div class="space-y-4" wire:key="password-form-{{ $user->id }}">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                        <div>
                                            <label class="os-label">Current password</label>
                                            <input
                                                type="password"
                                                wire:model="currentPassword"
                                                class="os-input"
                                                placeholder="Enter current password"
                                            >
                                            @error('currentPassword') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="os-label">New password</label>
                                            <input
                                                type="password"
                                                wire:model="newPassword"
                                                class="os-input"
                                                placeholder="Enter new password"
                                            >
                                            @error('newPassword') <span class="mt-1 block text-sm text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="os-label">Confirm password</label>
                                            <input
                                                type="password"
                                                wire:model="newPassword_confirmation"
                                                class="os-input"
                                                placeholder="Confirm new password"
                                            >
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-4 border-t border-[var(--color-line)] pt-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="text-sm text-[var(--color-muted)]">
                                            <p>Password requirements:</p>
                                            <ul class="mt-1 list-inside list-disc text-xs text-[var(--color-faint)]">
                                                <li>At least 8 characters long</li>
                                                <li>Include uppercase and lowercase letters</li>
                                                <li>Include numbers and special characters</li>
                                            </ul>
                                        </div>
                                        <div class="flex gap-3">
                                            <button
                                                wire:click="changePassword"
                                                wire:loading.attr="disabled"
                                                wire:key="password-update-btn-{{ now() }}"
                                                class="os-btn os-btn-primary disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                <span wire:loading.remove wire:target="changePassword">Update password</span>
                                                <span wire:loading wire:target="changePassword">Updating...</span>
                                            </button>
                                            <button
                                                wire:click="$set('showPasswordForm', false)"
                                                class="os-btn os-btn-secondary"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-[var(--color-muted)]">
                                    <p>Last changed: {{ $user->password_changed_at ? $user->password_changed_at->diffForHumans() : 'Never' }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Two-factor authentication --}}
                        <div class="os-card os-card-pad">
                            <div class="mb-6 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="os-icon-tile os-icon-tile-ink"><i class="fa-solid fa-shield-halved"></i></span>
                                    <div>
                                        <h3 class="text-base font-semibold text-[var(--color-ink)]">Two-factor authentication</h3>
                                        <p class="text-sm text-[var(--color-muted)]">Add an extra layer of security to your account.</p>
                                    </div>
                                </div>

                                @if($user && $user->two_factor_enabled)
                                    <div class="flex items-center gap-4">
                                        <span class="os-badge"><span class="h-1.5 w-1.5 rounded-full bg-[var(--color-success)]"></span>Enabled</span>
                                        <button
                                            wire:click="disable2FA"
                                            wire:confirm="Are you sure you want to disable two-factor authentication?"
                                            class="os-btn os-btn-secondary text-[var(--color-danger)]"
                                        >
                                            Disable 2FA
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-center gap-4">
                                        <span class="os-badge"><span class="h-1.5 w-1.5 rounded-full bg-[var(--color-faint)]"></span>Disabled</span>
                                        <button
                                            wire:click="setup2FA"
                                            class="os-btn os-btn-primary"
                                        >
                                            <i class="fa-solid fa-plus"></i>
                                            Enable 2FA
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if($show2FAForm)
                                <div class="space-y-6 border-t border-[var(--color-line)] pt-6">
                                    <div class="rounded-[var(--radius-md)] border border-[var(--color-line)] bg-[var(--color-surface-2)] p-4">
                                        <h4 class="mb-3 font-medium text-[var(--color-ink)]">Setup instructions</h4>
                                        <ol class="space-y-2 text-sm text-[var(--color-ink-2)]">
                                            <li class="flex items-start gap-2">
                                                <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-[var(--color-accent-soft)] text-xs font-semibold text-[var(--color-accent)]">1</span>
                                                <span>Install Google Authenticator or similar app on your phone</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-[var(--color-accent-soft)] text-xs font-semibold text-[var(--color-accent)]">2</span>
                                                <span>Scan the QR code below or enter the secret key manually</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-[var(--color-accent-soft)] text-xs font-semibold text-[var(--color-accent)]">3</span>
                                                <span>Enter the 6-digit code from your app to verify</span>
                                            </li>
                                        </ol>
                                    </div>

                                    @if($twoFactorQrCode)
                                        <div class="rounded-[var(--radius-md)] border border-dashed border-[var(--color-line-strong)] bg-[var(--color-surface)] p-6 text-center">
                                            <img src="{{ $twoFactorQrCode }}" alt="QR Code" class="mx-auto mb-4">
                                            <p class="rounded-[var(--radius-xs)] bg-[var(--color-surface-2)] p-2 font-mono text-xs text-[var(--color-muted)]">{{ $twoFactorSecret }}</p>
                                        </div>
                                    @endif

                                    <div class="mx-auto max-w-xs">
                                        <label class="os-label">Verification code</label>
                                        <input
                                            type="text"
                                            wire:model="twoFactorCode"
                                            class="os-input text-center font-mono text-lg tracking-[0.3em]"
                                            placeholder="000000"
                                            maxlength="6"
                                        >
                                    </div>

                                    <div class="flex items-center justify-center gap-4">
                                        <button
                                            wire:click="enable2FA"
                                            class="os-btn os-btn-primary"
                                        >
                                            <i class="fa-solid fa-circle-check"></i>
                                            Enable 2FA
                                        </button>
                                        <button
                                            wire:click="$set('show2FAForm', false)"
                                            class="os-btn os-btn-secondary"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                @elseif($activeTab === 'systems')
                    <div class="space-y-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="os-icon-tile"><i class="fa-solid fa-grip"></i></span>
                                <div>
                                    <h3 class="text-lg font-semibold text-[var(--color-ink)]">Dashboard visibility settings</h3>
                                    <p class="text-sm text-[var(--color-muted)]">Control which systems appear on your dashboard.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-[var(--color-muted)]">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Show or hide systems regardless of link status</span>
                            </div>
                        </div>

                        @if(count($linkedSystems) > 0)
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                @foreach($linkedSystems as $system)
                                    <div class="os-card os-card-hover" wire:key="system-{{ $system['client_system_id'] }}">
                                        <div class="p-6">
                                            <div class="mb-4 flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-[var(--radius-md)] bg-[var(--color-surface-2)] text-base font-semibold text-[var(--color-ink-2)]">
                                                        {{ strtoupper(substr($system['name'], 0, 2)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <h4 class="truncate text-base font-semibold text-[var(--color-ink)]">{{ $system['name'] }}</h4>
                                                        @if($system['description'])
                                                            <p class="mt-1 line-clamp-2 text-sm text-[var(--color-muted)]">{{ $system['description'] }}</p>
                                                        @endif
                                                        <p class="mt-2 truncate text-xs text-[var(--color-faint)]">{{ $system['callback_url'] }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    @if($system['is_active'])
                                                        <span class="h-2.5 w-2.5 rounded-full bg-[var(--color-success)]" title="Active"></span>
                                                    @else
                                                        <span class="h-2.5 w-2.5 rounded-full bg-[var(--color-faint)]" title="Inactive"></span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="space-y-4">
                                                <div class="flex items-center justify-between rounded-[var(--radius-sm)] bg-[var(--color-surface-2)] p-3 text-sm">
                                                    <span class="font-medium text-[var(--color-ink-2)]">Dashboard visibility</span>
                                                    <div class="flex items-center">
                                                        @if($system['show_in_dashboard'])
                                                            <span class="os-badge os-badge-accent">
                                                                <i class="fa-solid fa-eye"></i>
                                                                Visible
                                                            </span>
                                                        @else
                                                            <span class="os-badge">
                                                                <i class="fa-solid fa-eye-slash"></i>
                                                                Hidden
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4 flex justify-center border-t border-[var(--color-line)] pt-4">
                                                <button
                                                    wire:click="toggleSystemVisibility({{ $system['id'] ?? 'null' }}, {{ $system['client_system_id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleSystemVisibility"
                                                    wire:key="toggle-btn-{{ $loop->index }}-{{ $system['client_system_id'] }}"
                                                    class="os-btn os-btn-secondary disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                    <x-icon name="{{ $system['show_in_dashboard'] ? 'eye-off' : 'eye' }}" class="w-4 h-4 mr-2" />
                                                    <span wire:loading.remove wire:target="toggleSystemVisibility">{{ $system['show_in_dashboard'] ? 'Hide from Dashboard' : 'Show on Dashboard' }}</span>
                                                    <span wire:loading wire:target="toggleSystemVisibility">Processing...</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-12 text-center">
                                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-[var(--color-surface-2)]">
                                    <x-icon name="desktop" class="w-10 h-10 text-[var(--color-faint)]" />
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-[var(--color-ink)]">No systems available</h3>
                                <p class="mx-auto max-w-md text-[var(--color-muted)]">No client systems are currently configured in the database. Contact your administrator to set up systems.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($message)
        <div class="fixed bottom-4 right-4 z-50" wire:key="message-{{ now() }}">
            <div class="os-card pointer-events-auto overflow-hidden border-l-4 {{ $messageType === 'success' ? 'border-l-[var(--color-success)]' : 'border-l-[var(--color-danger)]' }}" style="min-width: 320px; max-width: 400px; box-shadow: var(--shadow-lg);">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($messageType === 'success')
                                <i class="fa-solid fa-circle-check text-lg text-[var(--color-success)]"></i>
                            @else
                                <i class="fa-solid fa-circle-exclamation text-lg text-[var(--color-danger)]"></i>
                            @endif
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium {{ $messageType === 'success' ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]' }}">
                                {{ $message }}
                            </p>
                        </div>
                        <div class="ml-4 flex flex-shrink-0">
                            <button wire:click="clearMessage" class="inline-flex text-[var(--color-faint)] hover:text-[var(--color-ink-2)] focus:outline-none">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            setTimeout(() => {
            @this.call('clearMessage');
            }, 5000);
        </script>
    @endif
</div>

<div class="relative">

    @if (session()->has('message'))
        <div class="os-alert os-alert-success mb-6">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="os-alert os-alert-danger mb-6">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-8">
        <p class="os-eyebrow mb-2">Admin profile</p>
        <h2 class="text-2xl font-semibold tracking-tight">Profile settings</h2>
        <p class="mt-1.5 text-sm text-[var(--color-muted)]">Manage your admin account information and security settings.</p>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-8">
            <div class="os-card">
                <div class="flex items-start gap-3 border-b border-[var(--color-line)] px-6 py-5">
                    <span class="os-icon-tile os-icon-tile-ink">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold tracking-tight">Profile information</h3>
                        <p class="mt-0.5 text-sm text-[var(--color-muted)]">Update your admin account details.</p>
                    </div>
                </div>

                <form wire:submit.prevent="updateProfile" class="space-y-6 p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="os-label">Username</label>
                            <input type="text" wire:model="username" class="os-input">
                            @error('username') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="os-label">Email address</label>
                            <input type="email" wire:model="email" class="os-input">
                            @error('email') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="os-label">First name</label>
                            <input type="text" wire:model="first_name" class="os-input">
                            @error('first_name') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="os-label">Last name</label>
                            <input type="text" wire:model="last_name" class="os-input">
                            @error('last_name') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-[var(--color-line)] pt-6">
                        <button type="submit"
                                wire:loading.attr="disabled" wire:target="updateProfile"
                                class="os-btn os-btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="updateProfile">Update profile</span>
                            <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="os-card">
                <div class="flex items-start justify-between gap-4 border-b border-[var(--color-line)] px-6 py-5">
                    <div class="flex items-start gap-3">
                        <span class="os-icon-tile os-icon-tile-ink">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <div>
                            <h3 class="text-base font-semibold tracking-tight">Change password</h3>
                            <p class="mt-0.5 text-sm text-[var(--color-muted)]">Update your account password for security.</p>
                        </div>
                    </div>
                    <button wire:click="togglePasswordSection"
                            class="os-btn {{ $showPasswordSection ? 'os-btn-ghost' : 'os-btn-secondary' }}">
                        {{ $showPasswordSection ? 'Cancel' : 'Change password' }}
                    </button>
                </div>

                @if($showPasswordSection)
                    <form wire:submit.prevent="changePassword" class="space-y-6 p-6">
                        <div>
                            <label class="os-label">Current password</label>
                            <input type="password" wire:model="current_password" class="os-input">
                            @error('current_password') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="os-label">New password</label>
                                <input type="password" wire:model="new_password" class="os-input">
                                @error('new_password') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="os-label">Confirm new password</label>
                                <input type="password" wire:model="new_password_confirmation" class="os-input">
                            </div>
                        </div>

                        <div class="os-alert os-alert-warning">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <div>
                                <h4 class="text-sm font-medium">Password requirements</h4>
                                <ul class="mt-1 ml-5 list-disc space-y-1 text-xs">
                                    <li>At least 8 characters long</li>
                                    <li>Contains uppercase and lowercase letters</li>
                                    <li>Contains at least one number</li>
                                    <li>Contains at least one special character</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 border-t border-[var(--color-line)] pt-6">
                            <button type="button" wire:click="togglePasswordSection"
                                    wire:loading.attr="disabled" wire:target="changePassword"
                                    class="os-btn os-btn-secondary disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                            <button type="submit"
                                    wire:loading.attr="disabled" wire:target="changePassword"
                                    class="os-btn os-btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="changePassword">Change password</span>
                                <span wire:loading wire:target="changePassword" class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                    Changing...
                                </span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-10 text-center">
                        <span class="os-icon-tile os-icon-tile-ink mx-auto mb-4">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <p class="text-sm text-[var(--color-muted)]">Click "Change password" to update your account password.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="os-card os-card-pad">
                <h4 class="text-sm font-semibold tracking-tight">Account information</h4>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[var(--color-muted)]">Role</dt>
                        <dd class="font-medium text-[var(--color-ink-2)]">Administrator</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[var(--color-muted)]">Account status</dt>
                        <dd><span class="os-badge os-badge-accent">Active</span></dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-[var(--color-muted)]">Last login</dt>
                        <dd class="font-medium text-[var(--color-ink-2)]">{{ now()->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="os-card os-card-pad">
                <h4 class="text-sm font-semibold tracking-tight">Security tips</h4>
                <ul class="mt-4 space-y-2 text-xs text-[var(--color-muted)]">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check mt-0.5 text-[var(--color-accent)]"></i>
                        <span>Use a strong, unique password</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check mt-0.5 text-[var(--color-accent)]"></i>
                        <span>Change your password regularly</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check mt-0.5 text-[var(--color-accent)]"></i>
                        <span>Keep your profile information up to date</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check mt-0.5 text-[var(--color-accent)]"></i>
                        <span>Log out when finished working</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

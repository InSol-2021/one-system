<div class="relative">
    <div wire:loading.delay wire:target="saveSettings,resetToDefaults" class="absolute inset-0 bg-[var(--color-surface)]/75 z-40 flex items-center justify-center">
        <x-loading-overlay>Saving settings...</x-loading-overlay>
    </div>

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
        <p class="os-eyebrow mb-2">SSO</p>
        <h2 class="text-2xl font-semibold tracking-tight">SSO token configuration</h2>
        <p class="mt-1.5 text-sm text-[var(--color-muted)]">Configure SSO token settings including expiration, security, and validation parameters.</p>
    </div>

    <form wire:submit.prevent="saveSettings" class="space-y-6">
        {{-- Token configuration --}}
        <div class="os-card">
            <div class="flex items-start gap-3.5 border-b border-[var(--color-line)] px-6 py-5">
                <span class="os-icon-tile os-icon-tile-ink mt-0.5">
                    <i class="fa-solid fa-key"></i>
                </span>
                <div>
                    <h3 class="text-base font-semibold text-[var(--color-ink)]">Token configuration</h3>
                    <p class="mt-0.5 text-sm text-[var(--color-muted)]">Basic token generation and lifecycle settings</p>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="os-label">Token expiry (minutes)</label>
                        <input type="number" wire:model="token_expiry_minutes" min="5" max="1440" class="os-input">
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">How long tokens remain valid (5-1440 minutes)</p>
                        @error('token_expiry_minutes') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Max concurrent tokens</label>
                        <input type="number" wire:model="max_concurrent_tokens" min="1" max="20" class="os-input">
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">Maximum active tokens per user (1-20)</p>
                        @error('max_concurrent_tokens') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Token issuer</label>
                        <input type="text" wire:model="token_issuer" maxlength="100" class="os-input">
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">JWT issuer claim (iss)</p>
                        @error('token_issuer') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Token audience</label>
                        <input type="text" wire:model="token_audience" maxlength="100" class="os-input">
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">JWT audience claim (aud)</p>
                        @error('token_audience') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Token refresh settings --}}
        <div class="os-card">
            <div class="flex items-start gap-3.5 border-b border-[var(--color-line)] px-6 py-5">
                <span class="os-icon-tile os-icon-tile-ink mt-0.5">
                    <i class="fa-solid fa-rotate"></i>
                </span>
                <div>
                    <h3 class="text-base font-semibold text-[var(--color-ink)]">Token refresh settings</h3>
                    <p class="mt-0.5 text-sm text-[var(--color-muted)]">Configure automatic token refresh and renewal</p>
                </div>
            </div>
            <div class="space-y-6 p-6">
                <label class="flex items-center justify-between gap-4">
                    <span class="text-sm font-medium text-[var(--color-ink-2)]">Enable automatic token refresh</span>
                    <span class="relative inline-flex flex-shrink-0">
                        <input type="checkbox" wire:model="enable_token_refresh" class="peer sr-only">
                        <span class="h-5 w-9 rounded-full bg-[var(--color-line-strong)] transition-colors peer-checked:bg-[var(--color-accent)] peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--color-accent-soft)]"></span>
                        <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-4"></span>
                    </span>
                </label>

                @if($enable_token_refresh)
                    <div class="max-w-sm">
                        <label class="os-label">Refresh threshold (minutes)</label>
                        <input type="number" wire:model="token_refresh_threshold" min="1" max="60" class="os-input">
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">Refresh token when this many minutes remain before expiry</p>
                        @error('token_refresh_threshold') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>
        </div>

        {{-- Security settings --}}
        <div class="os-card">
            <div class="flex items-start gap-3.5 border-b border-[var(--color-line)] px-6 py-5">
                <span class="os-icon-tile os-icon-tile-ink mt-0.5">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <div>
                    <h3 class="text-base font-semibold text-[var(--color-ink)]">Security settings</h3>
                    <p class="mt-0.5 text-sm text-[var(--color-muted)]">Cryptographic and validation security options</p>
                </div>
            </div>
            <div class="space-y-6 p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="os-label">Signature algorithm</label>
                        <select wire:model="signature_algorithm" class="os-input">
                            <option value="HS256">HMAC SHA-256 (HS256)</option>
                            <option value="HS384">HMAC SHA-384 (HS384)</option>
                            <option value="HS512">HMAC SHA-512 (HS512)</option>
                            <option value="RS256">RSA SHA-256 (RS256)</option>
                        </select>
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">Cryptographic algorithm for token signing</p>
                        @error('signature_algorithm') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Max failed attempts</label>
                        <input type="number" wire:model="max_failed_attempts" min="1" max="10" class="os-input">
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">Failed authentication attempts before lockout</p>
                        @error('max_failed_attempts') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Lockout duration (minutes)</label>
                        <input type="number" wire:model="lockout_duration" min="5" max="1440" class="os-input">
                        <p class="mt-1.5 text-xs text-[var(--color-faint)]">How long to lock accounts after failed attempts</p>
                        @error('lockout_duration') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="divide-y divide-[var(--color-line)] rounded-[var(--radius-md)] border border-[var(--color-line)]">
                    <label class="flex items-center justify-between gap-4 px-4 py-3.5">
                        <span class="text-sm font-medium text-[var(--color-ink-2)]">Require IP address validation for tokens</span>
                        <span class="relative inline-flex flex-shrink-0">
                            <input type="checkbox" wire:model="require_ip_validation" class="peer sr-only">
                            <span class="h-5 w-9 rounded-full bg-[var(--color-line-strong)] transition-colors peer-checked:bg-[var(--color-accent)] peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--color-accent-soft)]"></span>
                            <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-4"></span>
                        </span>
                    </label>

                    <label class="flex items-center justify-between gap-4 px-4 py-3.5">
                        <span class="text-sm font-medium text-[var(--color-ink-2)]">Enable comprehensive audit logging</span>
                        <span class="relative inline-flex flex-shrink-0">
                            <input type="checkbox" wire:model="enable_audit_logging" class="peer sr-only">
                            <span class="h-5 w-9 rounded-full bg-[var(--color-line-strong)] transition-colors peer-checked:bg-[var(--color-accent)] peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--color-accent-soft)]"></span>
                            <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-4"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Configuration summary --}}
        <div class="os-card os-card-pad bg-[var(--color-accent-soft)] border-[var(--color-accent-line)]">
            <h4 class="text-sm font-semibold text-[var(--color-accent-strong)]">Current configuration summary</h4>
            <div class="mt-3 space-y-1.5 text-xs text-[var(--color-ink-2)]">
                <p><span class="font-medium text-[var(--color-ink)]">Token expiry:</span> {{ $token_expiry_minutes }} minutes ({{ number_format($token_expiry_minutes / 60, 1) }} hours)</p>
                <p><span class="font-medium text-[var(--color-ink)]">Refresh:</span> {{ $enable_token_refresh ? 'Enabled' : 'Disabled' }}{{ $enable_token_refresh ? " (threshold: {$token_refresh_threshold} min)" : '' }}</p>
                <p><span class="font-medium text-[var(--color-ink)]">Security:</span> {{ $signature_algorithm }} signing, IP validation {{ $require_ip_validation ? 'required' : 'optional' }}</p>
                <p><span class="font-medium text-[var(--color-ink)]">Rate limiting:</span> {{ $max_failed_attempts }} attempts, {{ $lockout_duration }} min lockout</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[var(--color-line)] pt-6">
            <button type="button" wire:click="resetToDefaults" class="os-btn os-btn-ghost">
                Reset to defaults
            </button>

            <div class="flex items-center gap-3">
                <button type="button" onclick="window.location.reload()" class="os-btn os-btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="os-btn os-btn-primary">
                    Save settings
                </button>
            </div>
        </div>
    </form>
</div>

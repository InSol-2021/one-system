<div class="min-h-screen bg-[var(--color-canvas)]">
    <!-- Header -->
    <div class="border-b border-[var(--color-line)] bg-[var(--color-surface)]">
        <div class="os-container py-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="os-eyebrow">Administration</div>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight text-[var(--color-ink)]">Client systems</h1>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Manage connected applications and their SSO credentials.</p>
                </div>
                <button wire:click="$set('showCreateForm', true)" class="os-btn os-btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Add client system
                </button>
            </div>
        </div>
    </div>

    @if($loading)
        <div class="flex h-64 items-center justify-center">
            <div class="h-10 w-10 animate-spin rounded-full border-2 border-[var(--color-line)] border-t-[var(--color-accent)]"></div>
            <span class="ml-3 text-sm text-[var(--color-muted)]">Loading client systems…</span>
        </div>
    @endif

    @if(!$loading)
        <div class="os-container py-8">
            @if(empty($clientSystems))
                <div class="os-card os-card-pad text-center">
                    <div class="mx-auto os-icon-tile os-icon-tile-ink">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-[var(--color-ink)]">No client systems</h3>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Get started by creating your first client system.</p>
                    <div class="mt-6">
                        <button wire:click="$set('showCreateForm', true)" class="os-btn os-btn-primary">
                            <i class="fa-solid fa-plus text-xs"></i>
                            Add client system
                        </button>
                    </div>
                </div>
            @else
                <div class="grid gap-5">
                    @foreach($clientSystems as $system)
                        <div wire:key="client-system-{{ $system['id'] }}" class="os-card os-card-hover">
                            @if($editingSystemId === $system['id'])
                                <!-- Edit Form -->
                                <div class="os-card-pad">
                                    <form wire:submit.prevent="updateClientSystem">
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="os-label">Name</label>
                                                <input type="text" wire:model="editName" required class="os-input">
                                                @error('editName') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="os-label">Status</label>
                                                <select wire:model="editStatus" required class="os-input">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="os-label">Description</label>
                                            <textarea wire:model="editDescription" rows="2" class="os-input"></textarea>
                                            @error('editDescription') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="mt-4">
                                            <label class="os-label">Callback URL</label>
                                            <input type="url" wire:model="editCallbackUrl" required class="os-input">
                                            @error('editCallbackUrl') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="mt-5 flex gap-2">
                                            <button type="submit" class="os-btn os-btn-primary">
                                                Save changes
                                            </button>
                                            <button type="button" wire:click="cancelEdit" class="os-btn os-btn-ghost">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="os-card-pad">
                                    <div class="mb-4 flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <h3 class="text-lg font-semibold text-[var(--color-ink)]">{{ $system['name'] }}</h3>
                                            <p class="break-all text-sm text-[var(--color-muted)]">{{ $system['callback_url'] ?? 'No callback URL set' }}</p>
                                            <p class="mt-1 text-xs text-[var(--color-faint)]">Client ID: <span class="font-mono">{{ $system['client_id'] }}</span></p>
                                        </div>
                                        <div class="flex flex-shrink-0 items-center gap-2">
                                            @if($system['is_active'])
                                                <span class="os-badge os-badge-accent">Active</span>
                                            @else
                                                <span class="os-badge">Inactive</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="os-label">Callback URL</label>
                                            <p class="break-all text-sm text-[var(--color-ink-2)]">{{ $system['callback_url'] }}</p>
                                        </div>
                                        <div>
                                            <label class="os-label">Last accessed</label>
                                            <p class="text-sm text-[var(--color-ink-2)]">
                                                {{ $system['last_accessed'] ? \Carbon\Carbon::parse($system['last_accessed'])->diffForHumans() : 'Never' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-4 border-t border-[var(--color-line)] pt-4">
                                        <h4 class="mb-3 text-sm font-semibold text-[var(--color-ink)]">Security status</h4>
                                        <div class="grid gap-4 text-sm md:grid-cols-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[var(--color-muted)]">Credentials:</span>
                                                @if($system['security_status']['credentials_viewed'])
                                                    <span class="inline-flex items-center gap-1.5 font-medium text-[var(--color-success)]">
                                                        <i class="fa-solid fa-circle-check"></i>
                                                        Secured
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 font-medium text-[var(--color-warning)]">
                                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                                        Not viewed
                                                    </span>
                                                @endif
                                            </div>
                                            @if($system['security_status']['credentials_viewed_at'])
                                                <div>
                                                    <span class="text-[var(--color-muted)]">Viewed:</span>
                                                    <span class="text-[var(--color-ink-2)]">{{ \Carbon\Carbon::parse($system['security_status']['credentials_viewed_at'])->diffForHumans() }}</span>
                                                </div>
                                            @endif
                                            @if($system['security_status']['credentials_regenerated_at'])
                                                <div>
                                                    <span class="text-[var(--color-muted)]">Regenerated:</span>
                                                    <span class="text-[var(--color-ink-2)]">{{ \Carbon\Carbon::parse($system['security_status']['credentials_regenerated_at'])->diffForHumans() }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button wire:key="edit-btn-{{ $system['id'] }}"
                                                wire:click="startEdit({{ $system['id'] }})"
                                                wire:loading.attr="disabled"
                                                class="os-btn os-btn-secondary disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                            Edit
                                        </button>
                                        <button wire:key="toggle-btn-{{ $system['id'] }}"
                                                wire:click="toggleSystemStatus({{ $system['id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:target="toggleSystemStatus"
                                                class="os-btn os-btn-secondary disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fa-solid fa-power-off text-xs"></i>
                                            {{ $system['is_active'] ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button wire:key="regen-btn-{{ $system['id'] }}"
                                                wire:click="startRegenerate({{ $system['id'] }})"
                                                wire:loading.attr="disabled"
                                                class="os-btn os-btn-secondary disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fa-solid fa-rotate text-xs"></i>
                                            Regenerate credentials
                                        </button>
                                        <button wire:key="delete-btn-{{ $system['id'] }}"
                                                wire:click="deleteClientSystem({{ $system['id'] }})"
                                                wire:confirm="Are you sure you want to delete this client system? This action cannot be undone."
                                                wire:loading.attr="disabled"
                                                class="os-btn os-btn-secondary text-[var(--color-danger)] disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fa-solid fa-trash text-xs"></i>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--color-ink)]/40 p-4" wire:click.self="cancelCreate">
            <div class="os-card os-card-pad w-full max-w-md shadow-[var(--shadow-lg)]">
                <h3 class="mb-1 text-lg font-semibold text-[var(--color-ink)]">Create new client system</h3>
                <p class="mb-5 text-sm text-[var(--color-muted)]">Register a new application for single sign-on.</p>
                <form wire:submit.prevent="createClientSystem">
                    <div class="space-y-4">
                        <div>
                            <label class="os-label">Name</label>
                            <input type="text" wire:model="name" required class="os-input" placeholder="Customer Portal">
                            @error('name') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="os-label">Description</label>
                            <input type="text" wire:model="description" class="os-input" placeholder="Brief description of this system">
                            @error('description') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="os-label">Callback URL</label>
                            <input type="url" wire:model="callback_url" required class="os-input" placeholder="https://portal.example.com/cas/callback">
                            @error('callback_url') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="os-btn os-btn-primary flex-1">
                            Create system
                        </button>
                        <button type="button" wire:click="cancelCreate" class="os-btn os-btn-ghost">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showCredentials)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--color-ink)]/40 p-4">
            <div class="os-card os-card-pad w-full max-w-2xl shadow-[var(--shadow-lg)]">
                <div class="mb-6 text-center">
                    <div class="mx-auto os-icon-tile">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-[var(--color-ink)]">{{ $createdSystemName }} created successfully</h3>
                    <p class="mt-2 text-sm font-medium text-[var(--color-danger)]">These credentials will only be shown once. Copy them now.</p>
                </div>

                <div class="space-y-4">
                    <!-- Client ID -->
                    <div>
                        <label class="os-label">Client ID</label>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $newCredentials['client_id'] }}" readonly
                                   class="os-input flex-1 bg-[var(--color-surface-2)] font-mono">
                            <button onclick="copyToClipboard('{{ $newCredentials['client_id'] }}', 'Client ID')"
                                    class="os-btn os-btn-secondary" aria-label="Copy Client ID">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Client Secret -->
                    <div>
                        <label class="os-label">Client Secret</label>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $newCredentials['client_secret'] }}" readonly
                                   class="os-input flex-1 bg-[var(--color-surface-2)] font-mono">
                            <button onclick="copyToClipboard('{{ $newCredentials['client_secret'] }}', 'Client Secret')"
                                    class="os-btn os-btn-secondary" aria-label="Copy Client Secret">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Webhook Secret -->
                    <div>
                        <label class="os-label">Webhook Secret</label>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $newCredentials['webhook_secret'] }}" readonly
                                   class="os-input flex-1 bg-[var(--color-surface-2)] font-mono">
                            <button onclick="copyToClipboard('{{ $newCredentials['webhook_secret'] }}', 'Webhook Secret')"
                                    class="os-btn os-btn-secondary" aria-label="Copy Webhook Secret">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="os-alert os-alert-warning mt-6">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold">Important security notice</h4>
                        <ul class="mt-1 list-inside list-disc space-y-1 text-sm">
                            <li>Store these credentials securely in your customer portal configuration</li>
                            <li>Never share these credentials or commit them to version control</li>
                            <li>These credentials will not be shown again after closing this dialog</li>
                            <li>Use the "Regenerate credentials" feature if you lose them</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 flex justify-center">
                    <button wire:click="closeCredentials" class="os-btn os-btn-primary os-btn-lg">
                        I have copied the credentials
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Regenerate Credentials Modal -->
    @if($showRegenerateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--color-ink)]/40 p-4" wire:click.self="cancelRegenerate">
            <div class="os-card os-card-pad w-full max-w-md shadow-[var(--shadow-lg)]">
                <h3 class="mb-4 text-lg font-semibold text-[var(--color-danger)]">Regenerate credentials</h3>
                <p class="mb-4 text-sm text-[var(--color-muted)]">
                    This will generate new credentials for <strong class="text-[var(--color-ink-2)]">{{ $regenerateSystemName }}</strong> and invalidate all existing SSO tokens.
                </p>
                <form wire:submit.prevent="regenerateCredentials">
                    <div class="mb-4">
                        <label class="os-label">Reason for regeneration</label>
                        <textarea wire:model="regenerateReason" required rows="3" class="os-input"
                                  placeholder="Please explain why you need to regenerate credentials…"></textarea>
                        @error('regenerateReason') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="os-btn os-btn-primary flex-1">
                            Regenerate credentials
                        </button>
                        <button type="button" wire:click="cancelRegenerate" class="os-btn os-btn-ghost">
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
            <div class="os-alert {{ $messageType === 'success' ? 'os-alert-success' : 'os-alert-danger' }} shadow-[var(--shadow-md)]">
                @if($messageType === 'success')
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                @else
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                @endif
                <span class="flex-1">{{ $message }}</span>
                <button wire:click="hideMessage" class="ml-2 opacity-70 hover:opacity-100" aria-label="Dismiss message">
                    <i class="fa-solid fa-xmark"></i>
                </button>
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

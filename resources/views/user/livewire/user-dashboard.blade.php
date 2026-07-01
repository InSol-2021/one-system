<div class="min-h-screen bg-[var(--color-canvas)]">
    @if(session('success'))
        <div class="os-container pt-6">
            <div class="os-alert os-alert-success" role="alert">
                <x-icon name="shield" class="w-5 h-5 mt-0.5 flex-shrink-0" />
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Page header --}}
    <div class="border-b border-[var(--color-line)] bg-[var(--color-surface)]">
        <div class="os-container py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="os-eyebrow">SSO dashboard</span>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-[var(--color-ink)]">
                        Welcome back, {{ $user->first_name ?? 'User' }}
                    </h1>
                    <p class="mt-2 text-[15px] text-[var(--color-muted)]">
                        Your secure gateway to all connected applications.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="os-badge">
                            <x-icon name="shield" class="w-3.5 h-3.5" />
                            Secure access
                        </span>
                        <span class="os-badge">
                            <x-icon name="clock" class="w-3.5 h-3.5" />
                            One-click login
                        </span>
                        <span class="os-badge">
                            <x-icon name="lock" class="w-3.5 h-3.5" />
                            Encrypted
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button wire:click="loadUserDashboard" wire:loading.attr="disabled"
                            class="os-btn os-btn-secondary group">
                        <div wire:loading.remove class="flex items-center gap-2">
                            <x-icon name="refresh" class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" />
                            Refresh
                        </div>
                        <div wire:loading class="flex items-center gap-2">
                            <div class="h-4 w-4 animate-spin rounded-full border-2 border-[var(--color-line-strong)] border-t-[var(--color-accent)]"></div>
                            Loading...
                        </div>
                    </button>

                    <a href="{{ route('user.profile.livewire') }}" class="os-btn os-btn-primary">
                        <x-icon name="settings" class="w-4 h-4" />
                        Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="os-container py-10">

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-4">
            <div class="os-card os-card-pad">
                <div class="mb-4 flex items-center justify-between">
                    <span class="os-icon-tile">
                        <x-icon name="link" class="w-5 h-5" />
                    </span>
                    <span class="os-badge os-badge-accent">Active</span>
                </div>
                <p class="text-3xl font-semibold tracking-tight text-[var(--color-ink)]">{{ collect($this->clientSystems)->where('is_linked', true)->count() }}</p>
                <p class="mt-1 text-sm font-medium text-[var(--color-ink-2)]">Connected systems</p>
                <p class="mt-1 text-xs text-[var(--color-faint)]">Active and ready to use</p>
            </div>

            <div class="os-card os-card-pad">
                <div class="mb-4 flex items-center justify-between">
                    <span class="os-icon-tile os-icon-tile-ink">
                        <x-icon name="desktop" class="w-5 h-5" />
                    </span>
                </div>
                <p class="text-3xl font-semibold tracking-tight text-[var(--color-ink)]">{{ count($this->clientSystems) }}</p>
                <p class="mt-1 text-sm font-medium text-[var(--color-ink-2)]">Total available</p>
                <p class="mt-1 text-xs text-[var(--color-faint)]">Applications you can access</p>
            </div>

            <div class="os-card os-card-pad">
                <div class="mb-4 flex items-center justify-between">
                    <span class="os-icon-tile os-icon-tile-ink">
                        <x-icon name="shield" class="w-5 h-5" />
                    </span>
                </div>
                <p class="text-3xl font-semibold tracking-tight text-[var(--color-ink)]">100%</p>
                <p class="mt-1 text-sm font-medium text-[var(--color-ink-2)]">Secure</p>
                <p class="mt-1 text-xs text-[var(--color-faint)]">Encrypted connections</p>
            </div>

            <div class="os-card os-card-pad">
                <div class="mb-4 flex items-center justify-between">
                    <span class="os-icon-tile os-icon-tile-ink">
                        <x-icon name="clock" class="w-5 h-5" />
                    </span>
                </div>
                <p class="text-3xl font-semibold tracking-tight text-[var(--color-ink)]">{{ now()->format('H:i') }}</p>
                <p class="mt-1 text-sm font-medium text-[var(--color-ink-2)]">Last activity</p>
                <p class="mt-1 text-xs text-[var(--color-faint)]">{{ now()->format('M j, Y') }}</p>
            </div>
        </div>

        @if($loading)
            <div class="os-card os-card-pad mt-10 py-16 text-center">
                <div class="inline-flex flex-col items-center">
                    <div class="h-12 w-12 animate-spin rounded-full border-4 border-[var(--color-line)] border-t-[var(--color-accent)]"></div>
                    <span class="mt-6 text-lg font-semibold text-[var(--color-ink-2)]">Loading your applications...</span>
                    <span class="mt-1 text-sm text-[var(--color-muted)]">Please wait while we fetch your data</span>
                </div>
            </div>
        @else
            <div class="os-card mt-10 overflow-hidden">
                <div class="flex flex-col items-start justify-between gap-4 border-b border-[var(--color-line)] bg-[var(--color-surface-2)] px-6 py-5 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="flex items-center gap-2.5 text-xl font-semibold tracking-tight text-[var(--color-ink)]">
                            <x-icon name="apps" class="w-5 h-5 text-[var(--color-accent)]" />
                            Your applications
                        </h2>
                        <p class="mt-1 text-sm text-[var(--color-muted)]">Click any application to access it instantly with single sign-on.</p>
                    </div>
                    <span class="os-badge">
                        {{ collect($this->clientSystems)->where('is_linked', true)->count() }} of {{ count($this->clientSystems) }} connected
                    </span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 xl:grid-cols-3">
                        @forelse($this->clientSystems as $system)
                            <div wire:key="system-{{ $system['id'] }}-{{ $refreshData }}"
                                 class="os-card os-card-hover overflow-hidden">

                                <div class="p-5">
                                    <div class="mb-5 flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3.5">
                                            <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-[var(--radius-md)] border text-base font-semibold {{ $system['is_linked'] ? 'border-[var(--color-accent-line)] bg-[var(--color-accent)] text-white' : 'border-[var(--color-line)] bg-[var(--color-surface-2)] text-[var(--color-faint)]' }}">
                                                @if($system['is_linked'])
                                                    {{ strtoupper(substr($system['name'], 0, 2)) }}
                                                @else
                                                    <x-icon name="desktop" class="w-5 h-5" />
                                                @endif
                                            </span>
                                            <div class="min-w-0">
                                                <h3 class="text-[15px] font-semibold tracking-tight text-[var(--color-ink)]">
                                                    {{ $system['name'] }}
                                                </h3>
                                                <p class="max-w-[180px] truncate text-sm text-[var(--color-muted)]">
                                                    {{ $system['description'] ?? 'Application system' }}
                                                </p>
                                            </div>
                                        </div>

                                        @if($system['is_linked'])
                                            <span class="os-badge os-badge-accent flex-shrink-0">
                                                <span class="inline-flex h-1.5 w-1.5 rounded-full bg-[var(--color-accent)]"></span>
                                                Live
                                            </span>
                                        @else
                                            <span class="os-badge flex-shrink-0">
                                                Not linked
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-3">
                                        @if($system['is_linked'])
                                            <button wire:click="loginToSystem({{ $system['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    class="os-btn os-btn-primary os-btn-block">
                                                <div wire:loading.remove wire:target="loginToSystem({{ $system['id'] }})" class="flex items-center gap-2 whitespace-nowrap">
                                                    <x-icon name="login" class="w-4 h-4" />
                                                    <span>Launch application</span>
                                                </div>
                                                <div wire:loading wire:target="loginToSystem({{ $system['id'] }})" class="flex items-center gap-2">
                                                    <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                                    <span>Launching...</span>
                                                </div>
                                            </button>

                                            <div class="grid grid-cols-2 gap-2">
                                                <button wire:click="openEditModal({{ $system['id'] }})"
                                                        class="os-btn os-btn-secondary os-btn-block">
                                                    <x-icon name="settings" class="w-4 h-4" />
                                                    Settings
                                                </button>

                                                <div class="os-btn os-btn-secondary os-btn-block cursor-default" title="Connection encrypted">
                                                    <x-icon name="lock" class="w-3.5 h-3.5 text-[var(--color-accent)]" />
                                                    Secure
                                                </div>
                                            </div>
                                        @else
                                            <button wire:click="openEditModal({{ $system['id'] }})"
                                                    class="os-btn os-btn-primary os-btn-block">
                                                <x-icon name="plus" class="w-4 h-4" />
                                                <span>Connect system</span>
                                            </button>
                                            <p class="mt-2 text-center text-xs text-[var(--color-faint)]">
                                                Setup required to access this application
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-[var(--color-line)] bg-[var(--color-surface-2)] px-5 py-3">
                                    <div class="flex items-center gap-2 text-xs text-[var(--color-muted)]">
                                        <span class="font-medium text-[var(--color-ink-2)]">ID:</span>
                                        <span class="rounded border border-[var(--color-line)] bg-[var(--color-surface)] px-1.5 py-0.5 font-mono">{{ substr($system['client_id'], 0, 8) }}</span>
                                    </div>
                                    <div class="text-xs text-[var(--color-faint)]">
                                        {{ $system['is_linked'] ? 'Last used: Today' : 'Never used' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full os-card os-card-pad py-16 text-center">
                                <span class="os-icon-tile os-icon-tile-ink mx-auto mb-5 h-16 w-16">
                                    <x-icon name="desktop" class="w-7 h-7" />
                                </span>
                                <h3 class="text-xl font-semibold tracking-tight text-[var(--color-ink)]">No applications available</h3>
                                <p class="mx-auto mt-2 mb-6 max-w-md text-sm text-[var(--color-muted)]">
                                    It looks like no client systems have been configured yet. Contact your administrator to set up applications.
                                </p>
                                <button wire:click="loadUserDashboard" class="os-btn os-btn-primary">
                                    <x-icon name="refresh" class="w-4 h-4" />
                                    Refresh
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- Help panel --}}
        <div class="os-card os-card-pad mt-10 text-center">
            <span class="os-icon-tile mx-auto mb-4">
                <x-icon name="question" class="w-5 h-5" />
            </span>
            <h3 class="text-lg font-semibold tracking-tight text-[var(--color-ink)]">Need help?</h3>
            <p class="mx-auto mt-2 mb-6 max-w-md text-sm text-[var(--color-muted)]">
                Having trouble connecting to an application or need assistance with your account?
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('user.profile.livewire') }}" class="os-btn os-btn-secondary">
                    <x-icon name="user" class="w-4 h-4" />
                    Profile settings
                </a>
                <a href="/docs" class="os-btn os-btn-primary">
                    <x-icon name="book" class="w-4 h-4" />
                    Documentation
                </a>
            </div>
        </div>

        @include('user.livewire.partials.edit-credentials-modal')

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up Livewire listeners');
    });

    document.addEventListener('livewire:initialized', () => {
        console.log('Livewire initialized, setting up redirect listener');

        Livewire.on('show-popup-blocked', (url) => {
            console.log('Popup was blocked for URL:', url);
            const message = 'Please allow popups for this site to open the client portal in a new tab. You can enable popups in your browser settings.';

            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-yellow-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-sm';
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <div class="font-semibold">Popup Blocked</div>
                        <div class="text-sm">${message}</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 8000);
    });

    Livewire.on('show-popup-error', (message) => {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">×</button>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    });
            Livewire.on('redirect-to-client', (event) => {
                const urlToOpen = event.url || event[0]?.url || event;
                if (!urlToOpen) return;
                window.open(urlToOpen, '_blank', 'noopener,noreferrer');
            });

            Livewire.on('openInNewTab', (url) => {
                if (!url) return;
                const newWindow = window.open(url, '_blank', 'noopener,noreferrer');

                // Check if popup was blocked
                if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                    console.log('Popup blocked detected');
                    const message = 'Popup blocked! Please allow popups for this site to access the application.';
                    Livewire.dispatch('show-popup-blocked', { url: url });
                }
            });

            Livewire.on('show-message', (data) => {
                // Livewire 3 passes named arguments as an object (e.g. data.message, data.type)
                // OR it might pass them as event detail if using traditional event listener.
                // In $this->dispatch('name', param: value), the listener callback receives an object { param: value }.

                const message = data.message || data[0]?.message || 'Operation successful';
                const type = data.type || data[0]?.type || 'success';

                const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';

                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 transition-opacity duration-500`;
                notification.innerHTML = `
                    <div class="flex items-center space-x-2">
                         <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            ${type === 'error'
                                ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
                                : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                            }
                        </svg>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">×</button>
                    </div>
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.style.opacity = '0';
                        setTimeout(() => notification.remove(), 500);
                    }
                }, 5000);
            });
});

window.testRedirect = function(url) {
    window.open(url, '_blank');
};
</script>
@endpush

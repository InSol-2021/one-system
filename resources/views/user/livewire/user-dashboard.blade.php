<div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-50">
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4" role="alert">
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="relative overflow-hidden bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 text-white border-b border-slate-200">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 40px 40px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="flex items-center space-x-6 mb-8 lg:mb-0">


                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-2">
                            Welcome back, {{ $user->first_name ?? 'User' }}!
                        </h1>
                        <p class="text-xl text-slate-200 mb-4">
                            Your secure gateway to all connected applications
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <div class="bg-emerald-500/10 backdrop-blur-sm border border-emerald-400/30 rounded-xl px-4 py-2 flex items-center">
                                <x-icon name="shield" class="w-4 h-4 mr-2 text-emerald-300" />
                                <span class="font-semibold">Secure Access</span>
                            </div>
                            <div class="bg-blue-500/10 backdrop-blur-sm border border-blue-400/30 rounded-xl px-4 py-2 flex items-center">
                                <x-icon name="clock" class="w-4 h-4 mr-2 text-blue-300" />
                                <span class="font-semibold">One-Click Login</span>
                            </div>
                            <div class="bg-amber-500/10 backdrop-blur-sm border border-amber-400/30 rounded-xl px-4 py-2 flex items-center">
                                <x-icon name="lock" class="w-4 h-4 mr-2 text-amber-300" />
                                <span class="font-semibold">Encrypted</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button wire:click="loadUserDashboard" wire:loading.attr="disabled"
                            class="bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center group">
                        <div wire:loading.remove class="flex items-center">
                            <x-icon name="refresh" class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-300" />
                            Refresh
                        </div>
                        <div wire:loading class="flex items-center">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                            Loading...
                        </div>
                    </button>

                    <a href="{{ route('user.profile.livewire') }}"
                       class="bg-white text-slate-700 hover:bg-slate-50 px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center group shadow-lg">
                        <x-icon name="settings" class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform duration-300" />
                        Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-emerald-100 w-14 h-14 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="link" class="w-6 h-6 text-emerald-600" />
                    </div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                </div>
                <p class="text-3xl font-bold text-slate-900 mb-1">{{ collect($this->clientSystems)->where('is_linked', true)->count() }}</p>
                <p class="text-slate-600 font-medium">Connected Systems</p>
                <p class="text-xs text-slate-500 mt-2">Active and ready to use</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 w-14 h-14 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="desktop" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                </div>
                <p class="text-3xl font-bold text-slate-900 mb-1">{{ count($this->clientSystems) }}</p>
                <p class="text-slate-600 font-medium">Total Available</p>
                <p class="text-xs text-slate-500 mt-2">Applications you can access</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-amber-100 w-14 h-14 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="shield" class="w-6 h-6 text-amber-600" />
                    </div>
                    <div class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></div>
                </div>
                <p class="text-3xl font-bold text-slate-900 mb-1">100%</p>
                <p class="text-slate-600 font-medium">Secure</p>
                <p class="text-xs text-slate-500 mt-2">Military-grade encryption</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-slate-100 w-14 h-14 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-icon name="clock" class="w-6 h-6 text-slate-600" />
                    </div>
                    <div class="w-3 h-3 bg-slate-500 rounded-full"></div>
                </div>
                <p class="text-3xl font-bold text-slate-900 mb-1">{{ now()->format('H:i') }}</p>
                <p class="text-slate-600 font-medium">Last Activity</p>
                <p class="text-xs text-slate-500 mt-2">{{ now()->format('M j, Y') }}</p>
            </div>
        </div>

        @if($loading)
            <div class="bg-white rounded-3xl shadow-lg p-16 text-center border border-gray-100">
                <div class="inline-flex flex-col items-center">
                    <div class="relative">
                        <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200"></div>
                        <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent absolute top-0 left-0"></div>
                    </div>
                    <span class="mt-6 text-xl font-semibold text-gray-700">Loading your applications...</span>
                    <span class="mt-2 text-sm text-gray-500">Please wait while we fetch your data</span>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-zinc-50 border-b border-slate-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                                <x-icon name="apps" class="w-6 h-6 text-blue-600 mr-3" />
                                Your Applications
                            </h2>
                            <p class="text-gray-600">Click on any application to access it instantly with single sign-on</p>
                        </div>
                        <div class="mt-4 sm:mt-0 bg-white rounded-xl px-4 py-2 shadow-sm border">
                            <span class="text-sm font-semibold text-gray-700">
                                {{ collect($this->clientSystems)->where('is_linked', true)->count() }} of {{ count($this->clientSystems) }} connected
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                        @forelse($this->clientSystems as $system)
                            <div wire:key="system-{{ $system['id'] }}-{{ $refreshData }}"
                                 class="group relative bg-gradient-to-br from-white to-gray-50 rounded-3xl border-2 border-gray-100 hover:border-blue-300 hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-1">

                                <div class="absolute inset-0 bg-gradient-to-br {{ $system['is_linked'] ? 'from-green-400/5 to-blue-400/5' : 'from-gray-400/5 to-slate-400/5' }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                <div class="absolute top-6 right-6 z-10">
                                    @if($system['is_linked'])
                                        <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold flex items-center shadow-sm">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                            CONNECTED
                                        </div>
                                    @else
                                        <div class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-bold flex items-center shadow-sm">
                                            <div class="w-2 h-2 bg-amber-500 rounded-full mr-2"></div>
                                            SETUP REQUIRED
                                        </div>
                                    @endif
                                </div>

                                <div class="relative p-8 pt-16">
                                    <div class="flex items-center space-x-4 mb-6">
                                        <div class="w-16 h-16 {{ $system['is_linked'] ? 'bg-gradient-to-br from-green-400 to-blue-600' : 'bg-gradient-to-br from-gray-400 to-slate-600' }} rounded-3xl flex items-center justify-center text-white font-bold text-xl shadow-xl group-hover:scale-110 transition-transform duration-300">
                                            {{ strtoupper(substr($system['name'], 0, 2)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xl font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors duration-300">
                                                {{ $system['name'] }}
                                            </h3>
                                            <p class="text-sm text-gray-600 truncate">
                                                {{ $system['description'] ?? 'Application system' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center mb-6 space-x-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $system['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <div class="w-1.5 h-1.5 {{ $system['is_active'] ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-1"></div>
                                            {{ $system['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($system['is_linked'])
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800">
                                                <x-icon name="lock" class="w-3 h-3 mr-1" />
                                                Encrypted
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-3">
                                        @if($system['is_linked'])
                                            <button wire:click="loginToSystem({{ $system['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    class="w-full bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform group-hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center group/btn">
                                                <div wire:loading.remove wire:target="loginToSystem({{ $system['id'] }})" class="flex items-center">
                                                    <x-icon name="login" class="w-5 h-5 mr-3 group-hover/btn:translate-x-1 transition-transform duration-300" />
                                                    <span class="text-lg">Access {{ $system['name'] }}</span>
                                                </div>
                                                <div wire:loading wire:target="loginToSystem({{ $system['id'] }})" class="flex items-center">
                                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                                                    <span>Logging in...</span>
                                                </div>
                                            </button>

                                            <button wire:click="openEditModal({{ $system['id'] }})"
                                                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-2xl transition-all duration-300 flex items-center justify-center group/edit">
                                                <x-icon name="settings" class="w-4 h-4 mr-2 group-hover/edit:rotate-90 transition-transform duration-300" />
                                                Manage Connection
                                            </button>
                                        @else
                                            <button wire:click="openEditModal({{ $system['id'] }})"
                                                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 transform group-hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center group/setup">
                                                <x-icon name="plus" class="w-5 h-5 mr-3 group-hover/setup:rotate-90 transition-transform duration-300" />
                                                <span class="text-lg">Setup Connection</span>
                                            </button>

                                            <div class="text-center">
                                                <p class="text-sm text-gray-500">Connect your credentials to enable one-click access</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-6 pt-4 border-t border-gray-100">
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-500 font-medium">Client ID</p>
                                                <p class="text-gray-900 font-mono text-xs">{{ substr($system['client_id'], 0, 8) }}...</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 font-medium">Last Used</p>
                                                <p class="text-gray-900">{{ $system['is_linked'] ? 'Today' : 'Never' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl p-16 text-center">
                                <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <x-icon name="desktop" class="w-12 h-12 text-gray-400" />
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">No Applications Available</h3>
                                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                                    It looks like no client systems have been configured yet. Contact your administrator to set up applications.
                                </p>
                                <button wire:click="loadUserDashboard" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors duration-300">
                                    <x-icon name="refresh" class="w-4 h-4 mr-2" />
                                    Refresh
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-3xl p-8 border border-blue-100">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="question" class="w-8 h-8 text-blue-600" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Need Help?</h3>
                <p class="text-gray-600 mb-6">
                    Having trouble connecting to an application or need assistance with your account?
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('user.profile.livewire') }}" class="bg-white hover:bg-gray-50 text-blue-600 px-6 py-3 rounded-xl font-semibold transition-colors duration-300 shadow-sm border">
                        <x-icon name="user" class="w-4 h-4 mr-2" />
                        Profile Settings
                    </a>
                    <a href="/docs" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-colors duration-300">
                        <x-icon name="book" class="w-4 h-4 mr-2" />
                        Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('user.livewire.partials.edit-credentials-modal')
</div>
        @if($message)
            <div class="fixed top-4 right-4 z-50 max-w-sm">
                <div class="@if($messageType === 'success') bg-green-100 border border-green-400 text-green-700 @else bg-red-100 border border-red-400 text-red-700 @endif px-4 py-3 rounded-lg shadow-lg">
                    {{ $message }}
                    <button wire:click="clearMessage" class="ml-2 text-sm underline">×</button>
                </div>
            </div>
            <script>
                setTimeout(() => {
                    @this.call('clearMessage');
                }, 5000);
            </script>
        @endif

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
                    console.log('=== REDIRECT EVENT RECEIVED ===');
                    console.log('Full event object:', event);
                    console.log('Event URL:', event.url);
                    console.log('Event type:', typeof event);
                    console.log('Event keys:', Object.keys(event));

                    const urlToOpen = event.url || event[0]?.url || event;
                    console.log('Final URL to open:', urlToOpen);

                    if (!urlToOpen || urlToOpen === 'undefined' || urlToOpen === '') {
                        console.error('Invalid URL for redirect:', urlToOpen);
                        alert('Error: Invalid redirect URL: ' + urlToOpen);
                        return;
                    }

                    console.log('Attempting to open URL:', urlToOpen);
                    const newWindow = window.open(urlToOpen, '_blank', 'noopener,noreferrer');

                    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                        console.error('Popup blocked or failed to open');
                        alert('Popup blocked. Please allow popups and try again.\nURL: ' + urlToOpen);
                    } else {
                        console.log('Successfully opened new window');
                    }
                });

                Livewire.on('openInNewTab', (url) => {
                    console.log('=== OPEN IN NEW TAB EVENT ===');
                    console.log('URL to open:', url);

                    if (!url || url === 'undefined' || url === '') {
                        console.error('Invalid URL for new tab:', url);
                        window.Livewire.dispatch('show-popup-error', 'Invalid login URL');
                        return;
                    }

                    const newWindow = window.open(url, '_blank', 'noopener,noreferrer');

                    setTimeout(() => {
                        if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                            console.error('Popup blocked or failed to open new tab');
                        } else {
                            console.log('Successfully opened new tab');
                        }
                    }, 100);
                });
            });

            window.testRedirect = function(url) {
                console.log('Testing redirect with URL:', url);
                window.open(url, '_blank');
            };
        </script>
    </div>
</div>

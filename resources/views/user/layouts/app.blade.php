<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'User Dashboard - CAS')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak] { display: none !important; }</style>

    @livewireStyles
</head>
<body class="min-h-screen flex flex-col bg-[var(--color-canvas)]">
    <nav class="bg-[var(--color-surface)] border-b border-[var(--color-line)]" x-data="{ mobileOpen: false }">
        <div class="os-container">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/user/dashboard" class="flex items-center gap-2.5">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-ink)] text-white">
                                <i class="fa-solid fa-shield-halved text-sm"></i>
                            </span>
                            <span class="text-base font-semibold tracking-tight text-[var(--color-ink)]">One System</span>
                        </a>
                    </div>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                        <a href="/user/dashboard"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('user/dashboard*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-table-cells-large w-4 text-center"></i>
                            <span>SSO dashboard</span>
                        </a>
                        <a href="/user/profile"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('user/profile*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-gear w-4 text-center"></i>
                            <span>Profile &amp; security</span>
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    @if(session('user_id'))
                        @php
                            $user = \App\Models\User::find(session('user_id'));
                        @endphp
                        @if($user)
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-[var(--color-ink)]">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    <p class="text-xs text-[var(--color-muted)]">{{ $user->email }}</p>
                                </div>
                                <div class="w-9 h-9 bg-[var(--color-accent-soft)] rounded-[var(--radius-sm)] flex items-center justify-center text-[var(--color-accent-strong)] font-semibold text-sm border border-[var(--color-accent-line)]">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="os-btn os-btn-secondary">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Mobile hamburger button -->
                <div class="flex items-center sm:hidden">
                    <button @click="mobileOpen = !mobileOpen" class="inline-flex items-center justify-center p-2 rounded-[var(--radius-sm)] text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:bg-[var(--color-surface-2)] focus:outline-none transition-colors">
                        <i x-show="!mobileOpen" class="fa-solid fa-bars text-lg"></i>
                        <i x-show="mobileOpen" x-cloak class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu panel -->
        <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" x-cloak class="sm:hidden border-t border-[var(--color-line)] bg-[var(--color-surface)]">
            <div class="pt-2 pb-3 space-y-1 px-3">
                <a href="/user/dashboard" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('user/dashboard*') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">SSO dashboard</a>
                <a href="/user/profile" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('user/profile*') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">Profile &amp; security</a>
            </div>
            <div class="pt-3 pb-3 border-t border-[var(--color-line)] px-4">
                @if(session('user_id'))
                    @php
                        $mobileUser = \App\Models\User::find(session('user_id'));
                    @endphp
                    @if($mobileUser)
                        <div class="flex items-center px-3 py-2 mb-2">
                            <div class="w-9 h-9 bg-[var(--color-accent-soft)] rounded-[var(--radius-sm)] flex items-center justify-center text-[var(--color-accent-strong)] font-semibold text-sm border border-[var(--color-accent-line)] mr-3">
                                {{ strtoupper(substr($mobileUser->first_name, 0, 1)) }}{{ strtoupper(substr($mobileUser->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-[var(--color-ink)]">{{ $mobileUser->first_name }} {{ $mobileUser->last_name }}</p>
                                <p class="text-xs text-[var(--color-muted)]">{{ $mobileUser->email }}</p>
                            </div>
                        </div>
                    @endif
                @endif
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium text-[var(--color-danger)] hover:bg-[var(--color-danger-soft)] transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-[var(--color-surface)] border-t border-[var(--color-line)] mt-auto">
        <div class="os-container py-4">
            <div class="flex justify-center items-center text-sm text-[var(--color-muted)]">
                <div>
                    Central Authentication Service — User dashboard
                </div>
            </div>
        </div>
    </footer>


    @livewireScripts
    @stack('scripts')
</body>
</html>

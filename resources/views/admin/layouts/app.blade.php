<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CAS Admin Panel')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        .loading-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }

        .loading-overlay {
            backdrop-filter: blur(2px);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[var(--color-canvas)]">
    <nav class="bg-[var(--color-surface)] border-b border-[var(--color-line)]" x-data="{ mobileOpen: false }">
        <div class="os-container">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/admin/dashboard" class="flex items-center gap-2.5">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-ink)] text-white">
                                <i class="fa-solid fa-shield-halved text-sm"></i>
                            </span>
                            <span class="text-base font-semibold tracking-tight text-[var(--color-ink)]">One System</span>
                        </a>
                    </div>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                        <a href="/admin/dashboard"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('admin/dashboard*') || request()->is('admin') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-gauge-high w-4 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="/admin/client-systems"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('admin/client-systems*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-server w-4 text-center"></i>
                            <span>Client systems</span>
                        </a>
                        <a href="/admin/users"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('admin/users*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-users w-4 text-center"></i>
                            <span>Users</span>
                        </a>
                        <a href="/admin/ip-whitelist"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('admin/ip-whitelist*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-shield-halved w-4 text-center"></i>
                            <span>IP whitelist</span>
                        </a>
                        <a href="/admin/audit-logs"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('admin/audit-logs*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-file-lines w-4 text-center"></i>
                            <span>Audit logs</span>
                        </a>
                        <a href="/admin/sso-settings"
                           class="inline-flex items-center gap-2 border-b-2 px-1 pt-1 text-sm font-medium transition-colors {{ request()->is('admin/sso-settings*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)]' }}">
                            <i class="fa-solid fa-gear w-4 text-center"></i>
                            <span>SSO settings</span>
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-[var(--color-ink-2)] hover:text-[var(--color-ink)] transition-colors focus:outline-none rounded-[var(--radius-sm)] px-2.5 py-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-surface-2)] text-[var(--color-ink-2)] border border-[var(--color-line)]">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </span>
                                <span>Admin User</span>
                                <i class="fa-solid fa-chevron-down text-xs text-[var(--color-faint)]"></i>
                            </button>
                        </div>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" x-cloak class="origin-top-right absolute right-0 mt-2 w-52 rounded-[var(--radius-md)] bg-[var(--color-surface)] border border-[var(--color-line)] shadow-[var(--shadow-md)] z-50">
                            <div class="py-1.5">
                                <a href="/admin/profile" class="flex items-center gap-2.5 px-4 py-2 text-sm text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)] transition-colors">
                                    <i class="fa-solid fa-user w-4 text-center text-[var(--color-muted)]"></i>
                                    My profile
                                </a>
                                <a href="/admin/security-settings" class="flex items-center gap-2.5 px-4 py-2 text-sm text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)] transition-colors">
                                    <i class="fa-solid fa-shield-halved w-4 text-center text-[var(--color-muted)]"></i>
                                    Security settings
                                </a>
                                <div class="my-1 border-t border-[var(--color-line)]"></div>
                                <form method="POST" action="/auth/logout" class="block">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-[var(--color-danger)] hover:bg-[var(--color-danger-soft)] transition-colors">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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
                <a href="/admin/dashboard" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('admin/dashboard*') || request()->is('admin') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">Dashboard</a>
                <a href="/admin/client-systems" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('admin/client-systems*') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">Client systems</a>
                <a href="/admin/users" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('admin/users*') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">Users</a>
                <a href="/admin/ip-whitelist" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('admin/ip-whitelist*') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">IP whitelist</a>
                <a href="/admin/audit-logs" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('admin/audit-logs*') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">Audit logs</a>
                <a href="/admin/sso-settings" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium transition-colors {{ request()->is('admin/sso-settings*') ? 'bg-[var(--color-accent-soft)] text-[var(--color-accent)]' : 'text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)]' }}">SSO settings</a>
            </div>
            <div class="pt-3 pb-3 border-t border-[var(--color-line)] px-4 space-y-1">
                <a href="/admin/profile" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)] transition-colors">My profile</a>
                <a href="/admin/security-settings" class="block px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-ink)] transition-colors">Security settings</a>
                <form method="POST" action="/auth/logout" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-[var(--radius-sm)] text-base font-medium text-[var(--color-danger)] hover:bg-[var(--color-danger-soft)] transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div id="page-loading" class="hidden">
        <x-page-loading title="Loading Page">Preparing your admin dashboard...</x-page-loading>
    </div>

    <main class="flex-1 relative">
        @yield('content')
    </main>

    <footer class="bg-[var(--color-surface)] border-t border-[var(--color-line)] mt-auto">
        <div class="os-container py-4">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2 text-sm text-[var(--color-muted)]">
                <div>
                    CAS admin panel — Centralized Authentication System
                </div>
                <div class="flex items-center gap-3">
                    <span>Laravel {{ app()->version() }}</span>
                    <span class="text-[var(--color-faint)]">•</span>
                    <span>Livewire enabled</span>
                </div>
            </div>
        </div>
    </footer>


    @livewireScripts

    <script>
        function showPageLoading() {
            const loading = document.getElementById('page-loading');
            if (loading) {
                loading.classList.remove('hidden');
            }
        }

        function hidePageLoading() {
            const loading = document.getElementById('page-loading');
            if (loading) {
                loading.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('nav a[href^="/admin"]');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    showPageLoading();

                    setTimeout(() => {
                        hidePageLoading();
                    }, 5000);
                });
            });

            window.addEventListener('livewire:navigating', () => {
                showPageLoading();
            });

            window.addEventListener('livewire:navigated', () => {
                hidePageLoading();
            });

            window.addEventListener('load', () => {
                hidePageLoading();
            });

            window.addEventListener('error', function(e) {
                if (e.filename && (e.filename.includes('share-modal.js') ||
                    e.filename.includes('extension'))) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
</body>
</html>

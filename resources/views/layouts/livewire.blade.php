<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')  — One System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        .animate-pulse-subtle {
            animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: var(--color-surface-2);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--color-line-strong);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--color-faint);
        }
    </style>

    @stack('head')
</head>
<body class="min-h-screen antialiased bg-[var(--color-canvas)]">
    <div class="flex min-h-screen flex-col">
        <nav class="sticky top-0 z-40 border-b border-[var(--color-line)] bg-[var(--color-surface)]">
            <div class="os-container">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <a href="/" class="flex flex-shrink-0 items-center gap-2.5">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-ink)] text-white">
                                <i class="fa-solid fa-shield-halved text-sm"></i>
                            </span>
                            <span class="text-[0.95rem] font-semibold tracking-tight text-[var(--color-ink)]">CAS Admin</span>
                        </a>
                        <div class="hidden sm:flex sm:items-center sm:gap-1">
                            <a href="/admin/client-systems"
                               class="inline-flex items-center border-b-2 px-3 pt-px text-sm font-medium leading-[3.75rem] transition-colors {{ request()->is('admin/client-systems*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:border-[var(--color-line-strong)]' }}">
                                Client systems
                            </a>
                            <a href="/admin/users"
                               class="inline-flex items-center border-b-2 px-3 pt-px text-sm font-medium leading-[3.75rem] transition-colors {{ request()->is('admin/users*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:border-[var(--color-line-strong)]' }}">
                                Users
                            </a>
                            <a href="/admin/audit-logs"
                               class="inline-flex items-center border-b-2 px-3 pt-px text-sm font-medium leading-[3.75rem] transition-colors {{ request()->is('admin/audit-logs*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:border-[var(--color-line-strong)]' }}">
                                Audit logs
                            </a>
                            <a href="/admin/ip-whitelist"
                               class="inline-flex items-center border-b-2 px-3 pt-px text-sm font-medium leading-[3.75rem] transition-colors {{ request()->is('admin/ip-whitelist*') ? 'border-[var(--color-accent)] text-[var(--color-accent)]' : 'border-transparent text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:border-[var(--color-line-strong)]' }}">
                                IP whitelist
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center">
                        <div class="flex items-center gap-2 text-sm text-[var(--color-muted)]">
                            <i class="fa-regular fa-circle-user text-base text-[var(--color-faint)]"></i>
                            Admin user
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="mt-auto border-t border-[var(--color-line)] bg-[var(--color-surface)]">
            <div class="os-container py-4">
                <div class="flex flex-col items-start justify-between gap-2 text-sm text-[var(--color-muted)] sm:flex-row sm:items-center">
                    <div>
                        Central Authentication Service (CAS) admin panel
                    </div>
                    <div class="flex items-center gap-3 text-[var(--color-faint)]">
                        <span>Laravel {{ app()->version() }}</span>
                        <span aria-hidden="true">&middot;</span>
                        <span>Livewire {{ \Livewire\Livewire::getVersion() }}</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts

    <script>
        window.showNotification = function(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 flex items-center gap-2 rounded-[var(--radius-sm)] border px-4 py-3 text-sm font-medium shadow-[var(--shadow-md)] transform transition-transform duration-300 ${
                type === 'success'
                    ? 'bg-[var(--color-success-soft)] border-[#bbf7d0] text-[var(--color-success)]'
                    : 'bg-[var(--color-danger-soft)] border-[#fecaca] text-[var(--color-danger)]'
            }`;
            notification.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(120%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        };

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notification', (event) => {
                showNotification(event.message, event.type);
            });
        });

        document.addEventListener('livewire:navigating', () => {
            const loader = document.getElementById('global-loader');
            if (loader) loader.classList.remove('hidden');
        });

        document.addEventListener('livewire:navigated', () => {
            const loader = document.getElementById('global-loader');
            if (loader) loader.classList.add('hidden');
        });
    </script>
</body>
</html>

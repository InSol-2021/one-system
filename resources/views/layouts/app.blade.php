<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'One System') }} — @yield('title', 'Central Authentication Service')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen antialiased">
    <div id="app" class="flex min-h-screen flex-col">
        @hasSection('navigation')
            @yield('navigation')
        @else
            <header class="sticky top-0 z-40 border-b border-[var(--color-line)] bg-[color-mix(in_srgb,var(--color-surface)_88%,transparent)] backdrop-blur">
                <nav class="os-container flex h-16 items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-ink)] text-white">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </span>
                        <span class="text-[0.95rem] font-semibold tracking-tight">One System</span>
                    </a>
                    <div class="flex items-center gap-1 sm:gap-2">
                        <a href="{{ route('docs') }}" class="os-btn os-btn-ghost hidden sm:inline-flex">Documentation</a>
                        <a href="{{ route('user.dashboard') }}" class="os-btn os-btn-ghost hidden sm:inline-flex">Dashboard</a>
                        <a href="{{ route('login') }}" class="os-btn os-btn-primary">Sign in</a>
                    </div>
                </nav>
            </header>
        @endif

        <main class="flex-1">
            @if (session('success') || session('error') || $errors->any())
                <div class="os-container pt-5">
                    @if (session('success'))
                        <div class="os-alert os-alert-success mb-3"><i class="fa-solid fa-circle-check mt-0.5"></i><span>{{ session('success') }}</span></div>
                    @endif
                    @if (session('error'))
                        <div class="os-alert os-alert-danger mb-3"><i class="fa-solid fa-circle-exclamation mt-0.5"></i><span>{{ session('error') }}</span></div>
                    @endif
                    @if ($errors->any())
                        <div class="os-alert os-alert-danger mb-3">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <ul class="list-inside list-disc space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>

        @hasSection('footer')
            @yield('footer')
        @else
            <footer class="border-t border-[var(--color-line)] bg-[var(--color-surface)]">
                <div class="os-container py-12">
                    <div class="grid grid-cols-1 gap-10 md:grid-cols-[1.4fr_1fr_1fr]">
                        <div>
                            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-ink)] text-white">
                                    <i class="fa-solid fa-shield-halved text-sm"></i>
                                </span>
                                <span class="text-[0.95rem] font-semibold tracking-tight">One System</span>
                            </a>
                            <p class="mt-3 max-w-xs text-sm text-[var(--color-muted)]">
                                Central Authentication Service for secure single sign-on across your connected applications.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">Product</h3>
                            <ul class="mt-4 space-y-2.5 text-sm text-[var(--color-ink-2)]">
                                <li><a href="{{ route('user.dashboard') }}" class="hover:text-[var(--color-accent)]">User Dashboard</a></li>
                                <li><a href="{{ route('docs') }}" class="hover:text-[var(--color-accent)]">Documentation</a></li>
                                <li><a href="/health" class="hover:text-[var(--color-accent)]">System Health</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">Capabilities</h3>
                            <ul class="mt-4 space-y-2.5 text-sm text-[var(--color-ink-2)]">
                                <li>Single sign-on</li>
                                <li>Two-factor authentication</li>
                                <li>Audit &amp; monitoring</li>
                                <li>Container-ready deployment</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-10 border-t border-[var(--color-line)] pt-6 text-sm text-[var(--color-faint)]">
                        &copy; {{ date('Y') }} One System. Enterprise authentication.
                    </div>
                </div>
            </footer>
        @endif
    </div>

    @stack('scripts')

    @livewireScripts
</body>
</html>

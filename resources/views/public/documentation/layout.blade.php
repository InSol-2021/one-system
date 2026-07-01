<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Documentation') — One System</title>
    <meta name="description" content="@yield('description', 'Documentation for the One System single sign-on authentication platform')">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Documentation shell — One System */
        .docs-sidebar {
            width: 280px;
            background-color: var(--color-surface);
            border-right: 1px solid var(--color-line);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 30;
            overflow-y: auto;
        }

        .docs-content {
            margin-left: 280px;
            min-height: 100vh;
            background-color: var(--color-canvas);
        }

        .sidebar-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-line);
            background-color: var(--color-surface);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .sidebar-section {
            padding: 1rem 0;
            border-bottom: 1px solid var(--color-line);
        }

        .sidebar-section:last-child {
            border-bottom: none;
        }

        .sidebar-title {
            padding: 0 1.5rem 0.5rem 1.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--color-faint);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.55rem 1.5rem;
            color: var(--color-ink-2);
            text-decoration: none;
            font-size: 0.875rem;
            border-left: 2px solid transparent;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .sidebar-link:hover {
            background-color: var(--color-surface-2);
            color: var(--color-ink);
        }

        .sidebar-link.active {
            background-color: var(--color-accent-soft);
            color: var(--color-accent-strong);
            border-left-color: var(--color-accent);
            font-weight: 500;
        }

        .sidebar-icon {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
            color: var(--color-muted);
        }

        .sidebar-link:hover .sidebar-icon,
        .sidebar-link.active .sidebar-icon {
            color: inherit;
        }

        /* Sidebar badges — monochrome, accent for emphasis only */
        .sidebar-badge {
            display: inline-flex;
            align-items: center;
            margin-left: auto;
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            background-color: var(--color-surface-2);
            border: 1px solid var(--color-line);
            color: var(--color-muted);
        }

        .sidebar-badge.is-accent {
            background-color: var(--color-accent-soft);
            border-color: var(--color-accent-line);
            color: var(--color-accent-strong);
        }

        .mobile-menu-btn {
            display: none;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .docs-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .docs-sidebar.mobile-open {
                transform: translateX(0);
            }

            .docs-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 40;
                width: 2.75rem;
                height: 2.75rem;
                background-color: var(--color-accent);
                color: #fff;
                border: 1px solid var(--color-accent-strong);
                border-radius: var(--radius-sm);
                box-shadow: var(--shadow-sm);
                cursor: pointer;
            }
        }
    </style>
</head>
<body class="bg-[var(--color-canvas)] text-[var(--color-ink)]">
    <!-- Mobile menu button -->
    <button type="button" class="mobile-menu-btn" onclick="toggleMobileSidebar()" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Documentation sidebar -->
    <nav class="docs-sidebar" id="docs-sidebar">
        <!-- Header -->
        <div class="sidebar-header">
            <a href="{{ route('docs') }}" class="flex items-center gap-3">
                <span class="os-icon-tile os-icon-tile-ink" style="width:2.25rem;height:2.25rem;font-size:0.95rem;">
                    <i class="fas fa-shield-halved"></i>
                </span>
                <span>
                    <span class="block font-semibold text-[var(--color-ink)] leading-tight">One System</span>
                    <span class="block text-xs text-[var(--color-muted)]">Documentation</span>
                </span>
            </a>
        </div>

        <!-- Documentation -->
        <div class="sidebar-section">
            <div class="sidebar-title">Documentation</div>
            <a href="{{ route('docs') }}" class="sidebar-link {{ request()->routeIs('docs') ? 'active' : '' }}">
                <i class="fas fa-house sidebar-icon"></i>
                1. Overview
            </a>
            <a href="{{ route('docs.architecture') }}" class="sidebar-link {{ request()->routeIs('docs.architecture') ? 'active' : '' }}">
                <i class="fas fa-sitemap sidebar-icon"></i>
                2. Architecture
            </a>
            <a href="{{ route('docs.security') }}" class="sidebar-link {{ request()->routeIs('docs.security') ? 'active' : '' }}">
                <i class="fas fa-shield-halved sidebar-icon"></i>
                3. Security features
            </a>
            <a href="{{ route('docs.quick-start') }}" class="sidebar-link {{ request()->routeIs('docs.quick-start') ? 'active' : '' }}">
                <i class="fas fa-rocket sidebar-icon"></i>
                4. Deploy locally (dev)
            </a>
            <a href="{{ route('docs.deployment') }}" class="sidebar-link {{ request()->routeIs('docs.deployment') ? 'active' : '' }}">
                <i class="fas fa-server sidebar-icon"></i>
                5. Deploy to Linux (prod)
            </a>
        </div>

        <!-- Integration guides -->
        <div class="sidebar-section">
            <div class="sidebar-title">6. Integration guides</div>
            <a href="{{ route('docs.sdks') }}" class="sidebar-link {{ request()->routeIs('docs.sdks') ? 'active' : '' }}">
                <i class="fas fa-cube sidebar-icon"></i>
                SDKs &amp; packages
            </a>
            <a href="{{ route('docs.laravel') }}" class="sidebar-link {{ request()->routeIs('docs.laravel') ? 'active' : '' }}">
                <i class="fab fa-laravel sidebar-icon"></i>
                Laravel
                <span class="sidebar-badge is-accent">Popular</span>
            </a>
            <a href="{{ route('docs.nodejs') }}" class="sidebar-link {{ request()->routeIs('docs.nodejs') ? 'active' : '' }}">
                <i class="fab fa-node-js sidebar-icon"></i>
                Node.js
            </a>
            <a href="{{ route('docs.javascript') }}" class="sidebar-link {{ request()->routeIs('docs.javascript') ? 'active' : '' }}">
                <i class="fab fa-js sidebar-icon"></i>
                JavaScript
            </a>
            <a href="{{ route('docs.react') }}" class="sidebar-link {{ request()->routeIs('docs.react') ? 'active' : '' }}">
                <i class="fab fa-react sidebar-icon"></i>
                React
                <span class="sidebar-badge">New</span>
            </a>
            <a href="{{ route('docs.nextjs') }}" class="sidebar-link {{ request()->routeIs('docs.nextjs') ? 'active' : '' }}">
                <i class="fas fa-n sidebar-icon"></i>
                Next.js
                <span class="sidebar-badge">New</span>
            </a>
            <a href="{{ route('docs.angular') }}" class="sidebar-link {{ request()->routeIs('docs.angular') ? 'active' : '' }}">
                <i class="fab fa-angular sidebar-icon"></i>
                Angular
                <span class="sidebar-badge">New</span>
            </a>
            <a href="{{ route('docs.vue') }}" class="sidebar-link {{ request()->routeIs('docs.vue') ? 'active' : '' }}">
                <i class="fab fa-vuejs sidebar-icon"></i>
                Vue
                <span class="sidebar-badge">New</span>
            </a>
            <a href="{{ route('docs.python') }}" class="sidebar-link {{ request()->routeIs('docs.python') ? 'active' : '' }}">
                <i class="fab fa-python sidebar-icon"></i>
                Python
            </a>
            <a href="{{ route('docs.java') }}" class="sidebar-link {{ request()->routeIs('docs.java') ? 'active' : '' }}">
                <i class="fab fa-java sidebar-icon"></i>
                Java
            </a>
            <a href="{{ route('docs.dotnet') }}" class="sidebar-link {{ request()->routeIs('docs.dotnet') ? 'active' : '' }}">
                <i class="fab fa-microsoft sidebar-icon"></i>
                .NET / C#
            </a>
            <a href="{{ route('docs.rust') }}" class="sidebar-link {{ request()->routeIs('docs.rust') ? 'active' : '' }}">
                <i class="fab fa-rust sidebar-icon"></i>
                Rust
                <span class="sidebar-badge">New</span>
            </a>
            <a href="{{ route('docs.api.overview') }}" class="sidebar-link {{ request()->routeIs('docs.api.overview') ? 'active' : '' }}">
                <i class="fas fa-code sidebar-icon"></i>
                API reference
            </a>
        </div>

        <!-- User guides -->
        <div class="sidebar-section">
            <div class="sidebar-title">User guides</div>
            <a href="{{ route('docs.admin-panel') }}" class="sidebar-link {{ request()->routeIs('docs.admin-panel') ? 'active' : '' }}">
                <i class="fas fa-gauge-high sidebar-icon"></i>
                7. Admin guide
            </a>
            <a href="{{ route('docs.user-guide') }}" class="sidebar-link {{ request()->routeIs('docs.user-guide') ? 'active' : '' }}">
                <i class="fas fa-user sidebar-icon"></i>
                8. User guide
            </a>
        </div>

        <!-- Reference -->
        <div class="sidebar-section">
            <div class="sidebar-title">Reference</div>
            <a href="{{ route('docs.security-guide') }}" class="sidebar-link {{ request()->routeIs('docs.security-guide') ? 'active' : '' }}">
                <i class="fas fa-lock sidebar-icon"></i>
                9. Security guide
            </a>
            <a href="{{ route('docs.troubleshooting') }}" class="sidebar-link {{ request()->routeIs('docs.troubleshooting') ? 'active' : '' }}">
                <i class="fas fa-screwdriver-wrench sidebar-icon"></i>
                10. Troubleshooting
            </a>
            <a href="{{ route('docs.examples') }}" class="sidebar-link {{ request()->routeIs('docs.examples') ? 'active' : '' }}">
                <i class="fas fa-book-open sidebar-icon"></i>
                11. Code examples
            </a>
        </div>

        <!-- Resources -->
        <div class="sidebar-section">
            <div class="sidebar-title">Resources</div>
            <a href="{{ route('docs.webhooks') }}" class="sidebar-link {{ request()->routeIs('docs.webhooks') ? 'active' : '' }}">
                <i class="fas fa-bolt sidebar-icon"></i>
                Webhooks
            </a>
            <a href="{{ route('docs.changelog') }}" class="sidebar-link {{ request()->routeIs('docs.changelog') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left sidebar-icon"></i>
                Changelog
            </a>
            <a href="{{ route('downloads.laravel-package') }}" class="sidebar-link">
                <i class="fas fa-download sidebar-icon"></i>
                Download packages
            </a>
            <a href="{{ url('/') }}" class="sidebar-link">
                <i class="fas fa-arrow-left sidebar-icon"></i>
                Back to One System
            </a>
        </div>
    </nav>

    <!-- Main content -->
    <main class="docs-content">
        <!-- Mobile overlay -->
        <div class="md:hidden fixed inset-0 bg-[var(--color-ink)]/40 z-20 hidden" id="mobile-overlay" onclick="toggleMobileSidebar()"></div>

        <!-- Page header (if needed) -->
        @hasSection('page-header')
            <div class="bg-[var(--color-surface)] border-b border-[var(--color-line)] px-6 py-4">
                @yield('page-header')
            </div>
        @endif

        <!-- Content -->
        <div class="p-6">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="border-t border-[var(--color-line)] bg-[var(--color-surface)] px-6 py-10 mt-12">
            <div class="">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="font-semibold text-[var(--color-ink)] mb-4">Documentation</h3>
                        <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                            <li><a href="{{ route('docs') }}" class="hover:text-[var(--color-accent)]">Getting started</a></li>
                            <li><a href="{{ route('docs.api.overview') }}" class="hover:text-[var(--color-accent)]">API reference</a></li>
                            <li><a href="{{ route('docs.security') }}" class="hover:text-[var(--color-accent)]">Security guide</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[var(--color-ink)] mb-4">Popular integrations</h3>
                        <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                            <li><a href="{{ route('docs.laravel') }}" class="hover:text-[var(--color-accent)]">Laravel package</a></li>
                            <li><a href="{{ route('docs.nodejs') }}" class="hover:text-[var(--color-accent)]">Node.js SDK</a></li>
                            <li><a href="{{ route('docs.python') }}" class="hover:text-[var(--color-accent)]">Python library</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[var(--color-ink)] mb-4">Support</h3>
                        <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                            <li><a href="{{ route('docs.troubleshooting') }}" class="hover:text-[var(--color-accent)]">Troubleshooting</a></li>
                            <li><a href="{{ route('docs.examples') }}" class="hover:text-[var(--color-accent)]">Code examples</a></li>
                            <li><a href="{{ url('/') }}" class="hover:text-[var(--color-accent)]">One System</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-[var(--color-line)] mt-8 pt-8 text-center text-sm text-[var(--color-muted)]">
                    <p>&copy; {{ date('Y') }} One System. Enterprise-grade single sign-on with comprehensive documentation.</p>
                </div>
            </div>
        </footer>
    </main>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('docs-sidebar');
            const overlay = document.getElementById('mobile-overlay');

            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('hidden');
        }

        // Close mobile sidebar when clicking outside of it
        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('docs-sidebar');
                const overlay = document.getElementById('mobile-overlay');
                const menuBtn = event.target.closest('.mobile-menu-btn');

                if (!sidebar.contains(event.target) && !menuBtn) {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.add('hidden');
                }
            }
        });

        // Reset sidebar state on resize to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('docs-sidebar');
                const overlay = document.getElementById('mobile-overlay');

                sidebar.classList.remove('mobile-open');
                overlay.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

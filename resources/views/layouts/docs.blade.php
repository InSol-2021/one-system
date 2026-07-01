<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'One System documentation')</title>
    <meta name="description" content="@yield('description', 'Complete integration guide for the One System single sign-on authentication platform')">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Prism syntax highlighting -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-csharp.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-java.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>

    <style>
        .docs-topnav-link {
            color: var(--color-ink-2);
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .docs-topnav-link:hover { color: var(--color-accent); }

        .docs-side-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.85rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            color: var(--color-ink-2);
            text-decoration: none;
            border: 1px solid transparent;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }
        .docs-side-link:hover {
            background-color: var(--color-surface-2);
            color: var(--color-ink);
        }
        .docs-side-link.active {
            background-color: var(--color-accent-soft);
            color: var(--color-accent-strong);
            border-color: var(--color-accent-line);
            font-weight: 500;
        }
        .docs-side-icon {
            width: 18px;
            margin-right: 0.6rem;
            text-align: center;
            color: var(--color-muted);
        }
        .docs-side-link:hover .docs-side-icon,
        .docs-side-link.active .docs-side-icon { color: inherit; }
    </style>
</head>
<body class="bg-[var(--color-canvas)] text-[var(--color-ink)]">
    <nav class="bg-[var(--color-surface)] border-b border-[var(--color-line)] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('docs') }}" class="flex items-center gap-3">
                        <span class="os-icon-tile os-icon-tile-ink" style="width:2.25rem;height:2.25rem;font-size:0.95rem;">
                            <i class="fas fa-shield-halved"></i>
                        </span>
                        <span class="text-lg font-semibold text-[var(--color-ink)]">One System docs</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="{{ route('docs') }}" class="docs-topnav-link">
                        <i class="fas fa-house mr-2"></i>Home
                    </a>
                    <a href="{{ route('docs.api.overview') }}" class="docs-topnav-link">
                        <i class="fas fa-code mr-2"></i>API reference
                    </a>
                    <a href="{{ route('docs.examples') }}" class="docs-topnav-link">
                        <i class="fas fa-book-open mr-2"></i>Examples
                    </a>
                    <a href="/" class="os-btn os-btn-secondary">
                        <i class="fas fa-arrow-left"></i>Back to One System
                    </a>
                </div>

                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="os-btn os-btn-ghost" aria-label="Toggle menu">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden border-t border-[var(--color-line)] bg-[var(--color-surface)]">
            <div class="px-3 py-3 space-y-1">
                <a href="{{ route('docs') }}" class="block px-3 py-2 rounded-[var(--radius-sm)] text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)]">
                    <i class="fas fa-house mr-2"></i>Home
                </a>
                <a href="{{ route('docs.api.overview') }}" class="block px-3 py-2 rounded-[var(--radius-sm)] text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)]">
                    <i class="fas fa-code mr-2"></i>API reference
                </a>
                <a href="{{ route('docs.examples') }}" class="block px-3 py-2 rounded-[var(--radius-sm)] text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)]">
                    <i class="fas fa-book-open mr-2"></i>Examples
                </a>
                <a href="/" class="block px-3 py-2 rounded-[var(--radius-sm)] text-[var(--color-ink-2)] hover:bg-[var(--color-surface-2)]">
                    <i class="fas fa-arrow-left mr-2"></i>Back to One System
                </a>
            </div>
        </div>
    </nav>

    <div class="flex">
        @if(Request::is('docs/*') && !Request::is('docs'))
        <aside class="w-64 bg-[var(--color-surface)] border-r border-[var(--color-line)] min-h-screen sticky top-16 overflow-y-auto">
            <div class="p-6">
                <h3 class="os-eyebrow mb-4">
                    <i class="fas fa-book"></i>Integration guides
                </h3>
                <nav class="space-y-1">
                    <a href="{{ route('docs.laravel') }}" class="docs-side-link {{ Request::is('docs/laravel') ? 'active' : '' }}">
                        <i class="fab fa-laravel docs-side-icon"></i>Laravel
                    </a>
                    <a href="{{ route('docs.dotnet') }}" class="docs-side-link {{ Request::is('docs/dotnet') ? 'active' : '' }}">
                        <i class="fab fa-microsoft docs-side-icon"></i>.NET MVC / C#
                    </a>
                    <a href="{{ route('docs.nodejs') }}" class="docs-side-link {{ Request::is('docs/nodejs') ? 'active' : '' }}">
                        <i class="fab fa-node-js docs-side-icon"></i>Node.js
                    </a>
                    <a href="{{ route('docs.java') }}" class="docs-side-link {{ Request::is('docs/java') ? 'active' : '' }}">
                        <i class="fab fa-java docs-side-icon"></i>Java Spring
                    </a>
                    <a href="{{ route('docs.python') }}" class="docs-side-link {{ Request::is('docs/python') ? 'active' : '' }}">
                        <i class="fab fa-python docs-side-icon"></i>Python Django
                    </a>
                    <a href="{{ route('docs.javascript') }}" class="docs-side-link {{ Request::is('docs/javascript') ? 'active' : '' }}">
                        <i class="fab fa-js docs-side-icon"></i>JavaScript / HTML
                    </a>
                    <a href="{{ route('docs.react') }}" class="docs-side-link {{ Request::is('docs/react') ? 'active' : '' }}">
                        <i class="fab fa-react docs-side-icon"></i>React
                    </a>
                    <a href="{{ route('docs.nextjs') }}" class="docs-side-link {{ Request::is('docs/nextjs') ? 'active' : '' }}">
                        <i class="fas fa-n docs-side-icon"></i>Next.js
                    </a>
                    <a href="{{ route('docs.angular') }}" class="docs-side-link {{ Request::is('docs/angular') ? 'active' : '' }}">
                        <i class="fab fa-angular docs-side-icon"></i>Angular
                    </a>
                    <a href="{{ route('docs.vue') }}" class="docs-side-link {{ Request::is('docs/vue') ? 'active' : '' }}">
                        <i class="fab fa-vuejs docs-side-icon"></i>Vue
                    </a>
                </nav>
            </div>
        </aside>
        @endif

        <main class="flex-1 @if(Request::is('docs/*') && !Request::is('docs')) ml-0 @endif">
            @yield('content')
        </main>
    </div>

    <footer class="bg-[var(--color-surface)] border-t border-[var(--color-line)] py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="flex items-center gap-2 text-base font-semibold text-[var(--color-ink)] mb-4">
                        <i class="fas fa-shield-halved text-[var(--color-accent)]"></i>One System
                    </h4>
                    <p class="text-sm text-[var(--color-muted)]">
                        Enterprise-grade single sign-on authentication for secure multi-platform integration.
                    </p>
                </div>

                <div>
                    <h4 class="text-base font-semibold text-[var(--color-ink)] mb-4">Documentation</h4>
                    <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                        <li><a href="{{ route('docs.laravel') }}" class="hover:text-[var(--color-accent)]">Laravel</a></li>
                        <li><a href="{{ route('docs.dotnet') }}" class="hover:text-[var(--color-accent)]">.NET MVC</a></li>
                        <li><a href="{{ route('docs.nodejs') }}" class="hover:text-[var(--color-accent)]">Node.js</a></li>
                        <li><a href="{{ route('docs.java') }}" class="hover:text-[var(--color-accent)]">Java Spring</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-base font-semibold text-[var(--color-ink)] mb-4">Resources</h4>
                    <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                        <li><a href="{{ route('docs.api.overview') }}" class="hover:text-[var(--color-accent)]">API reference</a></li>
                        <li><a href="{{ route('docs.examples') }}" class="hover:text-[var(--color-accent)]">Examples</a></li>
                        <li><a href="/" class="hover:text-[var(--color-accent)]">One System dashboard</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-base font-semibold text-[var(--color-ink)] mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                        <li><a href="{{ route('docs.troubleshooting') }}" class="hover:text-[var(--color-accent)]">Troubleshooting</a></li>
                        <li><a href="{{ route('docs.changelog') }}" class="hover:text-[var(--color-accent)]">Changelog</a></li>
                        <li><a href="{{ route('docs.webhooks') }}" class="hover:text-[var(--color-accent)]">Webhooks</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-[var(--color-line)] text-center text-sm text-[var(--color-muted)]">
                <p>&copy; {{ date('Y') }} One System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const codeBlocks = document.querySelectorAll('pre[class*="language-"]');
            codeBlocks.forEach(block => {
                const copyBtn = document.createElement('button');
                copyBtn.type = 'button';
                copyBtn.innerHTML = '<i class="fas fa-copy"></i>';
                copyBtn.className = 'absolute top-2 right-2 px-2 py-1 rounded text-xs text-white bg-[var(--color-ink-2)] hover:bg-[var(--color-ink)]';
                block.style.position = 'relative';
                block.appendChild(copyBtn);

                copyBtn.addEventListener('click', function () {
                    const code = block.querySelector('code');
                    navigator.clipboard.writeText(code.textContent);
                    copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        copyBtn.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>

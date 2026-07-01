<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in — One System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! RecaptchaV3::initJs() !!}
</head>
<body class="min-h-screen">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Brand panel (neutral ink, no gradients) --}}
        <aside class="relative hidden flex-col justify-between bg-[var(--color-ink)] p-12 text-white lg:flex">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-[var(--radius-sm)] bg-white/10">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <span class="text-base font-semibold tracking-tight">One System</span>
            </a>

            <div class="max-w-md">
                <h1 class="text-3xl font-semibold leading-tight tracking-tight">Central Authentication Service</h1>
                <p class="mt-4 text-[15px] leading-relaxed text-white/70">
                    Secure single sign-on across every connected application, with two-factor protection and a complete audit trail.
                </p>

                <ul class="mt-10 space-y-5">
                    @foreach ([
                        ['fa-shield-halved', 'Enterprise security', 'JWT tokens with configurable expiry'],
                        ['fa-bolt', 'Instant access', 'One login for all connected systems'],
                        ['fa-list-check', 'Full audit trail', 'Every authentication event is recorded'],
                    ] as $item)
                        <li class="flex items-start gap-3.5">
                            <span class="mt-0.5 inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-[var(--radius-sm)] bg-white/10 text-white/90">
                                <i class="fa-solid {{ $item[0] }} text-sm"></i>
                            </span>
                            <div>
                                <div class="text-sm font-medium text-white">{{ $item[1] }}</div>
                                <div class="text-[13px] text-white/55">{{ $item[2] }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="text-xs text-white/40">&copy; {{ date('Y') }} One System &middot; v1.0</p>
        </aside>

        {{-- Form panel --}}
        <main class="flex items-center justify-center px-5 py-12 sm:px-8">
            <div class="w-full max-w-sm">
                <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-ink)] text-white">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </span>
                    <span class="text-[0.95rem] font-semibold tracking-tight">One System</span>
                </a>

                <h2 class="text-2xl font-semibold tracking-tight">Sign in</h2>
                <p class="mt-1.5 text-sm text-[var(--color-muted)]">Enter your credentials to continue.</p>

                @if (session('error'))
                    <div class="os-alert os-alert-danger mt-6"><i class="fa-solid fa-circle-exclamation mt-0.5"></i><span>{{ session('error') }}</span></div>
                @endif
                @if (session('message'))
                    <div class="os-alert os-alert-success mt-6"><i class="fa-solid fa-circle-check mt-0.5"></i><span>{{ session('message') }}</span></div>
                @endif
                @if ($errors->any())
                    <div class="os-alert os-alert-danger mt-6">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <div>@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" id="login-form" class="mt-7 space-y-5">
                    @csrf
                    {!! RecaptchaV3::field('login') !!}

                    <div>
                        <label for="login" class="os-label">Username or email</label>
                        <input id="login" name="login" type="text" required autofocus
                               class="os-input" placeholder="you@example.com" value="{{ old('login') }}">
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="os-label">Password</label>
                            <a href="{{ route('password.request') }}" class="mb-1.5 text-xs font-medium text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                   class="os-input pr-11" placeholder="Enter your password">
                            <button type="button" id="toggle-password"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-[var(--color-faint)] hover:text-[var(--color-ink-2)]"
                                    aria-label="Show password">
                                <i class="fa-regular fa-eye" id="toggle-password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-[var(--color-ink-2)]">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-[var(--color-line-strong)] text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                        Remember me
                    </label>

                    <button type="submit" class="os-btn os-btn-primary os-btn-block os-btn-lg">Sign in</button>
                </form>

                <p class="mt-5 text-center text-xs text-[var(--color-faint)]">
                    Protected by reCAPTCHA ·
                    <a href="https://policies.google.com/privacy" class="hover:text-[var(--color-muted)]">Privacy</a> ·
                    <a href="https://policies.google.com/terms" class="hover:text-[var(--color-muted)]">Terms</a>
                </p>

                <div class="mt-6 border-t border-[var(--color-line)] pt-6 text-center">
                    <a href="{{ route('docs') }}" class="inline-flex items-center gap-1.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-accent)]">
                        <i class="fa-solid fa-book"></i> View documentation
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('toggle-password');
            var input = document.getElementById('password');
            var icon = document.getElementById('toggle-password-icon');
            if (btn && input) {
                btn.addEventListener('click', function () {
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    icon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
                    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            }
        })();
    </script>
</body>
</html>

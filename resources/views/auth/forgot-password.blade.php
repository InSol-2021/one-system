<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset password — One System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=ibm-plex-sans:400,500,600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <main class="flex min-h-screen items-center justify-center px-5 py-12 sm:px-8">
        <div class="w-full max-w-sm">
            <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2.5">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-sm)] bg-[var(--color-ink)] text-white">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                </span>
                <span class="text-[0.95rem] font-semibold tracking-tight">One System</span>
            </a>

            <h2 class="text-2xl font-semibold tracking-tight">Reset password</h2>
            <p class="mt-1.5 text-sm text-[var(--color-muted)]">
                Enter your email address and we'll send you a link to reset your password.
            </p>

            @if(session('message'))
                <div class="os-alert os-alert-success mt-6"><i class="fa-solid fa-circle-check mt-0.5"></i><span>{{ session('message') }}</span></div>
            @endif

            @if($errors->any())
                <div class="os-alert os-alert-danger mt-6">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <div>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="mt-7 space-y-5">
                @csrf

                <div>
                    <label for="email" class="os-label">Email address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           class="os-input" placeholder="you@example.com">
                </div>

                <button type="submit" class="os-btn os-btn-primary os-btn-block os-btn-lg">Send reset link</button>
            </form>

            <div class="mt-6 border-t border-[var(--color-line)] pt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-accent)]">
                    <i class="fa-solid fa-arrow-left"></i> Back to sign in
                </a>
            </div>
        </div>
    </main>
</body>
</html>

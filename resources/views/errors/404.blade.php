@extends('layouts.app')

@section('title', '404 - Page Not Found')

@section('content')
<section class="os-container flex min-h-[70vh] items-center justify-center py-20">
    <div class="w-full max-w-md text-center">
        <p class="text-7xl font-semibold tracking-tight text-[var(--color-faint)] sm:text-8xl">404</p>
        <h1 class="mt-6 text-2xl font-semibold tracking-tight">Page not found</h1>
        <p class="mt-2 text-[var(--color-muted)]">
            The page you're looking for doesn't exist or has been moved.
        </p>

        <div class="os-card os-card-pad mt-8 text-left">
            <h2 class="text-sm font-semibold text-[var(--color-ink-2)]">Available options</h2>
            <ul class="mt-3 space-y-2 text-sm text-[var(--color-muted)]">
                <li class="flex items-start gap-2.5">
                    <i class="fa-solid fa-arrow-right mt-1 text-xs text-[var(--color-faint)]"></i>
                    <span>Return to the <a href="{{ route('login') }}" class="font-medium text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">login page</a></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <i class="fa-solid fa-arrow-right mt-1 text-xs text-[var(--color-faint)]"></i>
                    <span>Visit the <a href="{{ route('user.dashboard') }}" class="font-medium text-[var(--color-accent)] hover:text-[var(--color-accent-strong)]">user dashboard</a></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <i class="fa-solid fa-arrow-right mt-1 text-xs text-[var(--color-faint)]"></i>
                    <span>Check the URL for typos</span>
                </li>
            </ul>
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('login') }}" class="os-btn os-btn-primary os-btn-lg">Go to login</a>
            <a href="{{ route('user.dashboard') }}" class="os-btn os-btn-secondary os-btn-lg">User dashboard</a>
        </div>
    </div>
</section>
@endsection

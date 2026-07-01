@extends('layouts.app')

@section('title', 'Central Authentication Service')

@section('content')
{{-- Hero --}}
<section class="relative overflow-hidden border-b border-[var(--color-line)] bg-[var(--color-surface)]">
    <div class="os-container py-20 md:py-28">
        <div class="mx-auto max-w-3xl text-center">
            <span class="os-eyebrow">
                <i class="fa-solid fa-shield-halved"></i> Enterprise SSO
            </span>
            <h1 class="mt-5 text-4xl font-semibold tracking-tight text-[var(--color-ink)] sm:text-5xl">
                One login for every connected application
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-lg leading-relaxed text-[var(--color-muted)]">
                A Central Authentication Service built on Laravel — secure, scalable single sign-on with two-factor
                protection, full audit trails, and SDKs for every major platform.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="os-btn os-btn-primary os-btn-lg">Sign in to console</a>
                <a href="{{ route('docs') }}" class="os-btn os-btn-secondary os-btn-lg">Read the docs</a>
            </div>
        </div>
    </div>
</section>

{{-- Access points --}}
<section class="os-container py-16 md:py-20">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        @php
            $access = [
                ['icon' => 'fa-user-shield', 'title' => 'Admin console', 'desc' => 'Manage authentication, client systems, and security events from one place.', 'href' => route('login'), 'cta' => 'Open console'],
                ['icon' => 'fa-gauge-high', 'title' => 'User dashboard', 'desc' => 'Link client systems and access single sign-on across applications.', 'href' => route('user.dashboard'), 'cta' => 'Open dashboard'],
                ['icon' => 'fa-book-open', 'title' => 'Documentation', 'desc' => 'Integration guides and SDKs for Laravel, Node, Python, Java, .NET and more.', 'href' => route('docs'), 'cta' => 'Browse docs'],
            ];
        @endphp
        @foreach ($access as $card)
            <a href="{{ $card['href'] }}" class="os-card os-card-hover os-card-pad block">
                <span class="os-icon-tile"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                <h3 class="mt-4 text-lg font-semibold">{{ $card['title'] }}</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-[var(--color-muted)]">{{ $card['desc'] }}</p>
                <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-[var(--color-accent)]">
                    {{ $card['cta'] }} <i class="fa-solid fa-arrow-right text-xs"></i>
                </span>
            </a>
        @endforeach
    </div>
</section>

{{-- Features --}}
<section class="border-y border-[var(--color-line)] bg-[var(--color-surface)]">
    <div class="os-container py-16 md:py-20">
        <div class="max-w-2xl">
            <span class="os-eyebrow">Capabilities</span>
            <h2 class="mt-3 text-3xl font-semibold tracking-tight">Built for enterprise authentication</h2>
            <p class="mt-3 text-[var(--color-muted)]">Everything required to run single sign-on safely in production.</p>
        </div>
        <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $features = [
                    ['icon' => 'fa-lock', 'title' => 'Secure authentication', 'desc' => 'Modern password hashing, JWT tokens, and HMAC-SHA256 ticket validation.'],
                    ['icon' => 'fa-bolt', 'title' => 'Single sign-on', 'desc' => 'Seamless SSO across multiple client applications and frameworks.'],
                    ['icon' => 'fa-chart-line', 'title' => 'Audit & monitoring', 'desc' => 'Complete audit trails and real-time security event tracking.'],
                    ['icon' => 'fa-cubes', 'title' => 'Deploy anywhere', 'desc' => 'Containerised with Nginx, PostgreSQL and Redis, ready for production.'],
                ];
            @endphp
            @foreach ($features as $f)
                <div>
                    <span class="os-icon-tile os-icon-tile-ink"><i class="fa-solid {{ $f['icon'] }}"></i></span>
                    <h3 class="mt-4 text-base font-semibold">{{ $f['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-[var(--color-muted)]">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="os-container py-16 md:py-20">
    <div class="os-card os-card-pad flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Start integrating today</h2>
            <p class="mt-2 text-[var(--color-muted)]">Comprehensive guides and ready-to-use client packages for every major platform.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('docs') }}" class="os-btn os-btn-primary"><i class="fa-solid fa-book"></i> View documentation</a>
            <a href="{{ route('docs.laravel') }}" class="os-btn os-btn-secondary"><i class="fa-brands fa-laravel"></i> Laravel guide</a>
        </div>
    </div>
</section>
@endsection

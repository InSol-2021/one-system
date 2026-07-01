@extends('layouts.app')

@section('title', 'CAS Authentication System - Home')

@section('content')
{{-- Hero --}}
<section class="relative overflow-hidden border-b border-[var(--color-line)] bg-[var(--color-surface)]">
    <div class="os-container py-20 md:py-28">
        <div class="mx-auto max-w-3xl text-center">
            <span class="os-eyebrow">
                <i class="fa-solid fa-shield-halved"></i> Enterprise SSO
            </span>
            <h1 class="mt-5 text-4xl font-semibold tracking-tight text-[var(--color-ink)] sm:text-5xl">
                Central Authentication Service
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-lg leading-relaxed text-[var(--color-muted)]">
                Enterprise-grade single sign-on built with Laravel. Secure, scalable, and easy to integrate
                across every connected application.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="os-btn os-btn-primary os-btn-lg">Sign in to console</a>
                <a href="/docs" class="os-btn os-btn-secondary os-btn-lg">Read the docs</a>
            </div>

            <div class="mx-auto mt-8 flex max-w-md flex-wrap items-center justify-center gap-x-8 gap-y-2 text-sm text-[var(--color-muted)]">
                <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[var(--color-success)]"></span> System online
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[var(--color-success)]"></span> All services active
                </span>
            </div>
        </div>
    </div>
</section>

{{-- Access points --}}
<section class="os-container py-16 md:py-20">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        @php
            $access = [
                ['icon' => 'fa-user-shield', 'title' => 'Administrator access', 'desc' => 'Manage client systems, users, and security settings from one place.', 'href' => route('login'), 'cta' => 'Admin login'],
                ['icon' => 'fa-gauge-high', 'title' => 'User dashboard', 'desc' => 'Access your linked client systems with single sign-on.', 'href' => route('user.dashboard'), 'cta' => 'User access'],
                ['icon' => 'fa-book-open', 'title' => 'API documentation', 'desc' => 'Integration guides and API documentation for developers.', 'href' => '/docs', 'cta' => 'View docs'],
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
            <h2 class="mt-3 text-3xl font-semibold tracking-tight">Enterprise security features</h2>
            <p class="mt-3 text-[var(--color-muted)]">Comprehensive authentication and security controls for enterprise environments.</p>
        </div>
        <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $features = [
                    ['icon' => 'fa-shield-virus', 'title' => 'Multi-factor authentication', 'desc' => '2FA with TOTP, QR code setup, and backup codes for enhanced security.'],
                    ['icon' => 'fa-robot', 'title' => 'reCAPTCHA protection', 'desc' => 'Google reCAPTCHA v3 invisible protection against bots and automated attacks.'],
                    ['icon' => 'fa-ban', 'title' => 'Account lockout', 'desc' => 'Progressive security with automatic account lockout after failed attempts.'],
                    ['icon' => 'fa-key', 'title' => 'Password complexity', 'desc' => 'Enterprise-grade password requirements with strength validation.'],
                    ['icon' => 'fa-network-wired', 'title' => 'IP whitelisting', 'desc' => 'Network-level access control with comprehensive IP management.'],
                    ['icon' => 'fa-clipboard-list', 'title' => 'Comprehensive auditing', 'desc' => 'Detailed audit logs for all authentication events and security actions.'],
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

{{-- Integration --}}
<section class="os-container py-16 md:py-20">
    <div class="mx-auto max-w-2xl text-center">
        <span class="os-eyebrow">Integration</span>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight">Easy integration</h2>
        <p class="mt-3 text-[var(--color-muted)]">
            Get started with our CAS system in minutes using our comprehensive APIs and client packages.
        </p>
    </div>

    <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="os-card os-card-pad">
            <h3 class="text-lg font-semibold">REST API</h3>
            <p class="mt-1.5 text-sm leading-relaxed text-[var(--color-muted)]">Simple HTTP-based authentication API for any technology stack.</p>
            <div class="os-codeblock mt-5">
                <div class="os-codeblock-head">HTTP</div>
                <pre><code>POST /api/sso/token
GET  /api/sso/validate</code></pre>
            </div>
        </div>

        <div class="os-card os-card-pad">
            <h3 class="text-lg font-semibold">Laravel package</h3>
            <p class="mt-1.5 text-sm leading-relaxed text-[var(--color-muted)]">Native Laravel integration with Composer package support.</p>
            <div class="os-codeblock mt-5">
                <div class="os-codeblock-head">bash</div>
                <pre><code>composer require one-system/client
php artisan cas:install</code></pre>
            </div>
        </div>
    </div>
</section>
@endsection

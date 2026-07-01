@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<section class="os-container flex min-h-[70vh] items-center justify-center py-20">
    <div class="w-full max-w-md text-center">
        <p class="text-7xl font-semibold tracking-tight text-[var(--color-faint)] sm:text-8xl">500</p>
        <h1 class="mt-6 text-2xl font-semibold tracking-tight">Server error</h1>
        <p class="mt-2 text-[var(--color-muted)]">
            Something went wrong on our end. Please try again later.
        </p>

        <div class="os-alert os-alert-danger mt-8 text-left">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <div>
                <p class="font-medium">What happened?</p>
                <p class="mt-0.5">An internal server error occurred. Our team has been notified and is working to fix the issue.</p>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('login') }}" class="os-btn os-btn-primary os-btn-lg">Go to login</a>
            <button onclick="window.location.reload()" class="os-btn os-btn-secondary os-btn-lg">
                <i class="fa-solid fa-rotate-right"></i> Try again
            </button>
        </div>
    </div>
</section>
@endsection

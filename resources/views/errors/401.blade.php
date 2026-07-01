@extends('layouts.app')

@section('title', '401 - Unauthorized')

@section('content')
<section class="os-container flex min-h-[70vh] items-center justify-center py-20">
    <div class="w-full max-w-md text-center">
        <p class="text-7xl font-semibold tracking-tight text-[var(--color-faint)] sm:text-8xl">401</p>
        <h1 class="mt-6 text-2xl font-semibold tracking-tight">Unauthorized access</h1>
        <p class="mt-2 text-[var(--color-muted)]">
            You need to authenticate to access this resource.
        </p>

        <div class="os-alert os-alert-warning mt-8 text-left">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <div>
                <p class="font-medium">Authentication required</p>
                <p class="mt-0.5">Please log in with your credentials to access the CAS system.</p>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <a href="{{ route('login') }}" class="os-btn os-btn-primary os-btn-lg">
                <i class="fa-solid fa-right-to-bracket"></i> Login to CAS
            </a>
        </div>
    </div>
</section>
@endsection

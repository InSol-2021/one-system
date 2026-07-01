@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-[var(--color-canvas)] py-8">
    <div class="os-container">
        {{-- Header --}}
        <div class="os-card os-card-pad mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="os-eyebrow">Admin</span>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-[var(--color-ink)]">Admin dashboard</h1>
                    <p class="mt-1.5 text-sm text-[var(--color-muted)]">Central Authentication Service system overview and analytics.</p>
                </div>
                <div class="flex flex-col items-start gap-2 sm:items-end">
                    <span class="os-badge os-badge-accent">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        System online
                    </span>
                    <div class="text-xs text-[var(--color-faint)]">
                        Last updated: {{ now()->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @livewire('admin.dashboard-component')
        </div>
    </div>
</div>
@endsection

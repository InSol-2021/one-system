@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-[var(--color-canvas)] py-8">
    <div class="os-container">
        {{-- Header --}}
        <div class="mb-8 os-card os-card-pad">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="os-eyebrow">Audit trail</span>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-[var(--color-ink)]">Audit logs</h1>
                    <p class="mt-1.5 text-sm text-[var(--color-muted)]">Monitor all authentication and system events.</p>
                </div>
                <div class="flex items-center">
                    <span class="os-badge os-badge-accent">
                        <i class="fa-solid fa-circle-check"></i>
                        Live monitoring
                    </span>
                </div>
            </div>
        </div>

        <div class="os-card overflow-hidden">
            @livewire('admin.audit-logs-component')
        </div>
    </div>
</div>
@endsection

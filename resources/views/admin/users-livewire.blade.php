@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-[var(--color-canvas)] py-8">
    <div class="os-container">
        <!-- Header -->
        <div class="mb-6">
            <div class="os-card os-card-pad">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-4">
                        <span class="os-icon-tile os-icon-tile-ink">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <h1 class="text-2xl font-semibold tracking-tight">User management</h1>
                            <p class="mt-1 text-sm text-[var(--color-muted)]">Manage system users, roles, and access permissions</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="os-badge os-badge-accent">
                            <i class="fa-solid fa-circle-check"></i>
                            Active users
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="os-card">
            @livewire('admin.users-component')
        </div>
    </div>
</div>
@endsection

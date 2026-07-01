<div class="relative">
    <div wire:loading.delay wire:target="mount" class="absolute inset-0 z-40 flex items-center justify-center rounded-[var(--radius-lg)] bg-[var(--color-surface)]/75">
        <x-loading-overlay>Loading dashboard statistics...</x-loading-overlay>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="os-card os-card-pad">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <dt class="text-xs font-medium uppercase tracking-wide text-[var(--color-muted)]">Total users</dt>
                    <dd class="mt-2 text-3xl font-semibold tracking-tight text-[var(--color-ink)]">{{ $stats['users']['total'] }}</dd>
                    <dd class="mt-1 text-xs text-[var(--color-faint)]">{{ $stats['users']['admin'] }} admin, {{ $stats['users']['regular'] }} regular</dd>
                </div>
                <span class="os-icon-tile">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </span>
            </div>
        </div>

        <div class="os-card os-card-pad">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <dt class="text-xs font-medium uppercase tracking-wide text-[var(--color-muted)]">Client systems</dt>
                    <dd class="mt-2 text-3xl font-semibold tracking-tight text-[var(--color-ink)]">{{ $stats['client_systems']['total'] }}</dd>
                    <dd class="mt-1 text-xs text-[var(--color-faint)]">{{ $stats['client_systems']['active'] }} active, {{ $stats['client_systems']['inactive'] }} inactive</dd>
                </div>
                <span class="os-icon-tile os-icon-tile-ink">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                    </svg>
                </span>
            </div>
        </div>

        <div class="os-card os-card-pad">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <dt class="text-xs font-medium uppercase tracking-wide text-[var(--color-muted)]">Logins (24h)</dt>
                    <dd class="mt-2 text-3xl font-semibold tracking-tight text-[var(--color-ink)]">{{ $stats['authentication']['total_logins_24h'] }}</dd>
                    <dd class="mt-1 text-xs text-[var(--color-faint)]">{{ $stats['authentication']['success_rate'] }}% success rate</dd>
                </div>
                <span class="os-icon-tile">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 8A8 8 0 11-2 8a8 8 0 0116 0zm-7-3a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </span>
            </div>
        </div>

        <div class="os-card os-card-pad">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <dt class="text-xs font-medium uppercase tracking-wide text-[var(--color-muted)]">SSO tokens (24h)</dt>
                    <dd class="mt-2 text-3xl font-semibold tracking-tight text-[var(--color-ink)]">{{ $stats['sso']['tokens_24h'] }}</dd>
                    <dd class="mt-1 text-xs text-[var(--color-faint)]">Authentication tokens issued</dd>
                </div>
                <span class="os-icon-tile os-icon-tile-ink">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                </span>
            </div>
        </div>
    </div>

    {{-- Activity + success rate --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-8">
        <div class="os-card os-card-pad">
            <div class="mb-6 flex items-center justify-between gap-4">
                <h3 class="text-base font-semibold text-[var(--color-ink)]">Authentication activity</h3>
                <select wire:model.live="selectedPeriod" class="os-input w-auto py-1.5 text-sm">
                    <option value="7days">Last 7 days</option>
                    <option value="30days">Last 30 days</option>
                </select>
            </div>

            <div class="space-y-3">
                @foreach($activityData as $day)
                    <div class="flex items-center gap-3">
                        <div class="w-16 text-xs text-[var(--color-muted)]">{{ $day['label'] }}</div>
                        <div class="flex flex-1 gap-3">
                            <div class="flex-1">
                                <div class="mb-1 flex items-center justify-between text-xs text-[var(--color-ink-2)]">
                                    <span>Logins: {{ $day['logins'] }}</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-[var(--color-surface-2)]">
                                    <div class="h-2 rounded-full bg-[var(--color-accent)]" style="width: {{ $activityData ? min(100, ($day['logins'] / max(1, collect($activityData)->max('logins'))) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="mb-1 flex items-center justify-between text-xs text-[var(--color-ink-2)]">
                                    <span>SSO: {{ $day['sso_tokens'] }}</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-[var(--color-surface-2)]">
                                    <div class="h-2 rounded-full bg-[var(--color-ink-2)]" style="width: {{ $activityData ? min(100, ($day['sso_tokens'] / max(1, collect($activityData)->max('sso_tokens'))) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 flex items-center gap-4 text-xs">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-sm bg-[var(--color-accent)]"></span>
                    <span class="text-[var(--color-muted)]">Login attempts</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-sm bg-[var(--color-ink-2)]"></span>
                    <span class="text-[var(--color-muted)]">SSO tokens</span>
                </div>
            </div>
        </div>

        <div class="os-card os-card-pad">
            <h3 class="mb-6 text-base font-semibold text-[var(--color-ink)]">Login success rate (24h)</h3>

            <div class="flex items-center justify-center">
                @if($stats['authentication']['total_logins_24h'] > 0)
                    <div class="relative h-40 w-40">
                        <svg class="h-40 w-40 -rotate-90" viewBox="0 0 36 36">
                            <path stroke="var(--color-surface-2)" stroke-width="3" fill="none"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path stroke="var(--color-accent)" stroke-width="3" fill="none"
                                  stroke-dasharray="{{ $stats['authentication']['success_rate'] }}, 100"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-semibold text-[var(--color-ink)]">{{ $stats['authentication']['success_rate'] }}%</div>
                                <div class="text-xs text-[var(--color-muted)]">Success</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <div class="flex h-40 w-40 items-center justify-center rounded-full bg-[var(--color-surface-2)]">
                            <div>
                                <div class="text-base font-medium text-[var(--color-ink-2)]">No data</div>
                                <div class="text-sm text-[var(--color-muted)]">No logins in 24h</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 space-y-2.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-[var(--color-ink-2)]">
                        <span class="h-2.5 w-2.5 rounded-full bg-[var(--color-accent)]"></span>
                        Successful
                    </span>
                    <span class="font-medium text-[var(--color-ink)]">{{ $stats['authentication']['successful_logins_24h'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-[var(--color-ink-2)]">
                        <span class="h-2.5 w-2.5 rounded-full bg-[var(--color-line-strong)]"></span>
                        Failed
                    </span>
                    <span class="font-medium text-[var(--color-ink)]">{{ $stats['authentication']['failed_logins_24h'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="os-card overflow-hidden">
        <div class="border-b border-[var(--color-line)] px-6 py-4">
            <h3 class="text-base font-semibold text-[var(--color-ink)]">Recent activity</h3>
        </div>
        <div class="overflow-hidden">
            @if($recentActivity->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[var(--color-line)] text-left">
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wide text-[var(--color-muted)]">Event</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wide text-[var(--color-muted)]">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-[var(--color-muted)]">When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivity as $activity)
                            <tr class="border-b border-[var(--color-line)] last:border-0">
                                <td class="px-6 py-4 align-top">
                                    <div class="text-sm font-medium text-[var(--color-ink)]">
                                        {{ ucfirst(str_replace('_', ' ', $activity['event_type'])) }}
                                    </div>
                                    <div class="mt-0.5 text-sm text-[var(--color-muted)]">
                                        {{ $activity['username'] ?? 'System' }} &middot; {{ $activity['ip_address'] }}
                                        @if($activity['client_system_name'] && $activity['client_system_name'] !== 'N/A')
                                            &middot; {{ $activity['client_system_name'] }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    @if($activity['success'])
                                        <span class="os-badge os-badge-accent">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Success
                                        </span>
                                    @else
                                        <span class="os-badge">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 10.586l2.293-2.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right align-top text-sm text-[var(--color-muted)]">
                                    {{ $activity['created_at'] ? \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() : 'Unknown' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center">
                    <span class="os-icon-tile os-icon-tile-ink mx-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </span>
                    <p class="mt-4 text-base font-medium text-[var(--color-ink-2)]">No recent activity</p>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Activity will appear here as users interact with the system</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick links --}}
    <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
        <a href="/admin/users" class="os-card os-card-pad os-card-hover block">
            <div class="flex items-center gap-4">
                <span class="os-icon-tile">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                <div>
                    <div class="text-sm font-semibold text-[var(--color-ink)]">Manage users</div>
                    <div class="text-sm text-[var(--color-muted)]">Add, edit, or remove users</div>
                </div>
            </div>
        </a>

        <a href="/admin/client-systems" class="os-card os-card-pad os-card-hover block">
            <div class="flex items-center gap-4">
                <span class="os-icon-tile">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                <div>
                    <div class="text-sm font-semibold text-[var(--color-ink)]">Client systems</div>
                    <div class="text-sm text-[var(--color-muted)]">Configure authentication clients</div>
                </div>
            </div>
        </a>

        <a href="/admin/audit-logs" class="os-card os-card-pad os-card-hover block">
            <div class="flex items-center gap-4">
                <span class="os-icon-tile">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                <div>
                    <div class="text-sm font-semibold text-[var(--color-ink)]">View audit logs</div>
                    <div class="text-sm text-[var(--color-muted)]">Monitor system activity</div>
                </div>
            </div>
        </a>
    </div>
</div>

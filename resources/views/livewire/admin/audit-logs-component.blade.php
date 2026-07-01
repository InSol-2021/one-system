<div class="relative">
    <div wire:loading.delay wire:target="search,eventTypeFilter,dateFilter,mount" class="absolute inset-0 bg-[var(--color-surface)]/75 z-40 flex items-center justify-center rounded-[var(--radius-lg)]">
        <x-loading-overlay>Loading audit logs...</x-loading-overlay>
    </div>

    {{-- Filter bar --}}
    <div class="p-6 border-b border-[var(--color-line)]">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div>
                <label class="os-label">Search</label>
                <div class="relative">
                    <input type="text"
                           wire:model.live="search"
                           placeholder="Search logs..."
                           class="os-input pr-10">
                    <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-4 w-4 text-[var(--color-faint)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="os-label">Event type</label>
                <div class="relative">
                    <select wire:model.live="eventTypeFilter" class="os-input pr-10 appearance-none">
                        <option value="">All events</option>
                        @foreach($eventTypes as $eventType)
                            <option value="{{ $eventType }}">{{ ucfirst($eventType) }}</option>
                        @endforeach
                    </select>
                    <div wire:loading wire:target="eventTypeFilter" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-4 w-4 text-[var(--color-faint)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="os-label">Time period</label>
                <div class="relative">
                    <select wire:model.live="dateFilter" class="os-input pr-10 appearance-none">
                        <option value="">All time</option>
                        <option value="today">Today</option>
                        <option value="week">Last week</option>
                        <option value="month">Last month</option>
                    </select>
                    <div wire:loading wire:target="dateFilter" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-4 w-4 text-[var(--color-faint)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-end">
                <div class="text-sm text-[var(--color-muted)]">
                    <div class="font-medium text-[var(--color-ink)]">{{ $auditLogs->total() }} total logs</div>
                    <div>{{ $auditLogs->count() }} showing</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">Client system</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">IP address</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                @forelse($auditLogs as $log)
                    <tr wire:key="audit-log-{{ $log->id }}" class="hover:bg-[var(--color-surface-2)] transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-ink-2)]">
                            {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i:s') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-[var(--color-surface-2)] border border-[var(--color-line)] text-[var(--color-muted)]">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </span>
                                <span class="text-sm font-medium text-[var(--color-ink)]">
                                    {{ $log->user->username ?? 'System' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-[var(--color-ink)]">{{ ucfirst($log->action) }}</div>
                            <div class="mt-0.5"><span class="os-badge">{{ ucfirst($log->event_type) }}</span></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-ink-2)]">
                            {{ $log->clientSystem->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-[var(--color-ink-2)]">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->success)
                                <span class="os-badge os-badge-accent">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Success
                                </span>
                            @else
                                <span class="os-badge" style="background-color: var(--color-danger-soft); color: var(--color-danger); border-color: #fecaca;">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    Failed
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="toggleDetails({{ $log->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleDetails"
                                    class="text-[var(--color-accent)] hover:text-[var(--color-accent-strong)] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ isset($showDetails[$log->id]) ? 'Hide' : 'View' }} details
                            </button>
                        </td>
                    </tr>

                    @if(isset($showDetails[$log->id]))
                        <tr class="bg-[var(--color-surface-2)]">
                            <td colspan="7" class="px-6 py-4">
                                <div class="space-y-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <h4 class="text-sm font-semibold text-[var(--color-ink)] mb-2">Event details</h4>
                                            <div class="space-y-1 text-sm text-[var(--color-ink-2)]">
                                                <div><span class="font-medium text-[var(--color-muted)]">User agent:</span> {{ $log->user_agent ?? 'N/A' }}</div>
                                                <div><span class="font-medium text-[var(--color-muted)]">Description:</span> {{ $log->description ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-[var(--color-ink)] mb-2">Additional data</h4>
                                            <div class="os-codeblock">
                                                <pre class="text-xs">@if($log->details)@if(is_array($log->details)){{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}@else{{ $log->details }}@endif @else No additional details @endif</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-[var(--color-muted)]">
                                <div class="flex justify-center mb-4">
                                    <span class="os-icon-tile os-icon-tile-ink">
                                        <i class="fa-regular fa-file-lines"></i>
                                    </span>
                                </div>
                                <p class="text-base font-medium text-[var(--color-ink)]">No audit logs found</p>
                                <p class="mt-1 text-sm">Try adjusting your search criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($auditLogs->hasPages())
        <div class="px-6 py-4 border-t border-[var(--color-line)]">
            {{ $auditLogs->links() }}
        </div>
    @endif
</div>

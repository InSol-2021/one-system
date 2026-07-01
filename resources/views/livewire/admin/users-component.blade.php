<div class="relative">
    <div wire:loading.delay wire:target="search,sortBy,mount" class="absolute inset-0 bg-[var(--color-surface)]/75 z-40 flex items-center justify-center">
        <x-loading-overlay>Loading users...</x-loading-overlay>
    </div>

    @if (session()->has('message'))
        <div class="m-6 mb-0 os-alert os-alert-success">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <p class="font-medium">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="m-6 mb-0 os-alert os-alert-danger">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="p-6 border-b border-[var(--color-line)]">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="os-card os-card-pad flex items-center gap-4">
                <span class="os-icon-tile os-icon-tile-ink">
                    <i class="fa-solid fa-user-group"></i>
                </span>
                <div>
                    <div class="text-2xl font-semibold tracking-tight">{{ $totalUsers }}</div>
                    <div class="text-sm text-[var(--color-muted)]">Total users</div>
                </div>
            </div>

            <div class="os-card os-card-pad flex items-center gap-4">
                <span class="os-icon-tile">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <div>
                    <div class="text-2xl font-semibold tracking-tight">{{ $adminUsers }}</div>
                    <div class="text-sm text-[var(--color-muted)]">Admin users</div>
                </div>
            </div>

            <div class="os-card os-card-pad flex items-center gap-4">
                <span class="os-icon-tile os-icon-tile-ink">
                    <i class="fa-solid fa-user"></i>
                </span>
                <div>
                    <div class="text-2xl font-semibold tracking-tight">{{ $regularUsers }}</div>
                    <div class="text-sm text-[var(--color-muted)]">Regular users</div>
                </div>
            </div>

            <button wire:click="openCreateModal"
                    class="os-card os-card-hover os-card-pad flex items-center gap-4 text-left">
                <span class="os-icon-tile">
                    <i class="fa-solid fa-plus"></i>
                </span>
                <div>
                    <div class="text-base font-medium text-[var(--color-ink)]">Add user</div>
                    <div class="text-sm text-[var(--color-muted)]">Create new account</div>
                </div>
            </button>
        </div>
    </div>

    <div class="p-6 border-b border-[var(--color-line)]">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="os-label">Search users</label>
                <div class="relative">
                    <input type="text" wire:model.live="search" placeholder="Search by username, email, or name..." class="os-input">
                </div>
            </div>

            <div>
                <label class="os-label">Filter by role</label>
                <select wire:model.live="roleFilter" class="os-input">
                    <option value="">All roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div class="flex items-end">
                <div class="text-sm text-[var(--color-muted)]">
                    <span class="font-semibold text-[var(--color-ink)]">{{ $users->total() }}</span> user(s) found
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-[var(--color-surface-2)] border-b border-[var(--color-line)]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-muted)] uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-muted)] uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-muted)] uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-muted)] uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-muted)] uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-line)]">
                @forelse($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-[var(--color-surface-2)] transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-[var(--color-surface-2)] border border-[var(--color-line)] flex items-center justify-center">
                                        <i class="fa-solid fa-user text-[var(--color-muted)]"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-[var(--color-ink)]">{{ $user->username }}</div>
                                    @if($user->first_name || $user->last_name)
                                        <div class="text-sm text-[var(--color-muted)]">{{ trim($user->first_name . ' ' . $user->last_name) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-ink-2)]">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                                <span class="os-badge os-badge-accent">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    Admin
                                </span>
                            @else
                                <span class="os-badge">
                                    <i class="fa-solid fa-user"></i>
                                    User
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-muted)]">
                            {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button wire:click="openEditModal({{ $user->id }})"
                                        wire:loading.attr="disabled"
                                        class="os-btn os-btn-ghost px-2.5 py-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Edit
                                </button>
                                <button wire:click="openDeleteModal({{ $user->id }})"
                                        wire:loading.attr="disabled"
                                        class="os-btn os-btn-ghost px-2.5 py-1.5 text-[var(--color-danger)] hover:text-[var(--color-danger)] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="text-[var(--color-muted)]">
                                <span class="os-icon-tile os-icon-tile-ink mx-auto mb-4">
                                    <i class="fa-solid fa-users-slash"></i>
                                </span>
                                <p class="text-base font-medium text-[var(--color-ink)]">No users found</p>
                                <p class="text-sm">Try adjusting your search criteria</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-[var(--color-line)]">
            {{ $users->links() }}
        </div>
    @endif

    @if($showCreateModal)
    <div class="fixed inset-0 bg-[var(--color-ink)]/50 z-50 flex items-center justify-center p-4" wire:click="closeModals">
        <div class="os-card w-full max-w-md relative shadow-[var(--shadow-lg)]" onclick="event.stopPropagation()">
            <!-- Loading Overlay -->
            <div wire:loading wire:target="createUser" class="absolute inset-0 bg-[var(--color-surface)]/90 flex items-center justify-center rounded-[var(--radius-lg)] z-10">
                <div class="flex items-center gap-2">
                    <x-spinner size="h-5 w-5" color="text-[var(--color-accent)]" />
                    <span class="text-sm text-[var(--color-muted)]">Creating user...</span>
                </div>
            </div>

            <div class="os-card-pad">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold tracking-tight">Create new user</h3>
                    <button wire:click="closeModals" class="text-[var(--color-faint)] hover:text-[var(--color-ink-2)] transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form wire:submit.prevent="createUser" class="space-y-4">
                    <div>
                        <label class="os-label">Username</label>
                        <input type="text" wire:model="username" class="os-input">
                        @error('username') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Email</label>
                        <input type="email" wire:model="email" class="os-input">
                        @error('email') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="os-label">First name</label>
                            <input type="text" wire:model="first_name" class="os-input">
                            @error('first_name') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="os-label">Last name</label>
                            <input type="text" wire:model="last_name" class="os-input">
                            @error('last_name') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="os-label">Role</label>
                        <select wire:model="role" class="os-input">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Password</label>
                        <input type="password" wire:model="password" class="os-input">
                        @error('password') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="os-label">Confirm password</label>
                        <input type="password" wire:model="password_confirmation" class="os-input">
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModals"
                                wire:loading.attr="disabled"
                                class="os-btn os-btn-secondary disabled:opacity-50">
                            Cancel
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="createUser"
                                class="os-btn os-btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                            Create user
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($showEditModal)
        <div class="fixed inset-0 bg-[var(--color-ink)]/50 overflow-y-auto h-full w-full z-50 p-4" wire:click="closeModals">
            <div class="os-card w-full max-w-md mx-auto mt-20 relative shadow-[var(--shadow-lg)]" onclick="event.stopPropagation()">
                <!-- Loading Overlay -->
                <div wire:loading wire:target="updateUser" class="absolute inset-0 bg-[var(--color-surface)]/90 flex items-center justify-center rounded-[var(--radius-lg)] z-10">
                    <div class="flex items-center gap-2">
                        <x-spinner size="h-5 w-5" color="text-[var(--color-accent)]" />
                        <span class="text-sm text-[var(--color-muted)]">Updating user...</span>
                    </div>
                </div>

                <div class="os-card-pad">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold tracking-tight">Edit user</h3>
                        <button wire:click="closeModals" class="text-[var(--color-faint)] hover:text-[var(--color-ink-2)] transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="updateUser" class="space-y-4">
                        <div>
                            <label class="os-label">Username</label>
                            <input type="text" wire:model="username" class="os-input">
                            @error('username') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="os-label">Email</label>
                            <input type="email" wire:model="email" class="os-input">
                            @error('email') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="os-label">First name</label>
                                <input type="text" wire:model="first_name" class="os-input">
                                @error('first_name') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="os-label">Last name</label>
                                <input type="text" wire:model="last_name" class="os-input">
                                @error('last_name') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="os-label">Role</label>
                            <select wire:model="role" class="os-input">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="os-label">New password (leave blank to keep current)</label>
                            <input type="password" wire:model="password" class="os-input">
                            @error('password') <span class="mt-1 block text-xs text-[var(--color-danger)]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="os-label">Confirm new password</label>
                            <input type="password" wire:model="password_confirmation" class="os-input">
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeModals" class="os-btn os-btn-secondary">
                                Cancel
                            </button>
                            <button type="submit" class="os-btn os-btn-primary">
                                Update user
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="fixed inset-0 bg-[var(--color-ink)]/50 overflow-y-auto h-full w-full z-50 p-4" wire:click="closeModals">
            <div class="os-card w-full max-w-md mx-auto mt-20 relative shadow-[var(--shadow-lg)]" onclick="event.stopPropagation()">
                <!-- Loading Overlay -->
                <div wire:loading wire:target="deleteUser" class="absolute inset-0 bg-[var(--color-surface)]/90 flex items-center justify-center rounded-[var(--radius-lg)] z-10">
                    <div class="flex items-center gap-2">
                        <x-spinner size="h-5 w-5" color="text-[var(--color-danger)]" />
                        <span class="text-sm text-[var(--color-muted)]">Deleting user...</span>
                    </div>
                </div>

                <div class="os-card-pad text-center">
                    <span class="os-icon-tile mx-auto mb-4 bg-[var(--color-danger-soft)] text-[var(--color-danger)] border-[#fecaca]">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>
                    <h3 class="text-lg font-semibold tracking-tight mb-2">Delete user</h3>
                    <p class="text-sm text-[var(--color-muted)] mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="closeModals"
                                wire:loading.attr="disabled"
                                class="os-btn os-btn-secondary disabled:opacity-50">
                            Cancel
                        </button>
                        <button wire:click="deleteUser"
                                wire:loading.attr="disabled"
                                wire:target="deleteUser"
                                class="os-btn os-btn-primary bg-[var(--color-danger)] hover:bg-[#991b1b] disabled:opacity-50 disabled:cursor-not-allowed">
                            Delete user
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

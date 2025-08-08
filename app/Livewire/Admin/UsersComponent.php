<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UsersComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedUserId = null;
    public $processing = false;

    // Form fields
    public $username = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'user';
    public $first_name = '';
    public $last_name = '';

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        $rules = [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->showCreateModal) {
            $rules['username'][] = Rule::unique(User::class, 'username');
            $rules['email'][] = Rule::unique(User::class, 'email');
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        } elseif ($this->showEditModal && $this->selectedUserId) {
            $rules['username'][] = Rule::unique(User::class, 'username')->ignore($this->selectedUserId);
            $rules['email'][] = Rule::unique(User::class, 'email')->ignore($this->selectedUserId);
            $rules['password'] = ['nullable', 'string', 'min:6', 'confirmed'];
        }

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        try {
            $this->resetForm();
            $this->showCreateModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to open form. Please try again.');
        }
    }

    public function openEditModal($userId)
    {
        $this->selectedUserId = $userId;
        $user = User::find($userId);

        if ($user) {
            $this->username = $user->username;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->first_name = $user->first_name ?? '';
            $this->last_name = $user->last_name ?? '';
            $this->password = '';
            $this->password_confirmation = '';
            $this->showEditModal = true;
        }
    }

    public function openDeleteModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function createUser()
    {
        if ($this->processing) {
            return;
        }

        $this->processing = true;

        $this->validate();

        try {
            User::create([
                'username' => $this->username,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'first_name' => $this->first_name ?: null,
                'last_name' => $this->last_name ?: null,
            ]);

            $this->closeModals();

            $this->resetPage();
            $this->dispatch('$refresh');

            session()->flash('message', 'User created successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create user: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }

    public function updateUser()
    {
        if ($this->processing) {
            return;
        }

        $this->processing = true;

        $this->validate();

        try {
            $updateData = [
                'username' => $this->username,
                'email' => $this->email,
                'role' => $this->role,
                'first_name' => $this->first_name ?: null,
                'last_name' => $this->last_name ?: null,
                'updated_at' => now(),
            ];

            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $user = User::findOrFail($this->selectedUserId);
            $user->update($updateData);

            $this->closeModals();

            $this->dispatch('$refresh');

            session()->flash('message', 'User updated successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update user: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }

    public function deleteUser()
    {
        if ($this->processing) {
            return;
        }

        $this->processing = true;

        try {
            User::findOrFail($this->selectedUserId)->delete();

            $this->closeModals();

            $this->dispatch('$refresh');

            session()->flash('message', 'User deleted successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete user: ' . $e->getMessage());
        } finally {
            $this->processing = false;
        }
    }

    private function resetForm()
    {
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'user';
        $this->first_name = '';
        $this->last_name = '';
        $this->selectedUserId = null;
    }

    public function render()
    {
        $query = User::query()->orderBy('created_at', 'desc');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('username', 'ILIKE', '%' . $this->search . '%')
                  ->orWhere('email', 'ILIKE', '%' . $this->search . '%')
                  ->orWhere('first_name', 'ILIKE', '%' . $this->search . '%')
                  ->orWhere('last_name', 'ILIKE', '%' . $this->search . '%');
            });
        }

        if (!empty($this->roleFilter)) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->paginate(15);

        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $regularUsers = User::where('role', 'user')->count();

        return view('livewire.admin.users-component', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'adminUsers' => $adminUsers,
            'regularUsers' => $regularUsers
        ]);
    }
}

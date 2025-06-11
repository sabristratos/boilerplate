<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    public ?string $status = null;
    public ?int $role = null;

    public bool $confirmingDelete = false;
    public ?User $deletingUser = null;

    public function hasFilters(): bool
    {
        return !empty($this->search) || !empty($this->status) || !empty($this->role);
    }

    #[On('user-saved')]
    public function refresh(): void
    {
        // This will refresh the component rendering the user list.
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $column;
        $this->resetPage();
    }

    #[On('confirm-delete-user')]
    public function confirmDeleteUser(User $user): void
    {
        Gate::authorize('delete-users');
        $this->deletingUser = $user;
        $this->confirmingDelete = true;
    }

    public function delete(UserService $userService): void
    {
        Gate::authorize('delete-users');
        if (!$this->deletingUser) {
            return;
        }

        try {
            $userService->deleteUser($this->deletingUser);
            Flux::toast(
                text: __('User deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->dispatch('user-saved'); // Re-use event to refresh list
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to delete user. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }

        $this->confirmingDelete = false;
        $this->deletingUser = null;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $latestSessions = DB::table('sessions')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->whereNotNull('user_id')
            ->groupBy('user_id');

        $users = User::query()
            ->leftJoinSub($latestSessions, 'latest_sessions', function ($join) {
                $join->on('users.id', '=', 'latest_sessions.user_id');
            })
            ->select('users.*', 'latest_sessions.last_activity')
            ->with(['roles'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, fn ($query, $status) => $query->where('status', $status))
            ->when($this->role, fn ($query, $role) => $query->whereRelation('roles', 'roles.id', $role))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.users.index', [
            'users' => $users,
            'roles' => Role::all(),
            'statuses' => UserStatus::cases(),
        ]);
    }
} 
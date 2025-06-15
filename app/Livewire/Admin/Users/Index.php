<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Livewire\Traits\WithFiltering;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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
    use WithFiltering;

    public ?string $status = null;
    public ?int $role = null;

    public bool $confirmingDelete = false;
    public ?User $deletingUser = null;

    public string $sortBy = 'name';
    protected array $searchableColumns = ['users.name', 'users.email'];

    public function hasFilters(): bool
    {
        return !empty($this->search) || !empty($this->status) || !empty($this->role);
    }

    #[On('user-saved')]
    public function refresh(): void
    {
        // This will refresh the component rendering the user list.
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

    public function export()
    {
        Gate::authorize('view-users');

        return Excel::download(new UsersExport($this->getUsersQuery()), 'users.xlsx');
    }

    protected function getUsersQuery(): Builder
    {
        $latestSessions = DB::table('sessions')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->whereNotNull('user_id')
            ->groupBy('user_id');

        $query = User::query()
            ->leftJoinSub($latestSessions, 'latest_sessions', function ($join) {
                $join->on('users.id', '=', 'latest_sessions.user_id');
            })
            ->select('users.*', 'latest_sessions.last_activity')
            ->with(['roles']);

        $query = $this->applySearching($query, $this->searchableColumns);
        return $query->when($this->status, fn (Builder $query, $status) => $query->where('status', $status))
            ->when($this->role, fn (Builder $query, $role) => $query->whereRelation('roles', 'roles.id', $role));
    }

    public function render(): View
    {
        $query = $this->getUsersQuery();

        $users = $this->applySorting($query)->paginate($this->perPage);

        return view('livewire.admin.users.index', [
            'users' => $users,
            'roles' => Role::all(),
            'statuses' => UserStatus::cases(),
        ]);
    }
} 
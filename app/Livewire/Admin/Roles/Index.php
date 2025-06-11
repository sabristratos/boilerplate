<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Models\Role;
use App\Services\RoleService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
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

    public bool $confirmingDelete = false;
    public ?Role $deletingRole = null;

    public function hasFilters(): bool
    {
        return !empty($this->search);
    }

    #[On('role-saved')]
    public function refresh(): void
    {
        // This will refresh the component rendering the role list.
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

    #[On('confirm-delete-role')]
    public function confirmDeleteRole(Role $role): void
    {
        Gate::authorize('delete-roles');
        $this->deletingRole = $role;
        $this->confirmingDelete = true;
    }

    public function delete(RoleService $roleService): void
    {
        Gate::authorize('delete-roles');
        if (!$this->deletingRole) {
            return;
        }

        try {
            $roleService->deleteRole($this->deletingRole);
            Flux::toast(
                text: __('Role deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->dispatch('role-saved');
        } catch (\Exception $e) {
            Log::error('Failed to delete role: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to delete role. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }

        $this->confirmingDelete = false;
        $this->deletingRole = null;
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
        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
        ]);
    }
} 
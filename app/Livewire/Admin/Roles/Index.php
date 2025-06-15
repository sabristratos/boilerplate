<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Livewire\Traits\WithFiltering;
use App\Models\Role;
use App\Services\RoleService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;
    use WithFiltering;

    public bool $confirmingDelete = false;
    public ?Role $deletingRole = null;

    protected array $searchableColumns = ['name', 'description'];

    #[On('role-saved')]
    public function refresh(): void
    {
        // This will refresh the component rendering the role list.
    }

    #[On('confirm-delete-role')]
    public function confirmDeleteRole(Role $role): void
    {
        $this->authorize('delete', $role);
        $this->deletingRole = $role;
        $this->confirmingDelete = true;
    }

    public function delete(RoleService $roleService): void
    {
        if (!$this->deletingRole) {
            return;
        }

        $this->authorize('delete', $this->deletingRole);

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

    public function render(): View
    {
        $query = Role::query()
            ->withCount(['permissions', 'users']);
        $query = $this->applySearching($query, $this->searchableColumns);
        $roles = $this->applySorting($query)->paginate($this->perPage);

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
        ]);
    }
} 
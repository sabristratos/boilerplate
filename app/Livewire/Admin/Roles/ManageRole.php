<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ManageRole extends Component
{
    public ?Role $role = null;

    public string $name = '';
    public string $description = '';
    public array $selectedPermissions = [];

    public function mount(?Role $role): void
    {
        $this->role = $role;
        if ($this->role?->exists) {
            Gate::authorize('edit-roles');
            $this->name = $this->role->name;
            $this->description = $this->role->description ?? '';
            $this->selectedPermissions = $this->role->permissions->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            Gate::authorize('create-roles');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'selectedPermissions' => 'array',
        ];
    }

    public function save(RoleService $roleService): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        try {
            if ($this->role?->exists) {
                Gate::authorize('edit-roles');
                $roleService->updateRole($this->role, $data, $this->selectedPermissions);
                Flux::toast(
                    text: __('Role updated successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
            } else {
                Gate::authorize('create-roles');
                $role = $roleService->createRole($data, $this->selectedPermissions);
                Flux::toast(
                    text: __('Role created successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
                $this->redirect(route('admin.roles.edit', $role), navigate: true);
                return;
            }
        } catch (\Exception $e) {
            Log::error('Failed to save role: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to save role. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        $this->dispatch('role-saved');
        $this->redirect(route('admin.roles.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.roles.manage-role', [
            'permissions' => Permission::all()->groupBy('group'),
        ]);
    }
} 
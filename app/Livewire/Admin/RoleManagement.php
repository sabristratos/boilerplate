<?php

namespace App\Livewire\Admin;

use App\Facades\ActivityLogger;
use App\Models\Role;
use App\Models\Permission;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

/**
 * Manages roles and permissions within the admin panel.
 *
 * This component allows for creating, reading, updating, and deleting roles,
 * as well as assigning permissions to them. It includes features like
 * pagination, search, and modal dialogs for CRUD operations.
 */
#[Layout('components.admin-layout')]
class RoleManagement extends Component
{
    use WithPagination;

    public ?int $role_id = null;
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public array $selectedPermissions = [];

    public bool $isCreating = false;
    public bool $isEditing = false;
    public bool $confirmingDelete = false;
    public bool $showModal = false;

    public string $search = '';
    public int $perPage = 10;

    /**
     * Validation rules for role properties.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $slugRules = [
            'required',
            'string',
            'max:255',
        ];

        if ($this->isCreating || !$this->role_id) {
            $slugRules[] = Rule::unique('roles', 'slug');
        } else {
            $slugRules[] = Rule::unique('roles', 'slug')->ignore($this->role_id);
        }

        return [
            'name' => 'required|string|max:255',
            'slug' => $slugRules,
            'description' => 'nullable|string',
            'selectedPermissions' => 'array',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('The role name is required.'),
            'slug.required' => __('The role slug is required.'),
            'slug.unique' => __('The role slug has already been taken.'),
        ];
    }

    /**
     * Automatically generate the slug when the name is updated.
     *
     * @param string $value The new name value.
     * @return void
     */
    public function updatedName(string $value): void
    {
        if ($this->isCreating || ($this->isEditing && Role::find($this->role_id)?->name !== $value)) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * Prepare the component to create a new role.
     *
     * Opens the modal for role creation.
     *
     * @return void
     */
    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['role_id', 'name', 'slug', 'description', 'selectedPermissions']);
        $this->isCreating = true;
        $this->isEditing = false;
        $this->confirmingDelete = false;
        $this->showModal = true;
    }

    /**
     * Store a newly created role in storage.
     *
     * Validates input, creates the role, attaches permissions, logs the action,
     * and shows a success toast.
     *
     * @return void
     */
    public function store(): void
    {
        $this->validate();

        try {
            $role = Role::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);

            if (!empty($this->selectedPermissions)) {
                $role->permissions()->attach($this->selectedPermissions);
            }

            ActivityLogger::logCreated(
                $role,
                auth()->user(),
                [
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'permissions' => $this->selectedPermissions,
                ],
                'role'
            );

            $this->closeModal();
            Flux::toast(
                text: __('Role created successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        } catch (\Exception $e) {
            Log::error('Failed to store role: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to create role. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Prepare the component to edit an existing role.
     *
     * Loads role data into the form and opens the modal for editing.
     *
     * @param Role $role The role to edit.
     * @return void
     */
    public function edit(Role $role): void
    {
        $this->resetValidation();
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->slug = $role->slug;
        $this->description = $role->description ?? '';
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        $this->isCreating = false;
        $this->isEditing = true;
        $this->confirmingDelete = false;
        $this->showModal = true;
    }

    /**
     * Update the specified role in storage.
     *
     * Validates input, updates the role, syncs permissions, logs the action,
     * and shows a success toast.
     *
     * @return void
     */
    public function update(): void
    {
        if (!$this->role_id) {
            Flux::toast(text: __('No role selected for update.'), heading: __('Error'), variant: 'danger');
            return;
        }
        $this->validate();

        try {
            $role = Role::findOrFail($this->role_id);

            $oldValues = [
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'permissions' => $role->permissions->pluck('id')->map(fn ($id) => (string) $id)->toArray(),
            ];

            $role->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);

            $role->permissions()->sync($this->selectedPermissions);

            ActivityLogger::logUpdated(
                $role,
                auth()->user(),
                [
                    'old' => $oldValues,
                    'new' => [
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'description' => $role->description,
                        'permissions' => $this->selectedPermissions,
                    ],
                ],
                'role'
            );

            $this->closeModal();
            Flux::toast(
                text: __('Role updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        } catch (\Exception $e) {
            Log::error('Failed to update role: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to update role. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Prepare the component for role deletion confirmation.
     *
     * Sets the role to be deleted and opens the confirmation modal.
     *
     * @param Role $role The role to delete.
     * @return void
     */
    public function confirmDelete(Role $role): void
    {
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->isCreating = false;
        $this->isEditing = false;
        $this->confirmingDelete = true;
        $this->showModal = true;
    }

    /**
     * Remove the specified role from storage.
     *
     * Detaches permissions and users, deletes the role, logs the action,
     * and shows a success toast.
     *
     * @return void
     */
    public function delete(): void
    {
        if (!$this->role_id) {
            Flux::toast(text: __('No role selected for deletion.'), heading: __('Error'), variant: 'danger');
            return;
        }

        try {
            $role = Role::findOrFail($this->role_id);

            $roleData = [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'permissions' => $role->permissions->pluck('id')->toArray(),
                'users_count' => $role->users()->count(),
            ];

            ActivityLogger::logDeleted(
                $role,
                auth()->user(),
                $roleData,
                'role'
            );

            $role->permissions()->detach();
            $role->users()->detach();
            $role->delete();

            $this->closeModal();
            Flux::toast(
                text: __('Role deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete role: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to delete role. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Close any open modal and reset component state.
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->isCreating = false;
        $this->isEditing = false;
        $this->confirmingDelete = false;
        $this->reset(['role_id', 'name', 'slug', 'description', 'selectedPermissions']);
        $this->resetValidation();
    }

    /**
     * Render the component.
     *
     * Fetches roles and permissions for display in the view.
     *
     * @return View
     */
    public function render(): View
    {
        $rolesQuery = Role::query();

        if (!empty($this->search)) {
            $rolesQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $roles = $rolesQuery->withCount('users', 'permissions')->latest()->paginate($this->perPage);
        $permissions = Permission::all();

        return view('livewire.admin.role-management', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}

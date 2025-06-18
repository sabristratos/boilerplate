<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionService;
use App\Services\RoleService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ManageRole extends Component
{
    public ?Role $role = null;

    public array $name = [];
    public array $description = [];
    public array $selectedPermissions = [];
    public array $locales;
    public string $currentLocale;

    public function mount(?Role $role): void
    {
        $this->role = $role;
        $this->locales = config('app.available_locales', ['en' => 'English']);
        $this->currentLocale = array_key_first($this->locales);

        if ($this->role?->exists) {
            $this->authorize('update', $this->role);
            foreach (array_keys($this->locales) as $localeCode) {
                $this->name[$localeCode] = $this->role->getTranslation('name', $localeCode);
                $this->description[$localeCode] = $this->role->getTranslation('description', $localeCode);
            }
            $this->selectedPermissions = $this->role->permissions->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->authorize('create', Role::class);
            foreach (array_keys($this->locales) as $localeCode) {
                $this->name[$localeCode] = '';
                $this->description[$localeCode] = '';
            }
        }
    }

    protected function rules(): array
    {
        $rules = [
            'selectedPermissions' => 'array',
        ];

        foreach (array_keys($this->locales) as $localeCode) {
            $rules["name.{$localeCode}"] = 'required|string|max:255';
            $rules["description.{$localeCode}"] = 'nullable|string|max:255';
        }

        return $rules;
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
                $roleService->updateRole($this->role, $data, $this->selectedPermissions);
                Flux::toast(text: __('Role updated successfully.'), variant: 'success');
            } else {
                $roleService->createRole($data, $this->selectedPermissions);
                Flux::toast(text: __('Role created successfully.'), variant: 'success');
            }

            $this->redirect(route('admin.roles.index'), navigate: true);
        } catch (\Exception $e) {
            Log::error('Failed to save role', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Flux::toast(text: __('Failed to save role. Please try again.'), variant: 'danger');
        }
    }

    public function render(PermissionService $permissionService): View
    {
        return view('livewire.admin.roles.manage-role', [
            'permissions' => $permissionService->all(),
        ]);
    }
} 
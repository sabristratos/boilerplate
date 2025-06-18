<?php

namespace App\Crud\Configurations;

use App\Crud\CrudConfigInterface;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleCrudConfig implements CrudConfigInterface
{
    public function getModelClass(): string
    {
        return Role::class;
    }

    public function getEntityName(): string
    {
        return 'Role';
    }

    public function getEntityPluralName(): string
    {
        return 'Roles';
    }

    public function getTableColumns(): array
    {
        return [
            [
                'label' => 'Name',
                'key' => 'name',
                'sortable' => true,
            ],
            [
                'label' => 'Description',
                'key' => 'description',
            ],
            [
                'label' => 'Languages',
                'key' => 'available_locales_as_string',
            ],
            [
                'label' => 'Permissions',
                'key' => 'permissions_count',
            ],
            [
                'label' => 'Created At',
                'key' => 'created_at',
                'sortable' => true,
            ],
        ];
    }

    public function getFormFields(): array
    {
        return [
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'required' => true,
                'translatable' => true,
                'column_span' => 2,
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'textarea',
                'translatable' => true,
                'column_span' => 2,
            ],
            [
                'name' => 'permissions',
                'label' => 'Permissions',
                'type' => 'multiselect',
                'relationship' => 'permissions',
                'options' => Permission::all()->pluck('name', 'id')->toArray(),
                'placeholder' => 'Select permissions...',
                'column_span' => 2,
            ],
        ];
    }

    public function getAttachableFields(): array
    {
        return [];
    }

    public function getValidationRules(Model $model, string $currentLocale): array
    {
        return [
            'name' => 'required|array',
            'name.' . $currentLocale => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'permissions' => 'nullable|array',
        ];
    }

    public function getEagerLoadRelations(): array
    {
        return ['permissions'];
    }

    public function getSearchableFields(): array
    {
        return ['name'];
    }

    public function getPermissionPrefix(): string
    {
        return 'roles';
    }

    public function getAlias(): string
    {
        return 'roles';
    }

    public function beforeSave(Model $model, array $data): Model
    {
        // Auto-generate the slug from the name.
        $defaultLocale = config('app.locale');
        if (isset($data['name'][$defaultLocale]) && !empty($data['name'][$defaultLocale])) {
            $model->slug = \Illuminate\Support\Str::slug($data['name'][$defaultLocale]);
        } elseif (isset($data['name']) && !empty(array_values($data['name'])[0])) {
            $model->slug = \Illuminate\Support\Str::slug(array_values($data['name'])[0]);
        }

        return $model;
    }

    public function getDefaultSortField(): string
    {
        return 'created_at';
    }

    public function getDefaultSortDirection(): string
    {
        return 'desc';
    }

    public function getAvailableLocales(): array
    {
        return config('app.available_locales', []);
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getActions(): array
    {
        return [];
    }
} 
<?php

namespace App\Crud\Configurations;

use App\Crud\CrudConfigInterface;
use App\Facades\Settings;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserStatus;
use App\Models\Role;

class UserCrudConfig implements CrudConfigInterface
{
    public function getModelClass(): string
    {
        return User::class;
    }

    public function getEntityName(): string
    {
        return 'User';
    }

    public function getEntityPluralName(): string
    {
        return 'Users';
    }

    public function getTableColumns(): array
    {
        return [
            [
                'label' => 'User',
                'key' => 'name',
                'render' => '<x-user-profile-cell :user="$item" />',
            ],
            [
                'label' => 'Status',
                'key' => 'status',
                'type' => 'badge',
                'colors' => [
                    UserStatus::Active->value => 'green',
                    UserStatus::Inactive->value => 'zinc',
                    UserStatus::Suspended->value => 'orange',
                ],
            ],
            [
                'label' => 'Roles',
                'key' => 'roles',
                'render' => '{{ $item->roles->pluck(\'name\')->implode(\', \') }}',
            ],
            [
                'label' => 'Verified',
                'key' => 'email_verified_at',
                'type' => 'badge',
                'colors' => [
                    true => 'sky',
                    false => 'zinc',
                ],
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
                'name' => 'avatar',
                'label' => __('Avatar'),
                'type' => 'circular',
                'collection' => 'avatars',
                'column_span' => 2,
            ],
            [
                'name' => 'name',
                'label' => __('Name'),
                'type' => 'text',
                'required' => true,
                'sortable' => true,
                'searchable' => true,
                'column_span' => 1,
            ],
            [
                'name' => 'email',
                'label' => __('Email'),
                'type' => 'email',
                'required' => true,
                'sortable' => true,
                'searchable' => true,
                'column_span' => 1,
            ],
            [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'placeholder' => 'Enter password',
                'persist' => false, // Do not persist this field directly
                'column_span' => 1,
            ],
            [
                'name' => 'password_confirmation',
                'label' => 'Confirm Password',
                'type' => 'password',
                'placeholder' => 'Confirm password',
                'persist' => false,
                'column_span' => 1,
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => UserStatus::asSelectArray(),
                'column_span' => 1,
            ],
            [
                'name' => 'roles',
                'label' => 'Roles',
                'type' => 'multiselect',
                'relationship' => 'roles',
                'options' => Role::all()->pluck('name', 'id')->toArray(),
                'placeholder' => __('Select Roles...'),
                'column_span' => 1,
            ]
        ];
    }

    public function getAttachableFields(): array
    {
        return [];
    }

    public function getValidationRules(Model $model): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $model->id,
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }

    public function getEagerLoadRelations(): array
    {
        return ['roles'];
    }

    public function getSearchableFields(): array
    {
        return ['name', 'email'];
    }

    public function getPermissionPrefix(): string
    {
        return 'users';
    }

    public function getAlias(): string
    {
        return 'users';
    }

    public function beforeSave(Model $model, array $data): Model
    {
        if (isset($data['password'])) {
            $model->password = Hash::make($data['password']);
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
        return [
            app()->getLocale() => config('app.available_locales.' . app()->getLocale()),
        ];
    }

    public function getFilters(): array
    {
        return [
            'status' => [
                'label' => 'Status',
                'type' => 'select',
                'options' => UserStatus::asSelectArray(),
            ],
            'roles' => [
                'label' => 'Role',
                'type' => 'select',
                'options' => Role::all()->pluck('name', 'id')->toArray(),
                'relationship' => true,
            ],
        ];
    }

    public function getActions(): array
    {
        return [
            [
                'type' => 'row_action',
                'label' => 'Impersonate',
                'icon' => 'user-circle',
                'method' => 'impersonateUser',
                'permission' => 'users.impersonate',
            ],
        ];
    }
} 
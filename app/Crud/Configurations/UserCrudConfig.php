<?php

namespace App\Crud\Configurations;

use App\Crud\CrudConfigInterface;
use App\Facades\Settings;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

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
            ['label' => 'Avatar', 'key' => 'avatar_url', 'type' => 'image'],
            ['label' => 'Name', 'key' => 'name', 'sortable' => true],
            ['label' => 'Email', 'key' => 'email', 'sortable' => true],
            [
                'label' => 'Status',
                'key' => 'status',
                'type' => 'badge',
                'colors' => [
                    'Active' => 'green',
                    'Inactive' => 'zinc',
                    'Suspended' => 'red',
                ]
            ],
        ];
    }

    public function getFormFields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'hint' => 'Leave empty to keep current password.'],
            ['name' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'persist' => false],
            ['name' => 'avatar', 'label' => 'Avatar', 'type' => 'file'],
        ];
    }

    public function getAttachableFields(): array
    {
        return [
            'avatar' => [],
        ];
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
        return [];
    }

    public function getSearchableFields(): array
    {
        return ['name', 'email'];
    }

    public function getPermissionPrefix(): string
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
                'options' => array_reduce(\App\Enums\UserStatus::cases(), function ($carry, $case) {
                    $carry[$case->value] = $case->getLabel();
                    return $carry;
                }, []),
            ],
        ];
    }
} 
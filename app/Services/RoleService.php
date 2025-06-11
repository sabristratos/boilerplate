<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\Role;

class RoleService
{
    public function createRole(array $data, array $selectedPermissions): Role
    {
        $role = Role::create($data);

        if (!empty($selectedPermissions)) {
            $role->permissions()->attach($selectedPermissions);
        }

        ActivityLogger::logCreated(
            $role,
            auth()->user(),
            [
                'name' => $role->name,
                'permissions' => $selectedPermissions,
            ],
            'role'
        );

        return $role;
    }

    public function updateRole(Role $role, array $data, array $selectedPermissions): Role
    {
        $oldValues = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('id')->map(fn ($id) => (string)$id)->toArray(),
        ];

        $role->update($data);

        $role->permissions()->sync($selectedPermissions);

        ActivityLogger::logUpdated(
            $role,
            auth()->user(),
            [
                'old' => $oldValues,
                'new' => [
                    'name' => $role->name,
                    'permissions' => $selectedPermissions,
                ],
            ],
            'role'
        );

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        ActivityLogger::logDeleted(
            $role,
            auth()->user(),
            [
                'name' => $role->name,
            ],
            'role'
        );

        $role->delete();
    }
} 
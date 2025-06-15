<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RoleService
{
    protected const CACHE_TTL = 3600; // 1 hour

    public function all(): Collection
    {
        return Cache::remember('roles.all', self::CACHE_TTL, function () {
            return Role::with('permissions')->get();
        });
    }

    public function find(int $id): ?Role
    {
        return Cache::remember("roles.{$id}", self::CACHE_TTL, function () use ($id) {
            return Role::with('permissions')->find($id);
        });
    }

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
                'name' => $role->getTranslations('name'),
                'permissions' => $selectedPermissions,
            ],
            'role'
        );

        $this->clearCache();

        return $role;
    }

    public function updateRole(Role $role, array $data, array $selectedPermissions): Role
    {
        $oldValues = [
            'name' => $role->getTranslations('name'),
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
                    'name' => $role->getTranslations('name'),
                    'permissions' => $selectedPermissions,
                ],
            ],
            'role'
        );

        $this->clearCache($role->id);

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        ActivityLogger::logDeleted(
            $role,
            auth()->user(),
            [
                'name' => $role->getTranslations('name'),
            ],
            'role'
        );

        $this->clearCache($role->id);
        $role->delete();
    }

    public function clearCache(?int $id = null): void
    {
        Cache::forget('roles.all');
        if ($id) {
            Cache::forget("roles.{$id}");
        }
    }
} 
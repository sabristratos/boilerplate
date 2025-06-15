<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-roles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $model): bool
    {
        return $user->hasPermission('view-roles');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-roles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $model): bool
    {
        if (!$user->hasPermission('edit-roles')) {
            return false;
        }

        // The 'is_system' flag protects system roles from being edited.
        if ($model->is_system) {
            return false;
        }

        // Super admin can edit any role except the super admin role
        if ($user->hasRole(config('roles.super_admin'))) {
            return $model->slug !== config('roles.super_admin');
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $model): bool
    {
        if (!$user->hasPermission('delete-roles')) {
            return false;
        }

        // The 'is_system' flag protects system roles from being deleted.
        if ($model->is_system) {
            return false;
        }

        // Super admin can delete any role except the super admin role
        if ($user->hasRole(config('roles.super_admin'))) {
            return $model->slug !== config('roles.super_admin');
        }

        return true;
    }
} 
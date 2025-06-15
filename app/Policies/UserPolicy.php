<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('view-users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if (!$user->hasPermission('edit-users')) {
            return false;
        }

        // Super admin can edit any user except other super admins
        if ($user->hasRole(config('roles.super_admin'))) {
            return !$model->hasRole(config('roles.super_admin'));
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if (!$user->hasPermission('delete-users')) {
            return false;
        }

        // Super admin can delete any user except other super admins
        if ($user->hasRole(config('roles.super_admin'))) {
            return !$model->hasRole(config('roles.super_admin'));
        }

        return false;
    }

    /**
     * Determine whether the user can assign roles.
     */
    public function assignRoles(User $user): bool
    {
        return $user->hasPermission('assign-roles');
    }

    /**
     * Determine whether the user can impersonate the model.
     */
    public function impersonate(User $user, User $model): bool
    {
        return $user->hasRole(config('roles.super_admin')) &&
               !$model->hasRole(config('roles.super_admin')) &&
               $user->id !== $model->id;
    }
} 
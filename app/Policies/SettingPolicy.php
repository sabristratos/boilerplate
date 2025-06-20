<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-settings');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Setting $model): bool
    {
        return $user->hasPermission('view-settings');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-settings');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Setting $model): bool
    {
        return $user->hasPermission('edit-settings');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Setting $model): bool
    {
        return $user->hasPermission('delete-settings');
    }
} 
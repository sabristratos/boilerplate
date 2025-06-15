<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-notifications');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DatabaseNotification $model): bool
    {
        return $user->hasPermission('view-notifications');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DatabaseNotification $model): bool
    {
        return $user->hasPermission('delete-notifications');
    }
} 
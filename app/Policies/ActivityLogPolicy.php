<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-activity-logs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityLog $model): bool
    {
        return $user->hasPermission('view-activity-logs');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityLog $model): bool
    {
        return $user->hasPermission('delete-activity-logs');
    }
} 
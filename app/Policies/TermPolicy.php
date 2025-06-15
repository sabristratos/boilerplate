<?php

namespace App\Policies;

use App\Models\Term;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-terms');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Term $model): bool
    {
        return $user->hasPermission('view-terms');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-terms');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Term $model): bool
    {
        return $user->hasPermission('edit-terms');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Term $model): bool
    {
        return $user->hasPermission('delete-terms');
    }
} 
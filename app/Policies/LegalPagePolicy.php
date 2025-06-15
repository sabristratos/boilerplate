<?php

namespace App\Policies;

use App\Models\LegalPage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LegalPagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-legal-pages');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegalPage $model): bool
    {
        return $user->hasPermission('view-legal-pages');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-legal-pages');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LegalPage $model): bool
    {
        return $user->hasPermission('edit-legal-pages');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LegalPage $model): bool
    {
        return $user->hasPermission('delete-legal-pages');
    }
} 
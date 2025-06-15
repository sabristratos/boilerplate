<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-taxonomies');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Taxonomy $model): bool
    {
        return $user->hasPermission('view-taxonomies');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-taxonomies');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Taxonomy $model): bool
    {
        return $user->hasPermission('edit-taxonomies');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Taxonomy $model): bool
    {
        return $user->hasPermission('delete-taxonomies');
    }
} 
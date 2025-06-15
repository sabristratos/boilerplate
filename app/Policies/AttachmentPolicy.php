<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-attachments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attachment $model): bool
    {
        return $user->hasPermission('view-attachments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-attachments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attachment $model): bool
    {
        return $user->hasPermission('edit-attachments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attachment $model): bool
    {
        return $user->hasPermission('delete-attachments');
    }
} 
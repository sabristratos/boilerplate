<?php

namespace App\Interfaces;

interface HasPermissions
{
    /**
     * The permissions that belong to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * Check if the model has the given permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool;
}

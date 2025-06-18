<?php

namespace App\Interfaces;

interface HasRoles
{
    /**
     * The roles that belong to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * Check if the model has the given role.
     */
    public function hasRole(string $role): bool;

    /**
     * Check if the model has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool;

    /**
     * Check if the model has all of the given roles.
     */
    public function hasAllRoles(array $roles): bool;

    /**
     * Check if the model has the given permission through any of their roles.
     */
    public function hasPermission(string $permission): bool;

    /**
     * Check if the model has any of the given permissions through any of their roles.
     */
    public function hasAnyPermission(array $permissions): bool;

    /**
     * Check if the model has all of the given permissions through any of their roles.
     */
    public function hasAllPermissions(array $permissions): bool;
}

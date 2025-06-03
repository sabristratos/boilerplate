<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_assigned_a_role()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a role
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'A role for testing',
        ]);

        // Assign the role to the user
        $user->roles()->attach($role);

        // Check if the user has the role
        $this->assertTrue($user->hasRole('test-role'));
    }

    public function test_role_can_be_assigned_a_permission()
    {
        // Create a role
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'A role for testing',
        ]);

        // Create a permission
        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test-permission',
            'description' => 'A permission for testing',
        ]);

        // Assign the permission to the role
        $role->permissions()->attach($permission);

        // Check if the role has the permission
        $this->assertTrue($role->hasPermission('test-permission'));
    }

    public function test_user_can_have_permission_through_role()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a role
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'A role for testing',
        ]);

        // Create a permission
        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test-permission',
            'description' => 'A permission for testing',
        ]);

        // Assign the permission to the role
        $role->permissions()->attach($permission);

        // Assign the role to the user
        $user->roles()->attach($role);

        // Check if the user has the permission through the role
        $this->assertTrue($user->hasPermission('test-permission'));
    }
}

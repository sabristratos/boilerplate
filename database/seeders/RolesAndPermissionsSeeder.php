<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permission_role')->truncate();
        DB::table('role_user')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create roles
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full access to all system features',
        ]);

        $editorRole = Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Can edit content but has limited administrative access',
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'Standard user with basic access',
        ]);

        // Create permissions
        // User management permissions
        $viewUsersPermission = Permission::create([
            'name' => 'View Users',
            'slug' => 'view-users',
            'description' => 'Can view user listings',
        ]);

        $createUsersPermission = Permission::create([
            'name' => 'Create Users',
            'slug' => 'create-users',
            'description' => 'Can create new users',
        ]);

        $editUsersPermission = Permission::create([
            'name' => 'Edit Users',
            'slug' => 'edit-users',
            'description' => 'Can edit existing users',
        ]);

        $deleteUsersPermission = Permission::create([
            'name' => 'Delete Users',
            'slug' => 'delete-users',
            'description' => 'Can delete users',
        ]);

        // Role management permissions
        $viewRolesPermission = Permission::create([
            'name' => 'View Roles',
            'slug' => 'view-roles',
            'description' => 'Can view role listings',
        ]);

        $createRolesPermission = Permission::create([
            'name' => 'Create Roles',
            'slug' => 'create-roles',
            'description' => 'Can create new roles',
        ]);

        $editRolesPermission = Permission::create([
            'name' => 'Edit Roles',
            'slug' => 'edit-roles',
            'description' => 'Can edit existing roles',
        ]);

        $deleteRolesPermission = Permission::create([
            'name' => 'Delete Roles',
            'slug' => 'delete-roles',
            'description' => 'Can delete roles',
        ]);

        // Content management permissions
        $viewContentPermission = Permission::create([
            'name' => 'View Content',
            'slug' => 'view-content',
            'description' => 'Can view content',
        ]);

        $createContentPermission = Permission::create([
            'name' => 'Create Content',
            'slug' => 'create-content',
            'description' => 'Can create new content',
        ]);

        $editContentPermission = Permission::create([
            'name' => 'Edit Content',
            'slug' => 'edit-content',
            'description' => 'Can edit existing content',
        ]);

        $deleteContentPermission = Permission::create([
            'name' => 'Delete Content',
            'slug' => 'delete-content',
            'description' => 'Can delete content',
        ]);

        // Settings permissions
        $viewSettingsPermission = Permission::create([
            'name' => 'View Settings',
            'slug' => 'view-settings',
            'description' => 'Can view system settings',
        ]);

        $editSettingsPermission = Permission::create([
            'name' => 'Edit Settings',
            'slug' => 'edit-settings',
            'description' => 'Can edit system settings',
        ]);

        // Assign permissions to roles
        // Admin role gets all permissions
        $adminPermissions = Permission::all();
        $adminRole->permissions()->attach($adminPermissions);

        // Editor role gets content management permissions and view permissions
        $editorRole->permissions()->attach([
            $viewUsersPermission->id,
            $viewRolesPermission->id,
            $viewContentPermission->id,
            $createContentPermission->id,
            $editContentPermission->id,
            $deleteContentPermission->id,
            $viewSettingsPermission->id,
        ]);

        // User role gets basic view permissions
        $userRole->permissions()->attach([
            $viewContentPermission->id,
        ]);

        // Assign admin role to user ID 1 if it exists
        if ($user = \App\Models\User::find(1)) {
            $user->roles()->attach($adminRole);
        }
    }
}

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

        $assignRolesPermission = Permission::create([
            'name' => 'Assign Roles',
            'slug' => 'assign-roles',
            'description' => 'Can assign roles to users',
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

        // Taxonomy management permissions
        $viewTaxonomiesPermission = Permission::create([
            'name' => 'View Taxonomies',
            'slug' => 'view-taxonomies',
            'description' => 'Can view taxonomy listings',
        ]);

        $createTaxonomiesPermission = Permission::create([
            'name' => 'Create Taxonomies',
            'slug' => 'create-taxonomies',
            'description' => 'Can create new taxonomies',
        ]);

        $editTaxonomiesPermission = Permission::create([
            'name' => 'Edit Taxonomies',
            'slug' => 'edit-taxonomies',
            'description' => 'Can edit existing taxonomies',
        ]);

        $deleteTaxonomiesPermission = Permission::create([
            'name' => 'Delete Taxonomies',
            'slug' => 'delete-taxonomies',
            'description' => 'Can delete taxonomies',
        ]);

        // Term management permissions
        $viewTermsPermission = Permission::create([
            'name' => 'View Terms',
            'slug' => 'view-terms',
            'description' => 'Can view term listings',
        ]);

        $createTermsPermission = Permission::create([
            'name' => 'Create Terms',
            'slug' => 'create-terms',
            'description' => 'Can create new terms',
        ]);

        $editTermsPermission = Permission::create([
            'name' => 'Edit Terms',
            'slug' => 'edit-terms',
            'description' => 'Can edit existing terms',
        ]);

        $deleteTermsPermission = Permission::create([
            'name' => 'Delete Terms',
            'slug' => 'delete-terms',
            'description' => 'Can delete terms',
        ]);

        // Attachment management permissions
        $viewAttachmentsPermission = Permission::create([
            'name' => 'View Attachments',
            'slug' => 'view-attachments',
            'description' => 'Can view attachments',
        ]);

        $deleteAttachmentsPermission = Permission::create([
            'name' => 'Delete Attachments',
            'slug' => 'delete-attachments',
            'description' => 'Can delete attachments',
        ]);

        // Activity Log permissions
        $viewActivityLogsPermission = Permission::create([
            'name' => 'View Activity Logs',
            'slug' => 'view-activity-logs',
            'description' => 'Can view activity logs',
        ]);

        $deleteActivityLogsPermission = Permission::create([
            'name' => 'Delete Activity Logs',
            'slug' => 'delete-activity-logs',
            'description' => 'Can delete activity logs',
        ]);

        // Notification management permissions
        $viewNotificationsPermission = Permission::create([
            'name' => 'View Notifications',
            'slug' => 'view-notifications',
            'description' => 'Can view notifications',
        ]);

        $createNotificationsPermission = Permission::create([
            'name' => 'Create Notifications',
            'slug' => 'create-notifications',
            'description' => 'Can create notifications',
        ]);

        $deleteNotificationsPermission = Permission::create([
            'name' => 'Delete Notifications',
            'slug' => 'delete-notifications',
            'description' => 'Can delete notifications',
        ]);

        // Legal Page management permissions
        $viewLegalPagesPermission = Permission::create([
            'name' => 'View Legal Pages',
            'slug' => 'view-legal-pages',
            'description' => 'Can view legal pages',
        ]);

        $createLegalPagesPermission = Permission::create([
            'name' => 'Create Legal Pages',
            'slug' => 'create-legal-pages',
            'description' => 'Can create legal pages',
        ]);

        $editLegalPagesPermission = Permission::create([
            'name' => 'Edit Legal Pages',
            'slug' => 'edit-legal-pages',
            'description' => 'Can edit legal pages',
        ]);

        $deleteLegalPagesPermission = Permission::create([
            'name' => 'Delete Legal Pages',
            'slug' => 'delete-legal-pages',
            'description' => 'Can delete legal pages',
        ]);

        // Dashboard permissions
        $viewDashboardPermission = Permission::create([
            'name' => 'View Dashboard',
            'slug' => 'view-dashboard',
            'description' => 'Can view the admin dashboard',
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
            $viewDashboardPermission->id,
            $viewUsersPermission->id,
            $viewRolesPermission->id,
            $viewTaxonomiesPermission->id,
            $createTaxonomiesPermission->id,
            $editTaxonomiesPermission->id,
            $deleteTaxonomiesPermission->id,
            $viewTermsPermission->id,
            $createTermsPermission->id,
            $editTermsPermission->id,
            $deleteTermsPermission->id,
            $viewLegalPagesPermission->id,
            $createLegalPagesPermission->id,
            $editLegalPagesPermission->id,
            $deleteLegalPagesPermission->id,
            $viewSettingsPermission->id,
        ]);

        // User role gets basic view permissions
        $userRole->permissions()->attach([
            // Basic permissions for a standard user
        ]);

        // Assign admin role to user ID 1 if it exists
        if ($user = \App\Models\User::find(1)) {
            $user->roles()->attach($adminRole);
        }
    }
}

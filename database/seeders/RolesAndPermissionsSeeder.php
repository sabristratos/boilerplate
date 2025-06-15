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
            'name' => [
                'en' => 'Administrator',
                'fr' => 'Administrateur',
                'es' => 'Administrador',
            ],
            'slug' => config('roles.super_admin'),
            'description' => [
                'en' => 'Full access to all system features',
                'fr' => 'Accès complet à toutes les fonctionnalités du système',
                'es' => 'Acceso completo a todas las funciones del sistema',
            ],
            'is_system' => true,
        ]);

        $editorRole = Role::create([
            'name' => [
                'en' => 'Editor',
                'fr' => 'Éditeur',
                'es' => 'Editor',
            ],
            'slug' => 'editor',
            'description' => [
                'en' => 'Can edit content but has limited administrative access',
                'fr' => 'Peut modifier le contenu mais a un accès administratif limité',
                'es' => 'Puede editar contenido pero tiene acceso administrativo limitado',
            ],
            'is_system' => true,
        ]);

        $userRole = Role::create([
            'name' => [
                'en' => 'User',
                'fr' => 'Utilisateur',
                'es' => 'Usuario',
            ],
            'slug' => 'user',
            'description' => [
                'en' => 'Standard user with basic access',
                'fr' => 'Utilisateur standard avec accès de base',
                'es' => 'Usuario estándar con acceso básico',
            ],
            'is_system' => true,
        ]);

        // Create permissions
        // User management permissions
        $viewUsersPermission = Permission::create([
            'name' => [
                'en' => 'View Users',
                'fr' => 'Voir les Utilisateurs',
                'es' => 'Ver Usuarios',
            ],
            'slug' => 'view-users',
            'description' => [
                'en' => 'Can view user listings',
                'fr' => 'Peut voir la liste des utilisateurs',
                'es' => 'Puede ver listados de usuarios',
            ],
        ]);

        $createUsersPermission = Permission::create([
            'name' => [
                'en' => 'Create Users',
                'fr' => 'Créer des Utilisateurs',
                'es' => 'Crear Usuarios',
            ],
            'slug' => 'create-users',
            'description' => [
                'en' => 'Can create new users',
                'fr' => 'Peut créer de nouveaux utilisateurs',
                'es' => 'Puede crear nuevos usuarios',
            ],
        ]);

        $editUsersPermission = Permission::create([
            'name' => [
                'en' => 'Edit Users',
                'fr' => 'Modifier les Utilisateurs',
                'es' => 'Editar Usuarios',
            ],
            'slug' => 'edit-users',
            'description' => [
                'en' => 'Can edit existing users',
                'fr' => 'Peut modifier les utilisateurs existants',
                'es' => 'Puede editar usuarios existentes',
            ],
        ]);

        $deleteUsersPermission = Permission::create([
            'name' => [
                'en' => 'Delete Users',
                'fr' => 'Supprimer des Utilisateurs',
                'es' => 'Eliminar Usuarios',
            ],
            'slug' => 'delete-users',
            'description' => [
                'en' => 'Can delete users',
                'fr' => 'Peut supprimer des utilisateurs',
                'es' => 'Puede eliminar usuarios',
            ],
        ]);

        $assignRolesPermission = Permission::create([
            'name' => [
                'en' => 'Assign Roles',
                'fr' => 'Attribuer des Rôles',
                'es' => 'Asignar Roles',
            ],
            'slug' => 'assign-roles',
            'description' => [
                'en' => 'Can assign roles to users',
                'fr' => 'Peut attribuer des rôles aux utilisateurs',
                'es' => 'Puede asignar roles a usuarios',
            ],
        ]);

        // Role management permissions
        $viewRolesPermission = Permission::create([
            'name' => [
                'en' => 'View Roles',
                'fr' => 'Voir les Rôles',
                'es' => 'Ver Roles',
            ],
            'slug' => 'view-roles',
            'description' => [
                'en' => 'Can view role listings',
                'fr' => 'Peut voir la liste des rôles',
                'es' => 'Puede ver listados de roles',
            ],
        ]);

        $createRolesPermission = Permission::create([
            'name' => [
                'en' => 'Create Roles',
                'fr' => 'Créer des Rôles',
                'es' => 'Crear Roles',
            ],
            'slug' => 'create-roles',
            'description' => [
                'en' => 'Can create new roles',
                'fr' => 'Peut créer de nouveaux rôles',
                'es' => 'Puede crear nuevos roles',
            ],
        ]);

        $editRolesPermission = Permission::create([
            'name' => [
                'en' => 'Edit Roles',
                'fr' => 'Modifier les Rôles',
                'es' => 'Editar Roles',
            ],
            'slug' => 'edit-roles',
            'description' => [
                'en' => 'Can edit existing roles',
                'fr' => 'Peut modifier les rôles existants',
                'es' => 'Puede editar roles existentes',
            ],
        ]);

        $deleteRolesPermission = Permission::create([
            'name' => [
                'en' => 'Delete Roles',
                'fr' => 'Supprimer des Rôles',
                'es' => 'Eliminar Roles',
            ],
            'slug' => 'delete-roles',
            'description' => [
                'en' => 'Can delete roles',
                'fr' => 'Peut supprimer des rôles',
                'es' => 'Puede eliminar roles',
            ],
        ]);

        // Taxonomy management permissions
        $viewTaxonomiesPermission = Permission::create([
            'name' => [
                'en' => 'View Taxonomies',
                'fr' => 'Voir les Taxonomies',
                'es' => 'Ver Taxonomías',
            ],
            'slug' => 'view-taxonomies',
            'description' => [
                'en' => 'Can view taxonomy listings',
                'fr' => 'Peut voir la liste des taxonomies',
                'es' => 'Puede ver listados de taxonomías',
            ],
        ]);

        $createTaxonomiesPermission = Permission::create([
            'name' => [
                'en' => 'Create Taxonomies',
                'fr' => 'Créer des Taxonomies',
                'es' => 'Crear Taxonomías',
            ],
            'slug' => 'create-taxonomies',
            'description' => [
                'en' => 'Can create new taxonomies',
                'fr' => 'Peut créer de nouvelles taxonomies',
                'es' => 'Puede crear nuevas taxonomías',
            ],
        ]);

        $editTaxonomiesPermission = Permission::create([
            'name' => [
                'en' => 'Edit Taxonomies',
                'fr' => 'Modifier les Taxonomies',
                'es' => 'Editar Taxonomías',
            ],
            'slug' => 'edit-taxonomies',
            'description' => [
                'en' => 'Can edit existing taxonomies',
                'fr' => 'Peut modifier les taxonomies existantes',
                'es' => 'Puede editar taxonomías existentes',
            ],
        ]);

        $deleteTaxonomiesPermission = Permission::create([
            'name' => [
                'en' => 'Delete Taxonomies',
                'fr' => 'Supprimer des Taxonomies',
                'es' => 'Eliminar Taxonomías',
            ],
            'slug' => 'delete-taxonomies',
            'description' => [
                'en' => 'Can delete taxonomies',
                'fr' => 'Peut supprimer des taxonomies',
                'es' => 'Puede eliminar taxonomías',
            ],
        ]);

        // Term management permissions
        $viewTermsPermission = Permission::create([
            'name' => [
                'en' => 'View Terms',
                'fr' => 'Voir les Termes',
                'es' => 'Ver Términos',
            ],
            'slug' => 'view-terms',
            'description' => [
                'en' => 'Can view term listings',
                'fr' => 'Peut voir la liste des termes',
                'es' => 'Puede ver listados de términos',
            ],
        ]);

        $createTermsPermission = Permission::create([
            'name' => [
                'en' => 'Create Terms',
                'fr' => 'Créer des Termes',
                'es' => 'Crear Términos',
            ],
            'slug' => 'create-terms',
            'description' => [
                'en' => 'Can create new terms',
                'fr' => 'Peut créer de nouveaux termes',
                'es' => 'Puede crear nuevos términos',
            ],
        ]);

        $editTermsPermission = Permission::create([
            'name' => [
                'en' => 'Edit Terms',
                'fr' => 'Modifier les Termes',
                'es' => 'Editar Términos',
            ],
            'slug' => 'edit-terms',
            'description' => [
                'en' => 'Can edit existing terms',
                'fr' => 'Peut modifier les termes existants',
                'es' => 'Puede editar términos existentes',
            ],
        ]);

        $deleteTermsPermission = Permission::create([
            'name' => [
                'en' => 'Delete Terms',
                'fr' => 'Supprimer des Termes',
                'es' => 'Eliminar Términos',
            ],
            'slug' => 'delete-terms',
            'description' => [
                'en' => 'Can delete terms',
                'fr' => 'Peut supprimer des termes',
                'es' => 'Puede eliminar términos',
            ],
        ]);

        // Attachment management permissions
        $viewAttachmentsPermission = Permission::create([
            'name' => [
                'en' => 'View Attachments',
                'fr' => 'Voir les Pièces Jointes',
                'es' => 'Ver Archivos Adjuntos',
            ],
            'slug' => 'view-attachments',
            'description' => [
                'en' => 'Can view attachments',
                'fr' => 'Peut voir les pièces jointes',
                'es' => 'Puede ver archivos adjuntos',
            ],
        ]);

        $deleteAttachmentsPermission = Permission::create([
            'name' => [
                'en' => 'Delete Attachments',
                'fr' => 'Supprimer des Pièces Jointes',
                'es' => 'Eliminar Archivos Adjuntos',
            ],
            'slug' => 'delete-attachments',
            'description' => [
                'en' => 'Can delete attachments',
                'fr' => 'Peut supprimer des pièces jointes',
                'es' => 'Puede eliminar archivos adjuntos',
            ],
        ]);

        // Activity Log permissions
        $viewActivityLogsPermission = Permission::create([
            'name' => [
                'en' => 'View Activity Logs',
                'fr' => 'Voir les Journaux d\'Activité',
                'es' => 'Ver Registros de Actividad',
            ],
            'slug' => 'view-activity-logs',
            'description' => [
                'en' => 'Can view activity logs',
                'fr' => 'Peut voir les journaux d\'activité',
                'es' => 'Puede ver registros de actividad',
            ],
        ]);

        $deleteActivityLogsPermission = Permission::create([
            'name' => [
                'en' => 'Delete Activity Logs',
                'fr' => 'Supprimer des Journaux d\'Activité',
                'es' => 'Eliminar Registros de Actividad',
            ],
            'slug' => 'delete-activity-logs',
            'description' => [
                'en' => 'Can delete activity logs',
                'fr' => 'Peut supprimer des journaux d\'activité',
                'es' => 'Puede eliminar registros de actividad',
            ],
        ]);

        // Notification management permissions
        $viewNotificationsPermission = Permission::create([
            'name' => [
                'en' => 'View Notifications',
                'fr' => 'Voir les Notifications',
                'es' => 'Ver Notificaciones',
            ],
            'slug' => 'view-notifications',
            'description' => [
                'en' => 'Can view notifications',
                'fr' => 'Peut voir les notifications',
                'es' => 'Puede ver notificaciones',
            ],
        ]);

        $createNotificationsPermission = Permission::create([
            'name' => [
                'en' => 'Create Notifications',
                'fr' => 'Créer des Notifications',
                'es' => 'Crear Notificaciones',
            ],
            'slug' => 'create-notifications',
            'description' => [
                'en' => 'Can create notifications',
                'fr' => 'Peut créer des notifications',
                'es' => 'Puede crear notificaciones',
            ],
        ]);

        $deleteNotificationsPermission = Permission::create([
            'name' => [
                'en' => 'Delete Notifications',
                'fr' => 'Supprimer des Notifications',
                'es' => 'Eliminar Notificaciones',
            ],
            'slug' => 'delete-notifications',
            'description' => [
                'en' => 'Can delete notifications',
                'fr' => 'Peut supprimer des notifications',
                'es' => 'Puede eliminar notificaciones',
            ],
        ]);

        // Legal Page management permissions
        $viewLegalPagesPermission = Permission::create([
            'name' => [
                'en' => 'View Legal Pages',
                'fr' => 'Voir les Pages Légales',
                'es' => 'Ver Páginas Legales',
            ],
            'slug' => 'view-legal-pages',
            'description' => [
                'en' => 'Can view legal pages',
                'fr' => 'Peut voir les pages légales',
                'es' => 'Puede ver páginas legales',
            ],
        ]);

        $createLegalPagesPermission = Permission::create([
            'name' => [
                'en' => 'Create Legal Pages',
                'fr' => 'Créer des Pages Légales',
                'es' => 'Crear Páginas Legales',
            ],
            'slug' => 'create-legal-pages',
            'description' => [
                'en' => 'Can create legal pages',
                'fr' => 'Peut créer des pages légales',
                'es' => 'Puede crear páginas legales',
            ],
        ]);

        $editLegalPagesPermission = Permission::create([
            'name' => [
                'en' => 'Edit Legal Pages',
                'fr' => 'Modifier les Pages Légales',
                'es' => 'Editar Páginas Legales',
            ],
            'slug' => 'edit-legal-pages',
            'description' => [
                'en' => 'Can edit legal pages',
                'fr' => 'Peut modifier les pages légales',
                'es' => 'Puede editar páginas legales',
            ],
        ]);

        $deleteLegalPagesPermission = Permission::create([
            'name' => [
                'en' => 'Delete Legal Pages',
                'fr' => 'Supprimer des Pages Légales',
                'es' => 'Eliminar Páginas Legales',
            ],
            'slug' => 'delete-legal-pages',
            'description' => [
                'en' => 'Can delete legal pages',
                'fr' => 'Peut supprimer des pages légales',
                'es' => 'Puede eliminar páginas legales',
            ],
        ]);

        // Dashboard permissions
        $viewDashboardPermission = Permission::create([
            'name' => [
                'en' => 'View Dashboard',
                'fr' => 'Voir le Tableau de Bord',
                'es' => 'Ver Panel de Control',
            ],
            'slug' => 'view-dashboard',
            'description' => [
                'en' => 'Can view the admin dashboard',
                'fr' => 'Peut voir le tableau de bord d\'administration',
                'es' => 'Puede ver el panel de control de administración',
            ],
        ]);

        // Settings permissions
        $viewSettingsPermission = Permission::create([
            'name' => [
                'en' => 'View Settings',
                'fr' => 'Voir les Paramètres',
                'es' => 'Ver Configuración',
            ],
            'slug' => 'view-settings',
            'description' => [
                'en' => 'Can view system settings',
                'fr' => 'Peut voir les paramètres du système',
                'es' => 'Puede ver la configuración del sistema',
            ],
        ]);

        $editSettingsPermission = Permission::create([
            'name' => [
                'en' => 'Edit Settings',
                'fr' => 'Modifier les Paramètres',
                'es' => 'Editar Configuración',
            ],
            'slug' => 'edit-settings',
            'description' => [
                'en' => 'Can edit system settings',
                'fr' => 'Peut modifier les paramètres du système',
                'es' => 'Puede editar la configuración del sistema',
            ],
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

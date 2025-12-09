<?php

namespace App\Helpers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionHelper
{
    /**
     * Get all module permissions
     * 
     * @return array
     */
    public static function getModulePermissions()
    {
        return [
            'dashboard' => [
                'view-dashboard',
            ],
            'categories' => [
                'view-categories',
                'create-categories',
                'edit-categories',
                'delete-categories',
            ],
            'invitations' => [
                'view-invitations',
                'create-invitations',
                'edit-invitations',
                'delete-invitations',
                'approve-invitations',
            ],
            'invitation-requests' => [
                'view-invitation-requests',
                'approve-invitation-requests',
                'reject-invitation-requests',
            ],
            'users' => [
                'view-users',
                'edit-users',
                'delete-users',
                'suspend-users',
            ],
            'admins' => [
                'view-admins',
                'create-admins',
                'edit-admins',
                'delete-admins',
            ],
            'packages' => [
                'view-packages',
                'create-packages',
                'edit-packages',
                'delete-packages',
            ],
            'promo-codes' => [
                'view-promo-codes',
                'create-promo-codes',
                'edit-promo-codes',
                'delete-promo-codes',
            ],
            'financial' => [
                'view-financial',
                'export-financial',
            ],
            'notifications' => [
                'view-notifications',
                'create-notifications',
                'edit-notifications',
                'delete-notifications',
            ],
            'contact-us' => [
                'view-contact-us',
                'reply-contact-us',
                'delete-contact-us',
            ],
            'app-settings' => [
                'view-app-settings',
                'edit-app-settings',
            ],
            'roles' => [
                'view-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
            ],
            'permissions' => [
                'view-permissions',
                'create-permissions',
                'edit-permissions',
                'delete-permissions',
            ],
        ];
    }

    /**
     * Sync permissions to database
     * 
     * @return void
     */
    public static function syncPermissions()
    {
        $permissions = self::getModulePermissions();
        
        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permissionName) {
                Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'admin'],
                    ['name' => $permissionName, 'guard_name' => 'admin']
                );
            }
        }
    }

    /**
     * Create default roles
     * 
     * @return void
     */
    public static function createDefaultRoles()
    {
        // Create Super Admin role with all permissions
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'admin'],
            ['name' => 'Super Admin', 'guard_name' => 'admin']
        );
        
        $allPermissions = Permission::where('guard_name', 'admin')->pluck('id');
        $superAdmin->syncPermissions($allPermissions);
    }
}













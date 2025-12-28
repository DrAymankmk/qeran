<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\PermissionHelper;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sync permissions
        PermissionHelper::syncPermissions();
        
        // Create default roles
        PermissionHelper::createDefaultRoles();
        
        
        $this->command->info('Roles and Permissions seeded successfully!');
    }
}





































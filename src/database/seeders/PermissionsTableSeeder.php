<?php

namespace Mabrouk\RolePermissionGroup\Database\Seeders;

use Illuminate\Database\Seeder;
use Mabrouk\RolePermissionGroup\Helpers\RouteInvestigator;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $investigator = new RouteInvestigator();
        $permissions = $investigator->createPermissions()
            ->map(function ($permission) {
                $permission->translate([
                    'display_name' => $permission->name,
                ], 'en');
                return $permission->refresh();
            });
    }
}

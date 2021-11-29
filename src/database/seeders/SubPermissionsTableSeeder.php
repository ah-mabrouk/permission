<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mabrouk\Permission\Models\Permission;
use Mabrouk\Permission\Models\SubPermission;
use Mabrouk\Permission\Helpers\RouteInvestigator;

class SubPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $investigator = new RouteInvestigator();
        $permissions = Permission::all()->each(function ($permission) use ($investigator) {
            $investigator->createSubPermissionsOf($permission)->each(function ($subPermission) {
                $subPermissionObject = SubPermission::create($subPermission)->translate([
                    'display_name' => 'translated name',
                ]);
            });
        });
    }
}

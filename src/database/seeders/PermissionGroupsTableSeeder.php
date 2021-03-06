<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mabrouk\Permission\Models\PermissionGroup;
use Mabrouk\Permission\Models\PermissionGroupTranslation;

class PermissionGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionGroups = [
            [
                'name' => 'Basic Permissions',
                'ar_name' => 'الصلاحيات الأساسية',
            ],
            [
                'name' => 'Other Permissions',
                'ar_name' => 'صلاحيات أخرى',
            ],
        ];

        $currentPermissionGroupsInTable = PermissionGroupTranslation::pluck('name')->flatten()->toArray();

        for ($i = 0; $i < \count($permissionGroups); $i++) {
            if (! \in_array($permissionGroups[$i]['name'], $currentPermissionGroupsInTable)) {
                $permissionGroup = PermissionGroup::create([])->translate([
                    'name' => $permissionGroups[$i]['name'],
                ], 'en');
                if (\array_key_exists('ar_name', $permissionGroups[$i])) {
                    $permissionGroup->translate([
                        'name' => $permissionGroups[$i]['ar_name'],
                    ], 'ar');
                }
            }
        }
    }
}

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
        $permissionGroups = array_merge_recursive(
            config('permissions.default_permission_group'),
            config('permissions.additional_permission_groups')
        );

        $currentPermissionGroupsInTable = PermissionGroupTranslation::pluck('name')->flatten()->toArray();

        foreach ($permissionGroups['en'] as $index => $name) {
            if (!\in_array($name, $currentPermissionGroupsInTable)) {
                $permissionGroup = PermissionGroup::create([])->translate(['name' => $name], 'en');
                if (isset($permissionGroups['ar'][$index])) {
                    $permissionGroup->translate(['name' => $permissionGroups['ar'][$index]], 'ar');
                }
            }
        }
    }
}

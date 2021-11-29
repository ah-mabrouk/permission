<?php

namespace Database\Seeders;

use App\Models\RoleTranslation;
use Illuminate\Database\Seeder;
use Mabrouk\RolePermissionGroup\Models\Role;
use Mabrouk\RolePermissionGroup\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Super Admin Role',
                'ar_name' => 'دور المشرف العام',
            ],
        ];

        $currentRolesInTable = RoleTranslation::all()->flatten()->pluck('name')->toArray();

        for ($i = 0; $i < \count($roles); $i++) {
            if (! \in_array($roles[$i]['name'], $currentRolesInTable)) {
                $role = Role::create([]);
                if ($roles[$i]['name'] == 'Super Admin Role') {
                    $role->update(['id' => 0]);
                }
                $role->refresh()->translate([
                    'name' => $roles[$i]['name'],
                ], 'en');
                if (\array_key_exists('ar_name', $roles[$i])) {
                    $role->translate([
                        'name' => $roles[$i]['ar_name'],
                    ], 'ar');
                }
            }
        }
        $this->assignOwnerRole();
    }

    private function assignOwnerRole()
    {
        $role = Role::find(0);
        $ownerIdentifier = config('roleable.project_owner_id');
        $model = config('roleable.project_owner_model');
        $owner = $model::find($ownerIdentifier);
        $this->giveOwnerRoleAllPermissions($role);
        if ((bool) $owner) {
            $owner->takeRole($role);
        }
    }

    private function giveOwnerRoleAllPermissions(Role $role)
    {
        $permissions = Permission::all();
        $permissionsIdsWithSubPermissions = [];
        for ($i = 0; $i < \count($permissions); $i++) {
            $permissionsIdsWithSubPermissions[$permissions[$i]->id] = $permissions[$i]->subPermissions->pluck('id')->flatten()->toArray();
        }
        $role->syncPermissions($permissionsIdsWithSubPermissions);
    }
}

<?php

namespace Mabrouk\RolePermissionGroup\Database\Seeders;

use Illuminate\Database\Seeder;

class RoleableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionGroupsTableSeeder::class,
            PermissionsTableSeeder::class,
            SubPermissionsTableSeeder::class,
            RolesTableSeeder::class,
         ]);
    }
}

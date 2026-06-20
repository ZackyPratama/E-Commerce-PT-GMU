<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'ViewAny:Order',
            'View:Order',
            'ViewAny:Product',
            'View:Product',
            'ViewAny:RFQ',
            'View:RFQ',
            'ViewAny:Customer',
            'View:Customer',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $owner->givePermissionTo($permissions);

        $this->command->info('Role owner and permissions created successfully.');
    }
}

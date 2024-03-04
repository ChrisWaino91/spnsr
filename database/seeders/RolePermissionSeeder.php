<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        $viewSuppliers = Permission::create(['name' => 'view suppliers']);
        $viewBrands = Permission::create(['name' => 'view brands']);
        $viewData = Permission::create(['name' => 'view all data']);

        // Assign permissions to roles
        $adminRole->givePermissionTo($viewSuppliers);
        $adminRole->givePermissionTo($viewBrands);
        $adminRole->givePermissionTo($viewData);
    }
}

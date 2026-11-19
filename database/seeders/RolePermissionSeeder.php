<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        $permissions = collect([
            'manage_employees',
            'manage_attendance',
            'manage_leaves',
            'manage_payroll',
            'view_reports',
        ])->mapWithKeys(fn (string $name): array => [
            $name => Permission::firstOrCreate(['name' => $name]),
        ]);

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->permissions()->syncWithoutDetaching($permissions->pluck('id')->all());

        $hrManager = Role::firstOrCreate(['name' => 'HR Manager']);
        $hrManager->permissions()->syncWithoutDetaching([
            $permissions['manage_employees']->id,
            $permissions['manage_attendance']->id,
            $permissions['manage_leaves']->id,
            $permissions['view_reports']->id,
        ]);
    }
}

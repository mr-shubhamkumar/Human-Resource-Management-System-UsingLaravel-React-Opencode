<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with realistic HRM demo data.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            AttendanceSeeder::class,
            LeaveSeeder::class,
            PayrollSeeder::class,
            PayslipSeeder::class,
            NotificationSeeder::class,
            ActivityLogSeeder::class,
            AnnouncementSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}

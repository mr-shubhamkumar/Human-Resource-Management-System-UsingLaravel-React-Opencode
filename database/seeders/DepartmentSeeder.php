<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Seed the company departments.
     */
    public function run(): void
    {
        $departments = [
            'Human Resources' => 'Handles recruitment, employee relations and benefits.',
            'IT' => 'Maintains infrastructure, applications and security.',
            'Finance' => 'Manages accounting, budgeting and payroll funding.',
            'Marketing' => 'Owns branding, campaigns and market research.',
            'Operations' => 'Oversees day-to-day business processes.',
            'Sales' => 'Drives revenue and manages client accounts.',
        ];

        foreach ($departments as $name => $description) {
            Department::firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'is_approved' => true],
            );
        }
    }
}

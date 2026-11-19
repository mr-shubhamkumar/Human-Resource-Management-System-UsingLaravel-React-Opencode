<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Seed two to four positions for every department.
     */
    public function run(): void
    {
        $positions = [
            'Human Resources' => [
                ['HR Assistant', 28000, 42000],
                ['HR Specialist', 38000, 65000],
                ['HR Manager', 75000, 120000],
            ],
            'IT' => [
                ['Developer', 45000, 90000],
                ['QA Engineer', 40000, 80000],
                ['DevOps Engineer', 55000, 110000],
                ['IT Support', 28000, 50000],
            ],
            'Finance' => [
                ['Accountant', 35000, 65000],
                ['Financial Analyst', 45000, 85000],
                ['Finance Manager', 80000, 130000],
            ],
            'Marketing' => [
                ['Content Specialist', 30000, 55000],
                ['Marketing Analyst', 35000, 60000],
                ['Marketing Manager', 70000, 115000],
            ],
            'Operations' => [
                ['Operations Coordinator', 30000, 50000],
                ['Operations Analyst', 40000, 70000],
                ['Operations Manager', 75000, 120000],
            ],
            'Sales' => [
                ['Sales Representative', 28000, 60000],
                ['Account Manager', 40000, 85000],
                ['Sales Manager', 75000, 130000],
            ],
        ];

        foreach ($positions as $departmentName => $departmentPositions) {
            $department = Department::where('name', $departmentName)->firstOrFail();

            foreach ($departmentPositions as [$title, $salaryMin, $salaryMax]) {
                Position::firstOrCreate(
                    ['department_id' => $department->id, 'title' => $title],
                    [
                        'description' => sprintf('Responsible for %s activities within the %s department.', strtolower($title), $departmentName),
                        'salary_min' => $salaryMin,
                        'salary_max' => $salaryMax,
                    ],
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Seed 80 employees across the seeded departments and positions.
     */
    public function run(): void
    {
        $departments = Department::with('positions')->get();

        if ($departments->isEmpty()) {
            return;
        }

        for ($index = 1; $index <= 80; $index++) {
            $department = $departments->random();
            $position = $department->positions->random();
            $attributes = Employee::factory()->definition();
            $firstName = $attributes['first_name'];
            $lastName = $attributes['last_name'];
            $emailName = preg_replace('/[^a-z]/', '', strtolower($firstName.$lastName)) ?: 'employee';
            $statusRoll = fake()->numberBetween(1, 100);

            $status = match (true) {
                $statusRoll <= 80 => 'active',
                $statusRoll <= 95 => 'inactive',
                default => 'terminated',
            };

            Employee::factory()
                ->for($department)
                ->state([
                    'employee_id' => sprintf('EMP%04d', $index),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $emailName.$index.'@company.com',
                    'position_id' => $position->id,
                    'status' => $status,
                    'is_approved' => fake()->numberBetween(1, 100) > 5,
                ])
                ->create();
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    /**
     * Seed leave types and leave requests for roughly 30% of employees.
     */
    public function run(): void
    {
        $types = collect([
            ['name' => 'Sick Leave', 'days_allowed' => 10],
            ['name' => 'Vacation Leave', 'days_allowed' => 15],
            ['name' => 'Emergency Leave', 'days_allowed' => 5],
        ])->map(fn (array $type): LeaveType => LeaveType::firstOrCreate(
            ['name' => $type['name']],
            ['days_allowed' => $type['days_allowed']],
        ));

        $approvers = User::whereNotNull('role_id')->get();

        if ($approvers->isEmpty()) {
            return;
        }

        $employees = Employee::all()->shuffle()->take((int) ceil(Employee::count() * 0.3));

        foreach ($employees as $employee) {
            $startDate = today()->subDays(fake()->numberBetween(0, 60));
            $statusRoll = fake()->numberBetween(1, 100);
            $status = match (true) {
                $statusRoll <= 40 => 'pending',
                $statusRoll <= 75 => 'approved',
                default => 'rejected',
            };

            Leave::factory()
                ->for($employee)
                ->create([
                    'leave_type_id' => $types->random()->id,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $startDate->copy()->addDays(fake()->numberBetween(1, 5))->toDateString(),
                    'reason' => fake()->randomElement([
                        'Family matter that requires my attention.',
                        'Medical consultation and recovery.',
                        'Scheduled personal travel.',
                        'Home emergency requiring immediate action.',
                    ]),
                    'status' => $status,
                    'approved_by' => $status === 'pending' ? null : $approvers->random()->id,
                ]);
        }
    }
}

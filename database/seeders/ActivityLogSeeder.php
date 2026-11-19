<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Seed recent administrative activity logs.
     */
    public function run(): void
    {
        $users = User::whereNotNull('role_id')->get();

        if ($users->isEmpty()) {
            return;
        }

        $actions = [
            'Created employee',
            'Updated employee',
            'Approved leave',
            'Rejected leave',
            'Generated payroll',
            'Created department',
            'Updated position',
        ];

        for ($index = 0; $index < 60; $index++) {
            $action = fake()->randomElement($actions);
            $employee = Employee::inRandomOrder()->first();
            $leave = Leave::inRandomOrder()->first();
            $payroll = Payroll::inRandomOrder()->first();

            $description = match ($action) {
                'Approved leave' => $leave
                    ? sprintf('Approved leave request #%d.', $leave->id)
                    : 'Approved a leave request.',
                'Rejected leave' => $leave
                    ? sprintf('Rejected leave request #%d.', $leave->id)
                    : 'Rejected a leave request.',
                'Generated payroll' => $payroll
                    ? sprintf('Generated payroll #%d for %s.', $payroll->id, $payroll->pay_date->format('F Y'))
                    : 'Generated monthly payroll.',
                'Created department' => 'Created a new department.',
                'Updated position' => 'Updated position details.',
                default => $employee
                    ? sprintf('%s record for %s.', $action, $employee->full_name)
                    : sprintf('%s a record.', $action),
            };

            ActivityLog::factory()->create([
                'user_id' => $users->random()->id,
                'action' => $action,
                'description' => $description,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Leave;
use App\Models\Notification;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Seed notifications for the admin and HR accounts.
     */
    public function run(): void
    {
        $users = User::whereNotNull('role_id')->get();

        if ($users->isEmpty()) {
            return;
        }

        $leaves = Leave::whereIn('status', ['approved', 'rejected'])->get();

        for ($index = 0; $index < 15; $index++) {
            $leave = $leaves->isNotEmpty() ? $leaves->random() : null;

            Notification::factory()->create([
                'user_id' => $users->random()->id,
                'title' => $leave?->status === 'rejected'
                    ? 'Leave request rejected'
                    : 'Leave request approved',
                'message' => $leave
                    ? sprintf(
                        'Leave request from employee #%d for %s to %s was %s.',
                        $leave->employee_id,
                        $leave->start_date->format('M d'),
                        $leave->end_date->format('M d'),
                        $leave->status,
                    )
                    : 'A leave request has been processed.',
            ]);
        }

        for ($index = 0; $index < 15; $index++) {
            $payroll = Payroll::inRandomOrder()->first();

            Notification::factory()->create([
                'user_id' => $users->random()->id,
                'title' => 'Payroll generated',
                'message' => $payroll
                    ? sprintf('Payroll for %s has been generated.', $payroll->pay_date->format('F Y'))
                    : 'Monthly payroll has been generated.',
            ]);
        }
    }
}

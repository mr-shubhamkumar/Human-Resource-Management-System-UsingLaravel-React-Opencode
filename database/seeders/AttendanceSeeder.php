<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Seed 30 days of attendance for every employee.
     */
    public function run(): void
    {
        $employees = Employee::all();
        $today = today();
        $rows = [];

        foreach ($employees as $employee) {
            for ($daysAgo = 29; $daysAgo >= 0; $daysAgo--) {
                $date = $today->copy()->subDays($daysAgo);

                if ($date->dayOfWeek === 0) {
                    continue;
                }

                $statusRoll = fake()->numberBetween(1, 100);
                $status = match (true) {
                    $statusRoll <= 75 => 'present',
                    $statusRoll <= 90 => 'late',
                    default => 'absent',
                };

                $checkIn = match ($status) {
                    'present' => sprintf('08:%02d:00', fake()->numberBetween(0, 59)),
                    'late' => sprintf('09:%02d:00', fake()->numberBetween(1, 30)),
                    default => null,
                };
                $checkOut = $status === 'absent'
                    ? null
                    : today()->setTime(17, 0)->addMinutes(fake()->numberBetween(0, 120))->format('H:i:s');

                $attributes = Attendance::factory()->make([
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                ])->getAttributes();

                $attributes['created_at'] = $date->toDateTimeString();
                $attributes['updated_at'] = $date->toDateTimeString();
                $rows[] = $attributes;
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Attendance::query()->insert($chunk);
        }
    }
}

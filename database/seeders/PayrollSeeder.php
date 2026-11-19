<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    /**
     * Seed six months of monthly payroll records for every employee.
     */
    public function run(): void
    {
        $employees = Employee::with('position')->get();
        $rows = [];

        foreach ($employees as $employee) {
            $salaryMin = (float) ($employee->position->salary_min ?: 25000);
            $salaryMax = (float) ($employee->position->salary_max ?: 90000);
            $basicSalary = round(fake()->numberBetween((int) $salaryMin, (int) $salaryMax), 2);

            for ($monthsAgo = 6; $monthsAgo >= 1; $monthsAgo--) {
                $month = today()->copy()->subMonths($monthsAgo);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                $absences = Attendance::query()
                    ->where('employee_id', $employee->id)
                    ->whereBetween('date', [$monthStart, $monthEnd])
                    ->where('status', 'absent')
                    ->count();
                $dailyRate = $basicSalary / 22;
                $tax = $basicSalary * fake()->randomFloat(2, 0.08, 0.18);
                $deductions = round($tax + ($absences * $dailyRate), 2);
                $bonus = fake()->boolean(35) ? round($basicSalary * fake()->randomFloat(2, 0.02, 0.15), 2) : 0;
                $netSalary = round($basicSalary + $bonus - $deductions, 2);

                $attributes = Payroll::factory()->make([
                    'employee_id' => $employee->id,
                    'basic_salary' => $basicSalary,
                    'bonus' => $bonus,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'pay_date' => $monthEnd->toDateString(),
                ])->getAttributes();

                $attributes['created_at'] = $monthEnd->toDateTimeString();
                $attributes['updated_at'] = $monthEnd->toDateTimeString();
                $rows[] = $attributes;
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Payroll::query()->insert($chunk);
        }
    }
}

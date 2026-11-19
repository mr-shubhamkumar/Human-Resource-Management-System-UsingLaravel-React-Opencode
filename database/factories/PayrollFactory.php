<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basicSalary = $this->faker->numberBetween(25000, 95000);
        $bonus = $this->faker->boolean(35) ? $basicSalary * $this->faker->randomFloat(2, 0.02, 0.15) : 0;
        $deductions = $basicSalary * $this->faker->randomFloat(2, 0.08, 0.22);
        $payDate = Carbon::now()->subMonths($this->faker->numberBetween(0, 5))->endOfMonth();

        return [
            'employee_id' => Employee::factory(),
            'basic_salary' => $basicSalary,
            'bonus' => round($bonus, 2),
            'deductions' => round($deductions, 2),
            'net_salary' => round($basicSalary + $bonus - $deductions, 2),
            'pay_date' => $payDate->toDateString(),
        ];
    }
}

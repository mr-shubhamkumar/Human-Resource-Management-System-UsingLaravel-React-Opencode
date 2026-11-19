<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Leave>
 */
class LeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance($this->faker->dateTimeBetween('-4 months', 'now'));
        $endDate = $startDate->copy()->addDays($this->faker->numberBetween(1, 5));

        return [
            'employee_id' => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'reason' => $this->faker->sentence(),
            'status' => 'pending',
            'approved_by' => null,
        ];
    }

    /**
     * Indicate that the leave has been approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the leave has been rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'rejected',
        ]);
    }
}

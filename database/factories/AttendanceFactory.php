<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-30 days', 'now');
        $status = $this->faker->randomElement([
            'present', 'present', 'present', 'present', 'late', 'absent',
        ]);
        $checkIn = $status === 'absent' ? null : Carbon::instance($date)->setTime(8, 0)
            ->addMinutes($this->faker->numberBetween(0, 90))->format('H:i:s');
        $checkOut = $status === 'absent' ? null : Carbon::instance($date)->setTime(17, 0)
            ->addMinutes($this->faker->numberBetween(0, 120))->format('H:i:s');

        return [
            'employee_id' => Employee::factory(),
            'date' => $date->format('Y-m-d'),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $status,
        ];
    }

    /**
     * Indicate an on-time attendance record.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes): array => [
            'check_in' => '08:'.fake()->numberBetween(0, 45).':00',
            'check_out' => '17:'.fake()->numberBetween(0, 59).':00',
            'status' => 'present',
        ]);
    }

    /**
     * Indicate a late attendance record.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes): array => [
            'check_in' => '09:'.fake()->numberBetween(1, 30).':00',
            'check_out' => '18:'.fake()->numberBetween(0, 59).':00',
            'status' => 'late',
        ]);
    }

    /**
     * Indicate an absent attendance record.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes): array => [
            'check_in' => null,
            'check_out' => null,
            'status' => 'absent',
        ]);
    }
}

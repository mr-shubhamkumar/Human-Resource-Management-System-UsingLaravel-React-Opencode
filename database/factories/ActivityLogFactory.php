<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement([
                'Created employee',
                'Updated employee',
                'Approved leave',
                'Rejected leave',
                'Generated payroll',
            ]),
            'description' => $this->faker->sentence(),
            'ip_address' => $this->faker->ipv4(),
            'created_at' => Carbon::instance($this->faker->dateTimeBetween('-6 months', 'now')),
        ];
    }
}

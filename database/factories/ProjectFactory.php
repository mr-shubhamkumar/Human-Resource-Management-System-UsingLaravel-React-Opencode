<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance($this->faker->dateTimeBetween('-6 months', '-1 month'));

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->randomElement([
                'Employee Self-Service Portal',
                'Payroll Automation',
                'Recruitment Pipeline Redesign',
                'HR Analytics Dashboard',
                'Onboarding Experience Upgrade',
                'Performance Review System',
                'Attendance Mobile App',
                'Benefits Administration Module',
            ]),
            'client' => $this->faker->company(),
            'details' => $this->faker->paragraph(),
            'project_start_date' => $startDate->toDateString(),
            'project_end_date' => $startDate->copy()->addMonths($this->faker->numberBetween(2, 8))->toDateString(),
            'progress' => $this->faker->numberBetween(5, 100),
            'status' => $this->faker->randomElement(['planning', 'in_progress', 'completed']),
            'created_at' => Carbon::instance($this->faker->dateTimeBetween('-6 months', 'now')),
        ];
    }
}

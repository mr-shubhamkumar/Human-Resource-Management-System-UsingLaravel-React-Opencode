<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'title' => $this->faker->randomElement([
                'Coordinator',
                'Specialist',
                'Manager',
                'Analyst',
                'Assistant',
            ]),
            'description' => $this->faker->sentence(),
            'salary_min' => $this->faker->numberBetween(25000, 45000),
            'salary_max' => $this->faker->numberBetween(60000, 120000),
        ];
    }
}

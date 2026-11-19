<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Human Resources',
                'IT',
                'Finance',
                'Marketing',
                'Operations',
                'Sales',
                'Customer Support',
                'Legal',
                'Research & Development',
                'Procurement',
            ]),
            'description' => $this->faker->sentence(),
            'is_approved' => true,
        ];
    }

    /**
     * Indicate that the department is still awaiting approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_approved' => false,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Filipino and international first names used for realistic dummy data.
     *
     * @var array<int, string>
     */
    private const FIRST_NAMES = [
        'Juan', 'Maria', 'Jose', 'Ana', 'Miguel', 'Liza', 'Carlos', 'Sarah',
        'Andres', 'Catherine', 'Pedro', 'Rosa', 'Luigi', 'Nina', 'Paolo', 'Grace',
        'Ricardo', 'Mei', 'Daniel', 'Aisha', 'Thomas', 'Priya', 'James', 'Sofia',
        'Kenji', 'Emma', 'Omar', 'Lucia', 'Arjun', 'Chloe',
    ];

    /**
     * Filipino and international last names used for realistic dummy data.
     *
     * @var array<int, string>
     */
    private const LAST_NAMES = [
        'Dela Cruz', 'Reyes', 'Santos', 'Garcia', 'Mendoza', 'Fernandez',
        'Bautista', 'Torres', 'Nguyen', 'Patel', 'Kim', 'Tanaka', 'Silva',
        'Okafor', 'Haddad', 'Anderson', 'Martinez', 'Chen', 'Singh', 'Rivera',
        'Castro', 'Vargas', 'Ito', 'Wong', 'Khan', 'Novak', 'Dubois', 'Rossi',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        [$firstName, $lastName] = $this->name();

        return [
            'employee_id' => $this->faker->unique()->numerify('EMP####'),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '09'.$this->faker->numerify('#########'),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'date_of_birth' => Carbon::instance(
                $this->faker->dateTimeBetween('-60 years', '-22 years'),
            )->toDateString(),
            'address' => $this->faker->address(),
            'hire_date' => Carbon::instance(
                $this->faker->dateTimeBetween('-5 years', 'now'),
            )->toDateString(),
            'department_id' => Department::factory(),
            'position_id' => Position::factory(),
            'status' => 'active',
            'is_approved' => true,
            'profile_photo' => null,
        ];
    }

    /**
     * Indicate that the employee is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the employee has been terminated.
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'terminated',
        ]);
    }

    /**
     * Indicate that the employee record awaits approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_approved' => false,
        ]);
    }

    /**
     * Get a realistic first and last name pair.
     *
     * @return array{0: string, 1: string}
     */
    private function name(): array
    {
        return [
            trim($this->faker->randomElement(self::FIRST_NAMES)),
            $this->faker->randomElement(self::LAST_NAMES),
        ];
    }
}

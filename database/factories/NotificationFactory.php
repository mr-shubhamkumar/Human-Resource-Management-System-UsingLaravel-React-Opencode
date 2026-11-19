<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
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
            'title' => $this->faker->randomElement([
                'Leave request approved',
                'Leave request rejected',
                'Payroll generated',
            ]),
            'message' => $this->faker->sentence(),
            'is_read' => $this->faker->boolean(40),
        ];
    }
}

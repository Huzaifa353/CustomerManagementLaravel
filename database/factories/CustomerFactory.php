<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->name,
            'email'       => $this->faker->unique()->safeEmail,
            'gender'      => $this->faker->randomElement(['male', 'female', 'other']),
            'country'     => $this->faker->country,
            'department'  => $this->faker->optional(0.9)->randomElement([
                'Engineering',
                'Sales',
                'Marketing',
                'HR',
                'Finance',
                'Operations',
                'Support',
            ]),
            'designation' => $this->faker->optional(0.9)->jobTitle,
            'signup_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}

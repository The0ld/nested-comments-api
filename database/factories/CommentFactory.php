<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'comment'    => $this->faker->sentence(),
            'user_id'    => User::inRandomOrder()->first()->id ?? User::factory(),
            'parent_id'  => null, // We'll handle nesting in the seeder
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'updated_at' => now(),
        ];
    }
}


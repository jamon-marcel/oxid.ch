<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->lastName(),
            'firstname' => fake()->firstName(),
            'category' => fake()->numberBetween(1, 5),
            'publish' => 1,
        ];
    }
}

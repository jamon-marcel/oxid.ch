<?php

namespace Database\Factories;

use App\Models\Discourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discourse>
 */
class DiscourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'heading' => ['de' => fake()->word(), 'en' => ''],
            'date' => ['de' => fake()->dayOfWeek().', '.fake()->dayOfMonth().'. '.fake()->monthName(), 'en' => ''],
            'title' => ['de' => fake()->words(3, true), 'en' => ''],
            'description_short' => ['de' => fake()->text(), 'en' => ''],
            'description' => ['de' => fake()->text(), 'en' => ''],
            'info' => ['de' => fake()->text(), 'en' => ''],
            'category' => fake()->numberBetween(1, 3),
            'publish' => 1,
        ];
    }
}

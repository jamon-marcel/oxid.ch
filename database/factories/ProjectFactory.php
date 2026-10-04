<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ['de' => ucfirst(fake()->words(5, true)), 'en' => ''],
            'title_short' => ['de' => ucfirst(fake()->words(2, true)), 'en' => ''],
            'location' => ['de' => fake()->city(), 'en' => ''],
            'description' => ['de' => fake()->text(200), 'en' => ''],
            'info' => ['de' => fake()->text(100), 'en' => ''],
            'year' => fake()->year(),
            'program' => fake()->numberBetween(1, 4),
            'state' => fake()->numberBetween(1, 3),
            'author' => fake()->numberBetween(1, 3),
            'is_filter_wood' => fake()->numberBetween(0, 1),
            'is_filter_reuse' => fake()->numberBetween(0, 1),
            'has_detail' => fake()->numberBetween(0, 1),
            'is_highlight' => fake()->numberBetween(0, 1),
            'publish' => 1,
        ];
    }
}

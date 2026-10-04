<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ['de' => fake()->jobTitle(), 'en' => ''],
            'description' => ['de' => fake()->text(), 'en' => ''],
            'publish' => 1,
        ];
    }
}

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Project;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'title' => ['de' => ucfirst($faker->words(5, true)), 'en' => ''],
        'title_short' => ['de' => ucfirst($faker->words(2, true)), 'en' => ''],
        'location' => ['de' => $faker->city, 'en' => ''],
        'description' => ['de' => $faker->text(200), 'en' => ''],
        'info' => ['de' => $faker->text(100), 'en' => ''],
        'year' => $faker->year(),
        'program' => $faker->numberBetween(1,4),
        'state' => $faker->numberBetween(1,3),
        'author' => $faker->numberBetween(1,3),
        'is_filter_wood' => $faker->numberBetween(0,1),
        'is_filter_reuse' => $faker->numberBetween(0,1),
        'has_detail' => $faker->numberBetween(0,1),
        'is_highlight' => $faker->numberBetween(0,1),
        'publish' => 1,
    ];
});

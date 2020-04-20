<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Discourse;
use Faker\Generator as Faker;

$factory->define(Discourse::class, function (Faker $faker) {
    return [
        'heading' => ['de' => $faker->word, 'en' => ''],
        'date' => ['de' => $faker->dayOfWeek . ', ' . $faker->dayOfMonth .'. '. $faker->monthName, 'en' => ''],
        'title' => ['de' => $faker->word .' '. $faker->word .' '.$faker->word, 'en' => ''],
        'description_short' => ['de' => $faker->text, 'en' => ''],
        'description' => ['de' => $faker->text, 'en' => ''],
        'info' => ['de' => $faker->text, 'en' => ''],
        'category' => $faker->numberBetween(1,3)
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'name' => $faker->lastName,
        'firstname' => $faker->firstName,
        'category' => $faker->numberBetween(1,3),
    ];
});

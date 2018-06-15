<?php

use Faker\Generator as Faker;

$factory->define(App\Library::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'path' => $faker->sentence(10, true) . DIRECTORY_SEPARATOR . $faker->sentence(10, true)
    ];
});

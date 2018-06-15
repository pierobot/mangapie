<?php

use Faker\Generator as Faker;

$factory->define(App\Manga::class, function (Faker $faker) {
    $name = $faker->sentence(5, true);

    return [
        'name' => $name,
        'path' => $faker->sentence(5, true) . DIRECTORY_SEPARATOR . $name
    ];
});

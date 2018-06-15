<?php

use Faker\Generator as Faker;

$factory->define(App\Archive::class, function (Faker $faker) {
    return [
        'name' => $faker->bothify('**********') . $faker->randomElement(['.zip', '.cbz', '.rar', '.cbr']),
        'size' => $faker->numberBetween(1024 * 30, 1024 * 300)
    ];
});

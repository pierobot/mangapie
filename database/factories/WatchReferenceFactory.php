<?php

use Faker\Generator as Faker;

$factory->define(App\WatchReference::class, function (Faker $faker) {
    return [
        'user_id' => factory(App\User::class)->create()->getId(),
        'manga_id' => factory(App\Manga::class)->create([
            'library_id' => factory(App\Library::class)->create()
        ])->getId()
    ];
});

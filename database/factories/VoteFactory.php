<?php

use Faker\Generator as Faker;

use App\Library;
use App\Manga;
use App\Vote;
use App\User;

$factory->define(Vote::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create(),
        'manga_id' => factory(Manga::class)->create([
            'library_id' => factory(Library::class)->create()
        ]),

        'rating' => 70
    ];
});

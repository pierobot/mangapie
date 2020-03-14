<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'text' => $faker->text(10),
        'user_id' => factory(App\User::class)->create(),
        'manga_id' => factory(App\Manga::class)->create([
            'library_id' => factory(App\Library::class)->create()
        ])
    ];
});

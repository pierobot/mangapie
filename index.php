<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/bootstrap/app.php';

/*
$library = new \App\Library;
$library->name = "Manga1";
$library->path = "/mnt/hdd1/Manga/Manga";
$library->save();

$library = new \App\Library;
$library->name = "Manga2";
$library->path = "/mnt/hdd2/Manga/Manga";
$library->save();
*/

/*
$manga = new \App\Manga;
$manga->name = "Berserk";
$manga->path = "/mnt/hdd1/Manga/Manga/Berserk";
$manga->save();
*/

/*
$manga_info = new \App\MangaInformation;
$manga_info->id = 1;
$manga_info->mu_id = 88;
$manga_info->name = "Berserk";
$manga_info->description = "Guts, known as the Black Swordsman, seeks sanctuary from the demonic forces attracted to him and his woman because of a demonic mark on their necks, and also vengeance against the man who branded him as an unholy sacrifice. Aided only by his titanic strength gained from harsh childhood lived with mercenaries, a gigantic sword, and an iron prosthetic left hand, Guts must struggle against his bleak destiny, all the while fighting with a rage that might strip him of his humanity.";
$manga_info->type = "Manga";
$manga_info->save();
*/

/*
$genre = new \App\Genre;
$genre->name = "Action";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Adult";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Adventure";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Comedy";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Doujinshi";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Drama";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Ecchi";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Fantasy";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Gender Bender";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Harem";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Hentai";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Historical";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Horror";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Josei";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Lolicon";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Martial Arts";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Mature";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Mecha";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Mystery";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Psychological";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Romance";
$genre->save();

$genre = new \App\Genre;
$genre->name = "School Life";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Sci-fi";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Seinen";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Shotacon";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Shoujo";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Shoujo Ai";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Shounen";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Shounen Ai";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Slice of Life";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Smut";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Sports";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Supernatural";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Tragedy";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Yaoi";
$genre->save();

$genre = new \App\Genre;
$genre->name = "Yuri";
$genre->save();
*/

/*
$genre_info = new \App\GenreInformation;
$genre_info->id = 1;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 3;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 8;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 13;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 17;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 20;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 24;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 33;
$genre_info->manga_id = 1;
$genre_info->save();

$genre_info = new \App\GenreInformation;
$genre_info->id = 34;
$genre_info->manga_id = 1;
$genre_info->save();
*/

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);

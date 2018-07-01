<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', ['as' => 'login', 'uses' => 'LoginController@index']);
Route::post('/login', ['as' => 'login', 'uses' => 'LoginController@login']);

Route::middleware(['auth', 'last_seen'])->group(function () {

    Route::get('/logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);

    Route::prefix('admin')->middleware('admin')->name('admin')->group(function () {
        Route::get('/', 'AdminController@index');
        Route::get('/users', 'AdminController@users');
        Route::get('/libraries', 'AdminController@libraries');
    });

    Route::prefix('artist')->name('artist')->group(function () {
        Route::get('/{artist}', 'ArtistController@index');
    });

    Route::prefix('author')->name('author')->group(function () {
        Route::get('/{author}', 'AuthorController@index');
    });

    Route::prefix('cover')->name('cover')->group(function () {
        Route::get('/small/{manga}', 'CoverController@smallDefault');
        Route::get('/medium/{manga}', 'CoverController@mediumDefault');

        Route::get('/small/{manga}/{archive}/{page}', 'CoverController@small');
        Route::get('/medium/{manga}/{archive}/{page}', 'CoverController@medium');

        Route::post('/update', 'CoverController@update')->middleware('maintainer');
    });

    Route::prefix('edit')->middleware('maintainer')->name('edit')->group(function () {
        Route::get('/{manga}', 'MangaEditController@index');
        Route::post('/', 'MangaEditController@update');

        Route::get('/mangaupdates/{manga}', 'MangaEditController@mangaupdates');

        Route::get('/description/{manga}', 'MangaEditController@description');
        Route::get('/type/{manga}', 'MangaEditController@type');
        Route::get('/names/{manga}', 'MangaEditController@names');
        Route::get('/genres/{manga}', 'MangaEditController@genres');
        Route::get('/authors/{manga}', 'MangaEditController@authors');
        Route::get('/artists/{manga}', 'MangaEditController@artists');
        Route::get('/year/{manga}', 'MangaEditController@year');

        Route::get('/covers/{manga}', 'MangaEditController@covers');
    });

    Route::prefix('favorites')->name('favorites')->group(function () {
        Route::get('/', 'FavoriteController@index');
        Route::post('/', 'FavoriteController@update');
    });

    Route::prefix('genre')->name('genre')->group(function () {
        Route::get('/{genre}', 'GenreController@index');
    });

    Route::name('home')->group(function () {
        Route::get('/', 'HomeController@index');
        Route::get('/home', 'HomeController@index');
    });

    Route::prefix('image')->name('image')->group(function () {
        Route::get('/{manga}/{archive}/{page}', 'ReaderController@image');
    });

    Route::prefix('library')->middleware('admin')->name('library')->group(function () {
        Route::post('/create', 'LibraryController@create');
        Route::post('/update', 'LibraryController@update');
        Route::post('/status', 'LibraryController@status');
        Route::post('/delete', 'LibraryController@delete');
    });

    Route::prefix('manga')->name('manga')->group(function () {
        Route::get('/{manga}/{sort?}', 'MangaController@index');
    });

    Route::prefix('notifications')->name('notifications')->group(function () {
        Route::get('/', 'NotificationController@index');
        Route::post('/dismiss', 'NotificationController@dismiss');
    });

    Route::prefix('reader')->name('reader')->group(function () {
        Route::get('/{manga}/{archive}/{page}', 'ReaderController@index');
    });

    Route::prefix('search')->name('search')->group(function () {
        Route::get('/', 'SearchController@index');
        Route::post('/basic', 'SearchController@basic');
        Route::post('/advanced', 'SearchController@advanced');
        Route::get('/advanced', 'SearchController@index');
        Route::get('/autocomplete', 'SearchController@autoComplete');
    });

    Route::prefix('user')->name('user')->group(function () {
        Route::get('/{user}', 'UserController@index');
        Route::get('/{user}/profile', 'UserController@profile');
        Route::get('/{user}/activity', 'UserController@activity');

        Route::get('/{user}/avatar', 'UserController@avatar');
    });

    Route::prefix('users')->middleware('admin')->name('users')->group(function () {
        Route::post('/create', 'UserController@create');
        Route::post('/edit', 'UserController@edit');
        Route::post('/delete', 'UserController@delete');
    });

    Route::prefix('settings')->name('settings')->group(function () {
        Route::get('/', 'UserSettingsController@index');
        Route::get('/account', 'UserSettingsController@account');
        Route::get('/visuals', 'UserSettingsController@visuals');
        Route::get('/profile', 'UserSettingsController@profile');

        Route::post('/', 'UserSettingsController@update');
        Route::post('/profile', 'UserSettingsController@updateProfile');
        Route::post('/avatar', 'UserSettingsController@updateAvatar');
    });

    Route::prefix('watch')->name('watch')->group(function () {
        Route::post('/update', 'WatchController@update');
    });
});

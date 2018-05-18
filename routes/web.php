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

Route::middleware(['auth'])->group(function () {

    Route::get('/logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);

    Route::prefix('admin')->name('admin')->group(function () {
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

    Route::prefix('edit')->name('edit')->group(function () {
        Route::get('/{manga}', 'MangaEditController@index');
        Route::post('/', 'MangaEditController@update');
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
        Route::get('/home/library/{library}', 'HomeController@library');
    });

    Route::prefix('image')->name('image')->group(function () {
        Route::get('/{manga}/{archive_name}/{page}', 'ReaderController@image');
    });

    Route::prefix('library')->name('library')->group(function () {
        Route::post('/create', 'LibraryController@create');
        Route::post('/update', 'LibraryController@update');
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
        Route::get('/{manga}/{archive_name}/{page}', 'ReaderController@index');
    });

    Route::prefix('search')->name('search')->group(function () {
        Route::get('/', 'SearchController@index');
        Route::post('/basic', 'SearchController@basic');
        Route::post('/advanced', 'SearchController@advanced');
        Route::get('/advanced', 'SearchController@index');
        Route::get('/autocomplete', 'SearchController@autoComplete');
    });

    Route::prefix('thumbnail')->name('thumbnail')->group(function () {
        Route::get('/small/{manga}', 'ThumbnailController@smallDefault');
        Route::get('/medium/{manga}', 'ThumbnailController@mediumDefault');

        Route::get('/small/{manga}/{archive_name}/{page}', 'ThumbnailController@small');
        Route::get('/medium/{manga}/{archive_name}/{page}', 'ThumbnailController@medium');

        Route::post('/update', 'ThumbnailController@update');
    });

    Route::prefix('users')->name('users')->group(function () {
        Route::post('/create', 'UserController@create');
        Route::post('/edit', 'UserController@edit');
        Route::post('/delete', 'UserController@delete');
        Route::get('/settings', 'UserSettingsController@index');
        Route::post('/settings', 'UserSettingsController@update');
    });

    Route::prefix('watch')->name('watch')->group(function () {
        Route::post('/update', 'WatchController@update');
    });
});

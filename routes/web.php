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

Route::auth();

Route::middleware(['auth', 'last_seen'])->group(function () {

    Route::prefix('admin')->middleware('admin')->name('admin')->group(function () {
        Route::get('/', 'AdminController@index');
        Route::get('/dashboard/statistics', 'AdminController@statistics');
        Route::get('/dashboard/config', 'AdminController@config');
        Route::get('/users', 'AdminController@users');
        Route::get('/users/create', 'AdminController@createUsers');
        Route::get('/users/edit', 'AdminController@editUsers');
        Route::get('/users/delete', 'AdminController@deleteUsers');
        Route::get('/libraries', 'AdminController@libraries');
        Route::get('/libraries/create', 'AdminController@createLibraries');
        Route::get('/libraries/modify', 'AdminController@modifyLibraries');
        Route::get('/libraries/delete', 'AdminController@deleteLibraries');
        Route::get('/logs', 'AdminController@logs');
        Route::get('/logs/warnings', 'AdminController@logWarnings');
        Route::get('/logs/errors', 'AdminController@logErrors');

        Route::patch('/images', 'AdminController@patchImages');
        Route::delete('/images', 'AdminController@deleteImages');

        Route::patch('/config/registration', 'AdminController@patchRegistration');
        Route::put('/config/libraries', 'AdminController@putDefaultLibraries');
    });

    Route::prefix('avatar')->name('avatar')->group(function () {
        Route::get('/{user}', 'AvatarController@index');

        Route::put('/', 'AvatarController@put');
    });

    Route::prefix('comments')->name('comments')->group(function () {
        Route::put('/', 'CommentController@put');
        Route::delete('/', 'CommentController@delete');
    });

    Route::prefix('cover')->name('cover')->group(function () {
        Route::get('/small/{manga}', 'CoverController@smallDefault');
        Route::get('/medium/{manga}', 'CoverController@mediumDefault');

        Route::get('/small/{manga}/{archive}/{page}', 'CoverController@small');
        Route::get('/medium/{manga}/{archive}/{page}', 'CoverController@medium');

        Route::put('/', 'CoverController@put')->middleware('maintainer');
    });

    Route::prefix('edit')->middleware('maintainer')->name('edit')->group(function () {
        Route::get('/{manga}', 'MangaEditController@index');

        Route::post('/artist', 'MangaEditController@postArtist');
        Route::post('/author', 'MangaEditController@postAuthor');
        Route::post('/assocname', 'MangaEditController@postAssocName');
        Route::put('/genres', 'MangaEditController@putGenres');
        Route::put('/autofill', 'MangaEditController@putAutofill');
        Route::patch('/description', 'MangaEditController@patchDescription');
        Route::patch('/type', 'MangaEditController@patchType');
        Route::patch('/year', 'MangaEditController@patchYear');

        Route::delete('/artist', 'MangaEditController@deleteArtist');
        Route::delete('/author', 'MangaEditController@deleteAuthor');
        Route::delete('/assocname', 'MangaEditController@deleteAssocName');
        Route::delete('/description', 'MangaEditController@deleteDescription');
        Route::delete('/type', 'MangaEditController@deleteType');
        Route::delete('/year','MangaEditController@deleteYear');

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
        Route::post('/', 'FavoriteController@create');
        Route::delete('/', 'FavoriteController@delete');
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
        Route::put('/', 'LibraryController@create');
        Route::post('/', 'LibraryController@status');
        Route::patch('/', 'LibraryController@update');
        Route::delete('/', 'LibraryController@delete');
    });

    Route::prefix('manga')->middleware('manga_views')->name('manga')->group(function () {
        Route::get('/{manga}/{sort?}', 'MangaController@index');
        Route::get('/{manga}/files/{sort?}', 'MangaController@files');
        Route::get('/{manga}/comments', 'MangaController@comments');
    });

    Route::prefix('notifications')->name('notifications')->group(function () {
        Route::get('/', 'NotificationController@index');
        Route::delete('/', 'NotificationController@delete');
    });

    Route::prefix('person')->name('person')->group(function () {
        Route::get('/{person}', 'PersonController@index');
    });

    Route::prefix('reader')->middleware('archive_views')->name('reader')->group(function () {
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
        Route::get('/{user}/comments', 'UserController@comments');
        Route::get('/{user}/activity', 'UserController@activity');
    });

    Route::prefix('users')->middleware('admin')->name('users')->group(function () {
        Route::put('/', 'UserController@create');
        Route::patch('/', 'UserController@edit');
        Route::delete('/', 'UserController@delete');
    });

    Route::prefix('settings')->name('settings')->group(function () {
        Route::get('/', 'UserSettingsController@index');
        Route::get('/account', 'UserSettingsController@account');
        Route::get('/visuals', 'UserSettingsController@visuals');
        Route::get('/profile', 'UserSettingsController@profile');

        Route::put('/about', 'UserSettingsController@putAbout');
        Route::patch('/reader', 'UserSettingsController@patchReaderDirection');
        Route::patch('/password', 'UserSettingsController@patchPassword');
    });

    Route::prefix('vote')->name('votes')->group(function() {
        Route::put('/', 'VoteController@put');
        Route::patch('/', 'VoteController@patch');
        Route::delete('/', 'VoteController@delete');
    });

    Route::prefix('watch')->name('watch')->group(function () {
        Route::post('/', 'WatchController@create');
        Route::delete('/', 'WatchController@delete');
    });
});

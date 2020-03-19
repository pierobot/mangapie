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

Route::middleware(['auth'])->group(function () {

    Route::prefix('admin')->middleware('role:Administrator')->name('admin')->group(function () {
        Route::get('/', 'AdminController@index');

        Route::patch('/config/registration', 'AdminController@patchRegistration');
        Route::put('/config/roles', 'AdminController@putDefaultRoles');
        Route::patch('/config/heat', 'AdminController@patchHeat');
        Route::post('/config/heat', 'AdminController@postHeat');

        Route::get('/config', 'AdminController@config');
        Route::patch('/config/image', 'AdminController@patchImageExtraction');
        Route::patch('/config/image/scheduler', 'AdminController@patchScheduler');
        Route::put('/config/image/scheduler', 'AdminController@putScheduler');

        Route::patch('/config/views', 'AdminController@patchViews');
        Route::patch('/config/views/time', 'AdminController@patchViewsTime');
        Route::put('/config/views/time', 'AdminController@putViewsTime');

        Route::patch('/images', 'AdminController@patchImages');
        Route::delete('/images', 'AdminController@deleteImages');

        Route::get('/libraries', 'AdminController@libraries');
        Route::get('/statistics', 'AdminController@statistics');

        Route::get('/users', 'AdminController@users');
        Route::post('/users/search', 'AdminController@searchUsers');
    });

    Route::prefix('avatar')->name('avatar')->group(function () {
        Route::get('/{user}', 'AvatarController@index');

        Route::put('/', 'AvatarController@put');
    });

    Route::prefix('comments')->name('comments')->group(function () {
        Route::post('/', 'CommentController@create');
        Route::delete('/{comment}', 'CommentController@destroy');
    });

    Route::prefix('cover')->name('cover')->group(function () {
        Route::get('/small/{manga}', 'CoverController@smallDefault');
        Route::get('/medium/{manga}', 'CoverController@mediumDefault');

        Route::get('/small/{manga}/{archive}/{page}', 'CoverController@small');
        Route::get('/medium/{manga}/{archive}/{page}', 'CoverController@medium');

        Route::put('/', 'CoverController@put')->middleware('role:Administrator,Maintainer');
    });

    Route::prefix('edit')->middleware('role:Administrator,Maintainer')->name('edit')->group(function () {
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
        Route::delete('/{favorite}', 'FavoriteController@destroy');
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

    Route::prefix('job')->middleware('role:Administrator')->name('jobs')->group(function () {
        Route::get('/{jobStatus}', 'JobStatusController@status');
    });

    Route::prefix('library')->middleware('role:Administrator')->name('library')->group(function () {
        Route::post('/', 'LibraryController@create');
        Route::patch('/{library}', 'LibraryController@update');
        Route::delete('/{library}', 'LibraryController@destroy');
    });

    Route::prefix('lists')->name('lists')->group(function () {
        Route::get('/{user?}', 'UserController@statistics');
        Route::get('/completed/{user?}', 'UserController@completed');
        Route::get('/dropped/{user?}', 'UserController@dropped');
        Route::get('/onhold/{user?}', 'UserController@onHold');
        Route::get('/reading/{user?}', 'UserController@reading');
        Route::get('/planned/{user?}', 'UserController@planned');
    });

    Route::prefix('manga')->middleware('manga_views')->name('manga')->group(function () {
        Route::get('/{manga}/{sort?}', 'MangaController@show');
        Route::get('/{manga}/files/{sort?}', 'MangaController@files');
        Route::get('/{manga}/comments', 'MangaController@comments');
    });

    Route::prefix('notifications')->name('notifications')->group(function () {
        Route::get('/', 'NotificationController@index');
        Route::delete('/', 'NotificationController@delete');
    });

    Route::prefix('person')->name('person')->group(function () {
        Route::get('/{person}', 'PersonController@show');
    });

    Route::prefix('preview')->group(function () {
        Route::get('/{manga}/{archive}', 'PreviewController@index');

        Route::get('/small/{manga}/{archive}/{page}', 'PreviewController@small');
//        Route::get('/medium/{manga}/{archive}/{page}', 'PreviewController@medium');
    });

    Route::prefix('reader')->middleware('archive_views')->name('reader')->group(function () {
        Route::get('/{manga}/{archive}/{page}', 'ReaderController@index');

        Route::put('/history', 'ReaderController@putReaderHistory');
    });

    Route::prefix('roles')->middleware('role:Administrator')->name('roles')->group(function () {
        Route::get('/', 'RoleController@index');
        Route::post('/', 'RoleController@create');
        Route::patch('/{role}', 'RoleController@update');
        Route::delete('/{role}', 'RoleController@destroy');

        Route::patch('/grant/{user}/{role}', 'RoleController@grant');
        Route::delete('/revoke/{user}/{role}', 'RoleController@revoke');
    });

    Route::prefix('search')->name('search')->group(function () {
        Route::get('/', 'SearchController@index');
        Route::get('/basic', 'SearchController@getBasic');
        Route::post('/basic', 'SearchController@postBasic');
        Route::get('/advanced', 'SearchController@getAdvanced');
        Route::post('/advanced', 'SearchController@postAdvanced');
        Route::get('/autocomplete', 'SearchController@autoComplete');
    });

    Route::prefix('user')->name('user')->group(function () {
        Route::get('/{user}', 'UserController@show');
        Route::get('/{user}/comments', 'UserController@comments');
        Route::get('/{user}/activity', 'UserController@activity');

        Route::put('/status', 'UserController@putStatus');
    });

    Route::prefix('users')->middleware('role:Administrator')->name('users')->group(function () {
        Route::post('/', 'UserController@create');
        Route::patch('/', 'UserController@edit');
        Route::delete('/{user}', 'UserController@destroy');
    });

    Route::prefix('settings')->name('settings')->group(function () {
        Route::get('/', 'UserSettingsController@index');
        Route::get('/account', 'UserSettingsController@account');
        Route::get('/visuals', 'UserSettingsController@visuals');
        Route::get('/profile', 'UserSettingsController@profile');

        Route::put('/about', 'UserSettingsController@putAbout');
        Route::patch('/reader', 'UserSettingsController@patchReaderDirection');
        Route::patch('/password', 'UserSettingsController@patchPassword');

        Route::put('/display', 'UserSettingsController@putDisplay');
    });

    Route::prefix('vote')->name('votes')->group(function() {
        Route::put('/', 'VoteController@put');
        Route::delete('/{vote}', 'VoteController@destroy');
    });

    Route::prefix('watch')->name('watch')->group(function () {
        Route::post('/', 'WatchController@create');
        Route::delete('/{watchReference}', 'WatchController@destroy');
    });
});

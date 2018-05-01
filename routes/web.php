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
Route::get('/logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/home/library/{id}', 'HomeController@library')->where('id', '\d+');

Route::get('/search', 'SearchController@index');
Route::post('/search/basic', 'SearchController@basic');
Route::post('/search/advanced', 'SearchController@advanced');
Route::get('/search/advanced', 'SearchController@index');
Route::get('/search/autocomplete', 'SearchController@autoComplete');

Route::get('/manga/{id}/{sort?}', 'MangaController@index')->where('id', '\d+')
                                                                ->where('sort', 'ascending|descending');

Route::get('/edit/{id}', 'MangaEditController@index')->where('id', '\d+');
Route::post('/edit', 'MangaEditController@update');

Route::get('/genre/{id}', 'GenreController@index')->where('id', '\d+');

Route::get('/reader/{id}/{archive_name}/{page}', 'ReaderController@index')->where('id', '\d+')
                                                                          ->where('archive_name', '.+')
                                                                          ->where('page', '\d+');
Route::get('/image/{id}/{archive_name}/{page}', 'ReaderController@image')->where('id', '\d+')
                                                                         ->where('archive_name', '.+')
                                                                         ->where('page', '\d+');

Route::get('/favorites', 'FavoriteController@index');
Route::post('/favorites', 'FavoriteController@update');

Route::get('/admin', 'AdminController@index');
Route::get('/admin/users', 'AdminController@users');
Route::get('/admin/libraries', 'AdminController@libraries');

Route::post('/library/create', 'LibraryController@create');
Route::post('/library/update', 'LibraryController@update');
Route::post('/library/delete', 'LibraryController@delete');

Route::post('/users/create', 'UserController@create');
Route::post('/users/edit', 'UserController@edit');
Route::post('/users/delete', 'UserController@delete');
Route::get('/user/settings', 'UserSettingsController@index');
Route::post('/user/settings', 'UserSettingsController@update');

Route::get('/thumbnail/small/{id}', 'ThumbnailController@smallDefault')->where('id', '\d+');
Route::get('/thumbnail/medium/{id}', 'ThumbnailController@mediumDefault')->where('id', '\d+');
Route::get('/thumbnail/small/{id}/{archive_name}/{page}', 'ThumbnailController@small')->where('id', '\d+')
                                                                                      ->where('archive_name', '.+')
                                                                                      ->where('page', '\d+');
Route::get('/thumbnail/medium/{id}/{archive_name}/{page}', 'ThumbnailController@medium')->where('id', '\d+')
                                                                                        ->where('archive_name', '.+')
                                                                                        ->where('page', '\d+');
Route::post('/thumbnail/update', 'ThumbnailController@update');

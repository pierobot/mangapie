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

Route::get('/', 'MangaController@index');
Route::get('/browse/{page}', 'MangaController@show');
Route::get('/browse/library/{id}', 'MangaController@library');
Route::get('/thumbnail/{id}', 'MangaController@thumbnail');

Route::get('/search', 'SearchController@index');
Route::post('/search', 'SearchController@search');

Route::get('/information/{id}', 'MangaInformationController@index');
Route::post('/information', 'MangaInformationController@update');

Route::get('/genre/{id}', 'GenreController@index');

Route::get('/reader/{id}/{archive_name}/{page?}', 'ReaderController@index');
Route::get('/image/{id}/{archive_name}/{page}', 'ReaderController@image');

Route::get('/admin', 'AdminController@index');
Route::get('/admin/users', 'AdminController@users');
Route::post('/admin/users/create', 'AdminController@createUser');
Route::post('/admin/users/edit', 'AdminController@editUser');
Route::post('/admin/users/delete', 'AdminController@deleteUser');
Route::get('/admin/libraries', 'AdminController@libraries');

Route::post('/library', 'LibraryController@update');

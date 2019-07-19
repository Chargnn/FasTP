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

Route::get('/connect', 'FtpController@connect_form')->name('connect');
Route::post('/connect', 'FtpController@connect')->name('connect_action');
Route::get('/disconnect', 'FtpController@disconnect')->name('disconnect_action');
Route::get('/download/{file}', 'FtpController@download')->name('download');
Route::get('/delete/{file}', 'FtpController@delete')->name('delete');
Route::get('/see/{file}', 'FtpController@see')->name('see');
Route::post('/upload', 'FtpController@upload')->name('upload');
Route::post('/browse', 'FtpController@browse')->name('browse');
Route::post('/createDir', 'FtpController@createDir')->name('createDir');

Route::get('/', 'HomeController@index')->name('listing');

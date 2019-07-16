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

Route::get('/', function(){
    return view('index');
})->name('listing');

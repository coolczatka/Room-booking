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

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/abort', 'HomeController@abort_reservation')->name('abort');
Route::get('/', 'PagesController@index');
Route::get('/book/{id}','HomeController@bookingForm')->name('book');
Route::get('/reservations','HomeController@reservations')->name('reservations');
Route::post('/book/{id}','HomeController@book')->name('book_act');
Route::get('/create','HomeController@create_room')->name('create');
Route::get('logout', 'Auth\LoginController@logout', function () {
    return abort(404);
});
Auth::routes();
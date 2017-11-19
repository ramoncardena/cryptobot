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
Route::get('/', function () {
    return view('welcome');
});

Route::get('/settings', 'SettingsController@index');
Route::get('/settings/{setting}', 'SettingsController@show');
Route::patch('/settings', 'SettingsController@update');
Route::post('/settings', 'SettingsController@store');

Route::get('/orders', 'OrdersController@index');
Route::get('/trades', 'TradesController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


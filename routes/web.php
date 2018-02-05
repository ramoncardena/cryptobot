<?php

use Illuminate\Support\Facades\Auth;

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

Route::get('/connections', 'ConnectionsController@index');
Route::post('/connections', 'ConnectionsController@store');
Route::delete('/connections/{id}', 'ConnectionsController@destroy');
Route::patch('/connections/{id}', 'ConnectionsController@update');

Route::get('/invite', 'InviteController@index');
Route::post('/invite', 'InviteController@store');

Route::get('/mailable', function () {
    $name = Auth::user()->name;
    $email = Auth::user()->email;
    $invitation = json_decode(\Invi::generate("ramon.cardena@gmail.com", "7 day", true));

    return new App\Mail\NewInvite($invitation, $name, $email);
});

Route::get('/orders', 'OrdersController@index');
Route::get('/trades', 'TradeController@index');
Route::post('/trades', 'TradeController@store');
Route::delete('/trades/{id}', 'TradeController@destroy');
Route::patch('/trades/{id}', 'TradeController@update');

Route::get('/portfolio', 'PortfolioController@index');

Route::post('/portfolio/origin', 'PortfolioOriginController@store');
Route::patch('/portfolio/origin/{id}', 'PortfolioOriginController@update');
Route::delete('/portfolio/origin/{id}', 'PortfolioOriginController@destroy');

Route::post('/portfolio/asset', 'PortfolioAssetController@store');
Route::patch('/portfolio/asset/{id}', 'PortfolioAssetController@update');
Route::delete('/portfolio/asset/{id}', 'PortfolioAssetController@destroy');

Route::patch('/assets/{id}/transaction', 'PortfolioAssetController@settransaction');

Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');



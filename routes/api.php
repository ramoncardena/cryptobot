<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('broker/getpairs/{exchange}', 'BrokerController@getPairs');
Route::middleware('auth:api')->get('broker/getfee/{exchange}', 'BrokerController@getFee');
Route::middleware('auth:api')->get('broker/getticker/{exchange}/{coin}/{base}', 'BrokerController@getTicker');
Route::middleware('auth:api')->get('broker/getbalances/{exchange}', 'BrokerController@getBalances');
Route::middleware('auth:api')->get('broker/getcoininfo/{coin}', 'BrokerController@getCoinInfo');

Route::middleware('auth:api')->get('exchange/{name}/fee', 'ExchangeController@getfee');

Route::middleware('auth:api')->get('/notifications', 'NotificationsController@index');
Route::middleware('auth:api')->get('/notifications/{id}', 'NotificationsController@show');
Route::middleware('auth:api')->get('/notifications/markasread', 'NotificationsController@readall');
Route::middleware('auth:api')->get('/notifications/{id}/markasread', 'NotificationsController@read');
Route::middleware('auth:api')->delete('/notifications/{id}', 'NotificationsController@delete');
Route::middleware('auth:api')->delete('/notifications', 'NotificationsController@deleteall');

Route::middleware('auth:api')->get('/portfolio/refresh', 'PortfolioController@refresh');
Route::middleware('auth:api')->get('/assets', 'PortfolioAssetController@getall');
Route::middleware('auth:api')->get('/assets/{id}', 'PortfolioAssetController@get');




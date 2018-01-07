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

Route::middleware('auth:api')->get('bittrexapi/getpairs', 'BittrexApiController@getpairs');
Route::middleware('auth:api')->get('bittrexapi/getmarketsummary/{pair}', 'BittrexApiController@getmarketsummary');
Route::middleware('auth:api')->get('bittrexapi/getmarkets/{coin}', 'BittrexApiController@getmarkets');
Route::middleware('auth:api')->get('bittrexapi/getbalance/{coin}', 'BittrexApiController@getbalance');

Route::middleware('auth:api')->get('exchange/{name}/fee', 'ExchangeController@getfee');

Route::middleware('auth:api')->get('/notifications', 'NotificationsController@index');
Route::middleware('auth:api')->get('/notifications/{id}', 'NotificationsController@show');
Route::middleware('auth:api')->get('/notifications/markasread', 'NotificationsController@readall');
Route::middleware('auth:api')->get('/notifications/{id}/markasread', 'NotificationsController@read');
Route::middleware('auth:api')->delete('/notifications/{id}', 'NotificationsController@delete');
Route::middleware('auth:api')->delete('/notifications', 'NotificationsController@deleteall');

Route::middleware('auth:api')->get('/portfolio/refresh', 'PortfolioController@refresh');




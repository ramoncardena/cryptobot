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


Route::middleware('auth:api')->get('exchange/{name}/fee', 'ExchangeController@getfee');

Route::middleware('auth:api')->get('/notifications/markasread', function(){
	Auth::user()->notifications->markAsRead();
});

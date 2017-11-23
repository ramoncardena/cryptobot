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

Route::get('bittrexapi/getpairs', 'BittrexApiController@getpairs');
Route::get('bittrexapi/getmarketsummary/{pair}', 'BittrexApiController@getmarketsummary');
Route::get('bittrexapi/getmarkets/{coin}', 'BittrexApiController@getmarkets');


Route::get('exchange/{name}/fee', 'ExchangeController@getfee');


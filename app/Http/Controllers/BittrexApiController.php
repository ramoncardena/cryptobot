<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use App\Library\Services\Facades\Bittrex;

class BittrexApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getpairs(Request $request)
    {
        //var_dump(Bittrex::getmarkets()->result);
        $bittrexMarkets = collect(Bittrex::getmarkets()->result);
        $bittrexPairs = $bittrexMarkets->pluck('MarketName');

        return $bittrexPairs;
    }

    public function getmarketsummary($pair)
    {
        $bittrexMarketSummary = collect(Bittrex::getmarketsummary($pair)->result);

        return $bittrexMarketSummary->flatten(1);
    }

    public function getmarkets($coin) {

        $market = collect(Bittrex::getMarkets()->result);
        $coin = $market->where('MarketCurrency', $coin);
        $coin = array_values($coin->all());

        return $coin;
    }
}

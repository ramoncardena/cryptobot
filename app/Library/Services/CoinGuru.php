<?php

namespace App\Library\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Facades\Bittrex;
use App\User;
use App\CryptocompareAsset;

/**
 * Summary
 */
class CoinGuru
{
	public $guru;


	public $user;


    public function __construct()
    {

    }

    public function getCoinInfo($coin) 
    {
        $coin = CryptocompareAsset::where('symbol', $coin);

        return  $coin->first();

    }
    public function getCoinList()
    {
        $coins = CryptocompareAsset::pluck('name');

        return $coins->all();
    }

    
    public function cryptocompareCoinGetList() 
    {

    	$cryptocompareCoin = new \Cryptocompare\Coin();

        $response =  $cryptocompareCoin->getList();
        if ($response->Response == "Success") {
            return $cryptocompareCoin->getList();
        }
        else {

            return [];

        }

    }

    public function cryptocomparePriceGetSinglePrice($symbol, $counterValue) 
    {

    	$cryptocomparePrice = new \Cryptocompare\Price();
       
    	return $cryptocomparePrice->getSinglePrice("1",$symbol,$counterValue,"CCCAGG","false");
    	 	
    }

    public function getHistoricalPrice($from, $to, $date)
    {
        $cryptocomparePrice = new \Cryptocompare\Price();

        return $cryptocomparePrice->getHistoricalPrice("1", $from, $to, $date,"CCCAGG", "false");

    }
}
<?php

namespace App\Library\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Facades\Bittrex;
use App\User;

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

    public function cryptocompareCoinGetList() {

    	$cryptocompareCoin = new \Cryptocompare\Coin();

        $response =  $cryptocompareCoin->getList();
        if ($response->Response == "Success") {
            return $cryptocompareCoin->getList();
        }
        else {

            return [];

        }

    }

    public function cryptocomparePriceGetSinglePrice($symbol, $counterValue) {

    	$cryptocomparePrice = new \Cryptocompare\Price();
       
    	return $cryptocomparePrice->getSinglePrice("1",$symbol,$counterValue,"CCCAGG","false");
    	 	
    }
}
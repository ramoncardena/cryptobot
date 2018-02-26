<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Library\Services\CoinGuru;

class DashboardController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
    	// DATA FOR MODALS (New Ticker)
	    // Coin list
    	$guru = new CoinGuru;
	    $coins = $guru->getCoinList();

      $user =  Auth::user();
      $tickers = $user->tickers->all();
   		return view('dashboard', ['coins' => json_encode($coins), 'tickers' => $tickers]);
   	}
}

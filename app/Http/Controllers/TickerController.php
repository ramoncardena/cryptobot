<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Library\Services\CoinGuru;

use App\User;

class TickerController extends Controller
{
	public $user;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	    $this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
	    // DATA FOR MODALS (New Ticker)
	    // Coin list
	    $guru = new CoinGuru;
	    $coins = $guru->getCoinList();
	}

}

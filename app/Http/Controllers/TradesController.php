<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Library\Services\Facades\Bittrex;

class TradesController extends Controller
{
    protected $user;
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::check()) 
        {
            $this->user = Auth::user();

            Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));

            $bittrexMarkets = collect(Bittrex::getmarkets()->result);

            $bittrexPairs = $bittrexMarkets->pluck('MarketName');


            return view('trades', ['bittrexPairs' => $bittrexPairs]);
        }
    }

    public function getMarkets($exchange='') {
        if ($exchange == 'bittrex') {

        }
    }
    public function cancel($trade)
    {
    	
    }
}

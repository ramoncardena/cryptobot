<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\Services\Bittrex\Bittrex;

class HomeController extends Controller
{
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
        $bittrex = new Bittrex(config('services.bittrex.key'), config('services.bittrex.secret'));

        $balances = $bittrex->getBalances();


        return view('home', ['balances' => $balances->result]);
    }
}

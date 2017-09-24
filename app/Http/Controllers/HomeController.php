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

        // Get balance
        $balances= $bittrex->getBalances();
        $balanceCollection = collect($balances->result);
        
        // Split Currency and Balance arrays
        $currencies = $balanceCollection->pluck('Currency');
        $totals = $balanceCollection->pluck('Balance');

        // Combine a new array with ['currency'] => balance
        $portfolio = array_combine($currencies->toArray(), $totals->toArray());

        // Remove zero balances
        $portfolio = array_filter($portfolio, function ($v) {
                return $v != 0;
        });

        // Get value in BTC for each item in portfolio (ignoring BTC item)
        $values = collect($portfolio);
        $values = $values->map(function ($item, $key) {
            $bittrex = new Bittrex(config('services.bittrex.key'), config('services.bittrex.secret'));
            if ($key != 'BTC') {
                $ticker = $bittrex->getTicker('BTC-'. $key);
                return round($item * $ticker->result->Last, 8);
            }
            else {
                return $item;
            }
        });



        return view('home', ['portfolio' => $portfolio, 'values'=>$values, 'total' => $values->sum()]);
    }

}

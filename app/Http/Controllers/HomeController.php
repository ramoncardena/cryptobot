<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Library\Services\Facades\Bittrex;
use App\Library\Services\Facades\Bitcoin;

class HomeController extends Controller
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

            $coins = $this->getCoins();

            $totals = $this->getTotals($coins);

            $fiat = $this->user->settings()->fiat;


            return view('home', ['coins' => $coins, 'totals' => $totals, 'fiat' => $fiat]);

        }
        else {
            throw new Exception ("You must be authenticated to get your settings");
        }
    }
    
    protected function getCoinLogo( $coin ) {

        if ( $coin != 'BTC') {

            $market = Bittrex::getMarkets();

            $market = collect($market->result);

            $coin = $market->where('MarketCurrency', $coin);

            $coin = $coin->where('BaseCurrency', 'BTC');

            $logo = $coin->pluck('LogoUrl')->all();

            return $logo[0];
        }
        else {
            return "https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Bitcoin.svg/1200px-Bitcoin.svg.png";
        }
    }


    protected function getTotals ( $coins ) {

        $totals = ['BTC' => $coins->pluck('BTC-Value')->sum(), 'USD' => $coins->pluck('USD-Value')->sum(), 'EUR' => $coins->pluck('EUR-Value')->sum()];

        return $totals;
    }

    /**
     * Create a collection with all coins owned by user
     *
     * @return Collection
     */
    protected function getCoins() {

        // Get balance
        $balances= Bittrex::getBalances();
        $balanceCollection = collect($balances->result);
        
        // Split Currency and Balance arrays
        $currencies = $balanceCollection->pluck('Currency');
        $balance = $balanceCollection->pluck('Balance');

        // Combine a new array with ['currency'] => balance
        $portfolio = array_combine($currencies->toArray(), $balance->toArray());

        // Remove zero balances
        $portfolio = array_filter($portfolio, function ($v) {
                return $v != 0;
        });
        
        // Create a collection for the coins with balance
        $coins = Collect();

        // Add Name and Balance items in the portfolio collection
        $index = 1;
        foreach ($portfolio as $coinName => $coinBalance) {
            $coins->put($index, ['Name' => $coinName, 'Balance' => $coinBalance]);
            $index++;
        }

        $coins = $coins->map(function ($coin) {

            if ($coin['Name'] != 'BTC') {
                $ticker = Bittrex::getTicker('BTC-'. $coin['Name']);
                $coin['Price'] = number_format($ticker->result->Last, 8);
                return $coin;
            } else {
                $coin['Price'] = $coin['Balance'];
                return $coin;
            }
            
        });

        // Get value in BTC for each item in portfolio (ignoring BTC item)
        $coins = $coins->map(function ($coin) {
            
            if ($coin['Name'] != 'BTC') {
                $ticker = Bittrex::getTicker('BTC-'. $coin['Name']);
                $coin['BTC-Value'] = number_format($coin['Balance'] * $ticker->result->Last, 8);
                return $coin;
            } else {
                $coin['BTC-Value'] = $coin['Balance'];
                return $coin;
            }
            
        });

        // Get value in EUR for each item in portfolio
        $coins = $coins->map(function ($coin) {
            $btcTicker = Bitcoin::getTicker();
            $coin['EUR-Value'] = number_format($coin['BTC-Value'] * $btcTicker->EUR->last, 2);
            return $coin; 
        });

        // Get value in USD for each item in portfolio
        $coins = $coins->map(function ($coin) {
            $btcTicker = Bitcoin::getTicker();
            $coin['USD-Value'] = number_format($coin['BTC-Value'] * $btcTicker->USD->last, 2);
            return $coin; 
        });

        // Get coins logo
        $coins = $coins->map( function ($coin) {
            $coin['LogoUrl'] = $this->getCoinLogo($coin['Name']);
            return $coin;
        });
        return $coins;
    }

}

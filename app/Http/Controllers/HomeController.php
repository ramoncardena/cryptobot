<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Library\Services\Bittrex\Bittrex;
use App\Library\Services\Bitcoin\Bitcoin;

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
           
           $settings = settings();

           $coins = $this->getCoins();

           $totals = $this->getTotals($coins);

           $fiat = $settings->fiat;

           return view('home', ['coins' => $coins, 'totals' => $totals, 'fiat' => $fiat]);

        }
        else {
            throw new Exception ("You must be authenticated to get your settings");
        }
    }
    
    protected function getCoinLogo( $coin ) {

        if ( $coin != 'BTC') {

            $bittrex = new Bittrex($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));

            $market = $bittrex->getMarkets();

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

        $bittrex = new Bittrex($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));

        $this->getCoinLogo('BTC');

        // Get balance
        $balances= $bittrex->getBalances();
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
            $bittrex = new Bittrex($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));
            if ($coin['Name'] != 'BTC') {
                $ticker = $bittrex->getTicker('BTC-'. $coin['Name']);
                $coin['Price'] = round($ticker->result->Last, 8);
                return $coin;
            } else {
                $coin['Price'] = $coin['Balance'];
                return $coin;
            }
            
        });

        // Get value in BTC for each item in portfolio (ignoring BTC item)
        $coins = $coins->map(function ($coin) {
            $bittrex = new Bittrex($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));
            if ($coin['Name'] != 'BTC') {
                $ticker = $bittrex->getTicker('BTC-'. $coin['Name']);
                $coin['BTC-Value'] = round($coin['Balance'] * $ticker->result->Last, 8);
                return $coin;
            } else {
                $coin['BTC-Value'] = $coin['Balance'];
                return $coin;
            }
            
        });

        // Get value in EUR for each item in portfolio
        $coins = $coins->map(function ($coin) {
            $bitcoin = new Bitcoin();
            $btcTicker = $bitcoin->getTicker();
            $coin['EUR-Value'] = round($coin['BTC-Value'] * $btcTicker->EUR->last, 2);
            return $coin; 
        });

        // Get value in USD for each item in portfolio
        $coins = $coins->map(function ($coin) {
            $bitcoin = new Bitcoin();
            $btcTicker = $bitcoin->getTicker();
            $coin['USD-Value'] = round($coin['BTC-Value'] * $btcTicker->USD->last, 2);
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

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Library\Services\Facades\Bittrex;
use App\Library\Services\Facades\Bitcoin;

use App\Library\Services\CoinGuru;

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
            // Check cryptocompare remaining calls
            // $cryptocompareApi = new \Cryptocompare\CryptocompareApi();
            // $response =  $cryptocompareApi->getRateLimits("second");
            // dd($response);
            
            $this->user = Auth::user();

            Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));

            $coins = $this->getCoins();

            $totals = $this->getTotals($coins);

            $fiat = $this->user->settings()->fiat;

            $guru = new CoinGuru;
                $coinList = $guru->cryptocompareCoingetList();
                dd($coinList->Data->ROS);

            // $myexchange = '\\ccxt\\' . 'bittrex';

            // date_default_timezone_set ('UTC');
            // $bittrex  = new $myexchange  (array (
            //     'apiKey' => $this->user->settings()->get('bittrex_key'),
            //     'secret' => $this->user->settings()->get('bittrex_secret'),
            // ));
            // $balance = $bittrex->fetch_balance();

            // $allBalances = $balance['total'];
            
            // $nonZeroBalances = array_where($allBalances, function ($value, $key) {
            //     return $value > 0;
            // });

            // dd($nonZeroBalances);
            // dd(\ccxt\Exchange::$exchanges);

            return view('home', ['coins' => $coins, 'totals' => $totals, 'fiat' => $fiat]);

        }
        else {
            throw new Exception ("You must be authenticated to get your settings");
        }
    }

    protected function ParseFloat($floatString){
        $LocaleInfo = localeconv();
        $floatString = str_replace($LocaleInfo["mon_thousands_sep"] , "", $floatString);
        $floatString = str_replace($LocaleInfo["mon_decimal_point"] , ".", $floatString);
        return floatval($floatString);
    } 
    
    protected function getCoinLogo( $coin ) {

        if ( $coin != 'BTC') {

            $moneda = $coin;

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

        $eur = $coins->pluck('EUR-Value');
        $usd = $coins->pluck('USD-Value');
        $btc = $coins->pluck('BTC-Value');

        $eur = $eur->map(function ($item, $key) {
            return floatval(str_replace(',', '', $item));
        });
        $usd = $usd->map(function ($item, $key) {
            return floatval(str_replace(',', '', $item));
        });
        $btc = $btc->map(function ($item, $key) {
            return floatval(str_replace(',', '', $item));
        });



        $totals = ['BTC' => $btc->sum(), 'USD' => $usd->sum(), 'EUR' => $eur->sum()];

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

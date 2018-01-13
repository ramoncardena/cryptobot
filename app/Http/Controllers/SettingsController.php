<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Auth;

use App\Portfolio;
use App\PortfolioOrigin;
use App\User;
use App\Connection;

class SettingsController extends Controller
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
        // Get settings
        $settings = settings();

        // Decrypt exchange keys
        $bittrex['bittrex_key'] = $settings->get('bittrex_key');
        $bittrex['bittrex_secret'] = $settings->get('bittrex_secret');    
        $bittrex['bittrex_fee'] = $settings->get('bittrex_fee');      

        $bitstamp['bitstamp_key'] = $settings->get('bitstamp_key');
        $bitstamp['bitstamp_secret'] = $settings->get('bitstamp_secret');    
        $bitstamp['bitstamp_fee'] = $settings->get('bitstamp_fee');   

        // Get exchange lists
        $exchanges_active  = $settings->get('exchanges');                                                   

        // Get portfolio
        $portfolio = Portfolio::where('user_id', Auth::user()->id)->first();

        // Get exchange connections
        $connections = Auth::user()->connections;

        // CCTX Exchange list
        $cctx_exchanges = \ccxt\Exchange::$exchanges;

        return view('settings', ['settings' => $settings->all(), 'bittrex' => $bittrex, 'bitstamp' => $bitstamp, 'exchanges_active' => $exchanges_active, 'portfolio' => $portfolio, 'cctx_exchanges' => $cctx_exchanges, 'connections' => $connections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $settings = settings();

        $settings->set('exchanges', null);

        $bitstampSwitch=false;
        $bittrexSwitch=false;

        $newExchange = null;
        $newExchangeApi = null;
        $newExchangeSecret = null;
        $newExchangeFee = null;

        $user = Auth::user();

        foreach ($request->all() as $key => $value) 
        {
            switch ($key) {
                case 'new_exchange':
                    $newExchange = $value;
                    break;

                case 'new_exchange_api_key':
                    $newExchangeApi = $value;
                    break;

                case 'new_exchange_api_secret':
                    $newExchangeSecret = $value;
                    break;

                case 'new_exchange_fee':
                    $newExchangeFee = $value;
                    break;

                case 'bittrex_switch':
                    $settings->addExchange('bittrex');

                    $origins =  $user->origins;

                    if ($origins->firstWhere('name', 'Bittrex') == null) {
                        $portfolio = Portfolio::where('user_id', $user->id)->first();
                        $origin = new PortfolioOrigin;
                        $origin->portfolio_id = $portfolio->id;
                        $origin->user_id = $user->id;
                        $origin->type ='Exchange';
                        $origin->name = 'Bittrex';
                        $origin->address = "-";
                        $origin->save();
                    }

                    $bittrexSwitch = true;

                    break;

                case 'bitstamp_switch':
                    $settings->addExchange('bitstamp');

                    $origins =  $user->origins;

                    if ($origins->firstWhere('name', 'Bitstamp') == null) {
                        $portfolio = Portfolio::where('user_id', $user->id)->first();
                        $origin = new PortfolioOrigin;
                        $origin->portfolio_id = $portfolio->id;
                        $origin->user_id = $user->id;
                        $origin->type ='Exchange';
                        $origin->name = 'Bitstamp';
                        $origin->address = "-";
                        $origin->save();
                    }   

                    $bitstampSwitch = true;

                    break;

                case 'initialize_portfolio':
                    $portfolio = new Portfolio;
                    $portfolio->user_id = $user->id;
                    $portfolio->name = "My Portfolio";
                    $portfolio->counter_value = "eur";
                    $portfolio->balance = 0;
                    $portfolio->balance_counter_value = 0;
                    $portfolio->asset_count = 0;
                    $portfolio->update_id = "-";
                    $portfolio->save();
                    break;

                case 'portfolio_name':
                    if (Portfolio::where('user_id', $user->id)->first()) {
                        $portfolio = Portfolio::where('user_id', $user->id)->first();
                        $portfolio->name = $value;
                        $portfolio->save();
                    }
                    break;

                case 'portfolio_countervalue':
                    if (Portfolio::where('user_id', $user->id)->first()) {
                        $portfolio = Portfolio::where('user_id', $user->id)->first();
                        $portfolio->counter_value = $value;
                        $portfolio->save();
                    }
                    break;
                default:
                    if ($key !== "_token") {
                        $settings->set($key, $value);
                    }
                    break;
            }
        }

        if ($newExchange && $newExchangeApi && $newExchangeSecret) {
            $connection = new Connection;
            $connection->user_id = Auth::user()->id;
            $connection->exchange = $newExchange;
            $connection->api = $newExchangeApi;
            $connection->secret = $newExchangeSecret;
            $newExchangeFee ? $connection->fee = $newExchangeFee : $connection->fee = 0;
            $connection->active = true;
            $connection->save();
        }


        if ($bittrexSwitch == false) {
            if ($user->origins->firstWhere('name', 'Bittrex')) {
                // BORRAR ASSETS ASOCIADOS
                $assets = $user->assets->where('origin_name', 'Bittrex');
                foreach ($assets as $asset) {
                    $asset->delete();  
                }
                $user->origins->firstWhere('name', 'Bittrex')->delete();
            }
        }
        if ($bitstampSwitch == false) {
            if ($user->origins->firstWhere('name', 'Bitstamp')) {
                // BORRAR ASSETS ASOCIADOS
                $assets = $user->assets->where('origin_name', 'Bitstamp');
                foreach ($assets as $asset) {
                    $asset->delete();  
                }
                $user->origins->firstWhere('name', 'Bitstamp')->delete();
            }
        }

        return redirect('/settings');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($setting)
    {
        $settings = settings();
        return $settings->get($setting);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

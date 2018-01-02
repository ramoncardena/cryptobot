<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Events\PortfolioOpened;

use App\Library\Services\Broker;
use App\Library\Services\CoinGuru;

use App\User;
use App\Portfolio;
use App\PortfolioAsset;
use App\PortfolioOrigin;

class PortfolioController extends Controller
{

    public $user;

    public $portfolio;

    public $assets;

    public $origins;

    public $exchanges;

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
        
        // Get current user
        $this->user = Auth::user();

        // Get the user's exchanges
        $exchanges = $this->user->settings()->get('exchanges');
        if ($exchanges) $this->exchanges = array_divide($exchanges)[0];
        else $this->exchanges = [];

        // Get user's portfolio
        $this->portfolio = Portfolio::where('user_id', $this->user->id)->first();

        // Get portfolio origins
        $this->origins = $this->portfolio->origins; 

        // EVENT:  PortfolioOpened
        event(new PortfolioOpened($this->portfolio));


        // DATA FOR MODALS (New Asset and New Origin)
        // Coin list
        $guru = new CoinGuru;
        $coins = array_divide((array)$guru->cryptocompareCoingetList()->Data)[0];

        // Set origin types for new Portfolio Origins
        $originTypes = ['Online Wallet', 'Mobile Wallet', 'Desktop Wallet', 'Hardware Wallet', 'Paper Wallet'];

    
        return view('portfolio', ['originTypes' => json_encode($originTypes), 'exchanges' => json_encode($this->exchanges), 'portfolio' => $this->portfolio, 'origins' => $this->origins, 'coins' => json_encode($coins)]);
    }


    public function updateAssets() {
       
        $guru = new CoinGuru;

        foreach ($this->assets as $asset) {
            $asset->balance =  $asset->amount * $guru->cryptocomparePriceGetSinglePrice($asset->symbol, "BTC")->BTC;

            $counterValue = strtoupper($this->portfolio->counter_value);
            $asset->counter_value = $asset->amount * $guru->cryptocomparePriceGetSinglePrice($asset->symbol, $counterValue)->$counterValue;
            $asset->save();
        }

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function liveReload()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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

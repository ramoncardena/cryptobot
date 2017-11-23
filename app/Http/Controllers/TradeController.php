<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Library\Services\Facades\Bittrex;
use App\Trade;
use App\Stop;
use App\Profit;

class TradeController extends Controller
{
    protected $user;
    protected $trade;
    protected $order_id;

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

            $tradesHistory = Trade::where('user_id',  Auth::id())
                ->orderBy('updated_at', 'desc')
                ->get();
            $tradesHistory = $tradesHistory->reject(function ($trade) {
                return $trade->status == 'Opened';
            });

            $tradesOpened = Trade::where('user_id',  Auth::id())
               ->where('status', 'Opened')
               ->orderBy('updated_at', 'desc')
               ->get();


            return view('trades', ['tradesHistory' => $tradesHistory, 'tradesOpened' => $tradesOpened]);
        }
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
        if (Auth::check()) 
        {
            $this->trade = new Trade;

            $this->trade->order_id = "-";
            $this->trade->stop_id = "-";
            $this->trade->profit_id = "-";
            $this->trade->user_id = Auth::id();
            $this->trade->status = "Opening";
            $this->trade->position = $request->position;
            $this->trade->exchange = $request->exchange;
            $this->trade->pair = $request->pair;
            $this->trade->price = $request->price;
            $this->trade->amount = floatval($request->amount);
            $this->trade->total = $request->total;
            $this->trade->stop_loss = $request->stop_loss;
            $this->trade->take_profit = $request->take_profit;
            $this->trade->profit = 0;
            $this->trade->save();

            $order = $this->newOrder($request->exchange, $request->pair, $request->price,$request->amount, $request->position);
            
            $this->trade->order_id = $order['order_id'];
            $this->trade->stop_id = $order['stop_id'];
            $this->trade->profit_id = $order['profit_id'];
            $this->trade->status = "Opened";
            $this->trade->save();


        }
        
        return redirect('/trades')->with('status', 'Trade opened!');
        //return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($trade)
    {
        
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

    private function newOrder($exchange, $pair, $price, $amount, $position) 
    {
        $stop = new Stop;
        $profit = new Profit;

        $order_id = "-";
        // MISSING: Create new order and get order ID, then set stop-loss and take-profit

        // Stop-Loss
        $stop->trade_id = $this->trade->id;
        $stop->order_id = $order_id;
        $stop->exchange = $exchange;
        $stop->pair = $pair;
        $stop->price = $price;
        $stop->amount = $amount;
        if ($position = 'long') {
            $stop->type = 'sell';
        }
        else if ($position = 'short') {
            $stop->type = 'buy';
        }
        $stop->save();

        // Take-Profit
        $profit->trade_id = $this->trade->id;
        $profit->order_id = $order_id;
        $profit->exchange = $exchange;
        $profit->pair = $pair;
        $profit->price = $price;
        $profit->amount = $amount;
        if ($position = 'long') {
            $profit->type = 'sell';
        }
        else if ($position = 'short') {
           $profit->type = 'buy';
        }
        $profit->save();

        return ['order_id' => $order_id, 'stop_id' => $stop->id, 'profit_id' => $profit->id];
    }
}

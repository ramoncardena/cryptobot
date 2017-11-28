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
            // Get current authenticated user
            $this->user = Auth::user();

            // Retrieve trade history
            $tradesHistory = Trade::where('user_id',  Auth::id())
                ->orderBy('updated_at', 'desc')
                ->get();
            $tradesHistory = $tradesHistory->reject(function ($trade) {
                return $trade->status == 'Opened';
            });

            // Retrieve open trades
            $tradesOpened = Trade::where('user_id',  Auth::id())
               ->where('status', 'Opened')
               ->orderBy('updated_at', 'desc')
               ->get();

            // Return 'trades' view passing trade history and open trades objects
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

            $order = $this->newOrder($request->exchange, $request->pair, $request->price,$request->amount, $request->stop_loss, $request->take_profit, $request->position);
            
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

    private function newOrder($exchange, $pair, $price, $amount, $stop, $profit, $position) 
    {
        $stopLoss = new Stop;
        $takeProfit = new Profit;

        $order_id = "-";
        // MISSING: Create new order and get order ID, then set stop-loss and take-profit

        // If order executed:
        // Stop-Loss
        $stopLoss->trade_id = $this->trade->id;
        $stopLoss->order_id = $order_id;
        $stopLoss->status = "Opened";
        $stopLoss->exchange = $exchange;
        $stopLoss->pair = $pair;
        $stopLoss->price = $stop;
        $stopLoss->amount = $amount;
        if ($position = 'long') {
            $stopLoss->type = 'sell';
        }
        else if ($position = 'short') {
            $stopLoss->type = 'buy';
        }
        $stopLoss->save();

        // Take-Profit
        $takeProfit->trade_id = $this->trade->id;
        $takeProfit->order_id = $order_id;
        $takeProfit->status = "Opened";
        $takeProfit->exchange = $exchange;
        $takeProfit->pair = $pair;
        $takeProfit->price = $profit;
        $takeProfit->amount = $amount;
        if ($position = 'long') {
            $takeProfit->type = 'sell';
        }
        else if ($position = 'short') {
           $takeProfit->type = 'buy';
        }
        $takeProfit->save();

        return ['order_id' => $order_id, 'stop_id' => $stopLoss->id, 'profit_id' => $takeProfit->id];
    }
}

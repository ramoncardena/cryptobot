<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Library\Services\Facades\Bittrex;
use App\Trade;
use App\Stop;
use App\Profit;
use App\Conditional;
use App\User;

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
        try {
            // Double check for user to be authenticated
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
                $tradesHistory = $tradesHistory->reject(function ($trade) {
                    return $trade->status == 'Waiting';
                });

                // Retrieve open trades
                $tradesOpened = Trade::where('user_id',  Auth::id())
                   ->where('status', 'Opened')
                   ->orderBy('updated_at', 'desc')
                   ->get();

                // Retrieve waiting trades
                $tradesWaiting = Trade::where('user_id',  Auth::id())
                   ->where('status', 'Waiting')
                   ->orderBy('updated_at', 'desc')
                   ->get();

                // Return 'trades' view passing trade history and open trades objects
                return view('trades', ['tradesHistory' => $tradesHistory, 'tradesOpened' => $tradesOpened, 'tradesWaiting' => $tradesWaiting]);
            }
            else {
                Log::error("User not authorized trying to retieve trades.");
            }
        }catch(\Exception $e) {
                Log::critical("Exception: " . $e->getMessage());
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
        // Double check for user to be authenticated
        if (Auth::check()) 
        {
            // Create new Trade model
            $this->trade = new Trade;

            // Fill the new Trade model 
            $this->trade->order_id = "-";
            $this->trade->stop_id = "-";
            $this->trade->profit_id = "-";
            $this->trade->condition_id = "-";
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
            $this->trade->condition = $request->condition;
            $this->trade->condition_price = $request->condition_price;
            $this->trade->profit = 0.00000000;
            $this->trade->closing_price = 0.00000000;
            $this->trade->save();

            // Chech if conditional order
            if ($this->trade->condition == "now") {

                // No conditional order
                // Launch order to the exchange to get the order uuid
                $order = $this->newOrder($request->exchange, $request->pair, $request->price,$request->amount, $request->stop_loss, $request->take_profit, $request->position);

                if ($order['status'] == 'success') {

                    // If order succeeds fill the order id, stop id and profit id in the trade
                    // and set status as 'Opened'
                    $this->trade->order_id = $order['order_id'];
                    $this->trade->stop_id = $order['stop_id'];
                    $this->trade->profit_id = $order['profit_id'];
                    $this->trade->status = "Opened";
                    $this->trade->save();

                    $res = '#' . $this->trade->id . ' Trade Opened.' . 'Exchange: ' . $this->trade->exchange . ' Pair: ' . $this->trade->pair . ' Price: ' . $this->trade->price . ' Amount: ' . $this->trade->amount . ' Total: ' . $this->trade->total .' Stop-Loss: ' . $this->trade->stop . ' Take-Profit: ' . $this->trade->profit;

                    return response($res , 200)->header('Content-Type', 'text/plain');
               
                } else if ($order['status'] == 'fail') {

                    // If order fails set trade status as 'Aborted'
                    $this->trade->status = "Aborted";
                    $this->trade->save();

                    return response($order['message'], 500)->header('Content-Type', 'text/plain');
                }
            }
            else {
                // Conditional order
                
                // Stores a conditional order in the database to watch
                $conditional = $this->newConditional($request->exchange, $request->pair, $request->condition, $request->condition_price);

                if ($conditional['status'] == 'success') {

                    // Create a condition to be watched and lauched when reached
                    $this->condition_id = $conditional['conditional_id'];
                    $this->trade->status = "Waiting";
                    $this->trade->save();

                    $res = '#' . $this->trade->id . ' Conditional Trade Waiting.' . 'Exchange: ' . $this->trade->exchange . ' Pair: ' . $this->trade->pair . ' Price: ' . $this->trade->price . ' Amount: ' . $this->trade->amount . ' Total: ' . $this->trade->total .' Stop-Loss: ' . $this->trade->stop . ' Take-Profit: ' . $this->trade->profit . ' Condition: ' . $this->trade->condition . ' Condition Price: ' . $this->trade->condition_price;

                    return response($res , 200)->header('Content-Type', 'text/plain');


                } else if ($conditional['status'] == 'fail') {

                    // If order fails set trade status as 'Aborted'
                    $this->trade->status = "Aborted";
                    $this->trade->save();

                    return response($conditional['message'], 500)->header('Content-Type', 'text/plain');
                }
            }
        }
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
     * /Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->trade = Trade::find($id);

        return response("Trade " . $id . " closed." , 200)->header('Content-Type', 'text/plain');
    }

    /**
     * /Launch new order 
     * @param  string $exchange
     * @param  string $pair    
     * @param  float $price   
     * @param  float $amount  
     * @param  float $stop    
     * @param  float $profit  
     * @param  string $position
     * @return array         
     */
    private function newOrder($exchange, $pair, $price, $amount, $stop, $profit, $position) 
    {
        $stopLoss = new Stop;
        $takeProfit = new Profit;

        // Get the current user
        $user = User::find(Auth::id());

        $order_id = "-";

        switch ($exchange) {
            case 'bittrex':
                // Initialize Bittrex with user info
                Bittrex::setAPI($user->settings()->get('bittrex_key'), $user->settings()->get('bittrex_secret'));

                // Launch Bittrex sell order with Pair, Amount and Price as parameters
                // $order = Bittrex::buyLimit($pair, $amount, $price);
             
                // TESTING SUCCESS
                $order = new \stdClass();
                $order->success=true;
                $order->message="";
                $order->result = new \stdClass();
                $order->result->uuid = "7c6db929-6c4f-4711-b99b-01c9697330ce";

                // TESTING FAIL
                // $order = new \stdClass();
                // $order->success=false;
                // $order->message="Invalid API credentials";
                // $order->result = new \stdClass();
                // $order->result->uuid = "";

                // Check for order success
                if ($order->success == true) {

                    // Get order UUID
                    $order_id = $order->result->uuid;

                    // Create a new Stop-Loss instance in the DB
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

                    // Creat a new Take-Profit instance in the DB
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

                    return ['status' => 'success', 'order_id' => $order_id, 'stop_id' => $stopLoss->id, 'profit_id' => $takeProfit->id];
                }
                else {
                    return ['status' => 'fail', 'message' => $order->message];
                }

                break;
        }
    }

    /**
     * Stores a new Conditional order in conditionals table
     * @param  string $exchange 
     * @param  string $pair   
     * @param  string $condition 
     * @param  float $conditionprice  
     * @return array        
     */
    private function newConditional($exchange, $pair, $condition, $conditionprice) 
    {   
        $conditional = new Conditional;

        $conditional->user_id = Auth::id();
        $conditional->trade_id = $this->trade->id;
        $conditional->exchange = $exchange;
        $conditional->pair = $pair;
        $conditional->condition = $condition;
        $conditional->condition_price = $conditionprice;
        if ($conditional->save()) {
             return ["status"=>"success", "conditional_id"=>$conditional->id];
        }
        else {
             return ["status"=>"fail", "message"=>"Error creating conditional trade."];
        }
       
    }
}

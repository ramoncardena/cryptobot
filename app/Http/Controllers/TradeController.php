<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Broker;
use App\Library\FakeOrder;
use App\Notifications\TradeEditedNotification;
use App\Events\TakeProfitLaunched;
use App\Events\StopLossLaunched;
use App\Events\OrderLaunched;
use App\Events\ConditionalLaunched;
use App\Trade;
use App\Stop;
use App\Profit;
use App\Conditional;
use App\User;
use App\Order;

class TradeController extends Controller
{
    /**
     * Owner of the trade
     * @var User
     */
    protected $user;
  
    /**
     * Trade being controlled
     * @var Trade
     */
    protected $trade;
  
    /**
     * ID for the order launched by trade to the exchange
     * @var string
     */
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
     * Show the trades dashboard.
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
                    ->where([
                        ['status', '=', 'Closed'] 
                    ])
                    ->orWhere([
                        ['status', '=', 'Cancelled']
                    ])
                    ->orWhere([
                        ['status', '=', 'Aborted']
                    ])
                    ->orWhere([
                        ['status', '=', 'Cancelling']
                    ])
                    ->orWhere([
                        ['status', '=', 'Closing']
                    ])
                    ->orderBy('updated_at', 'desc')
                    ->get();

                // Retrieve active trades

                $tradesActive = Trade::where('user_id',  Auth::id())
                    ->where([
                        ['status', '=', 'Opening'] 
                    ])
                    ->orWhere([
                        ['status', '=', 'Waiting']
                    ])
                    ->orWhere([
                        ['status', '=', 'Opened']
                    ])
                    ->orderBy('updated_at', 'desc')
                    ->get();

                // Return 'trades' view passing trade history and open trades objects
                return view('trades', ['tradesActive' => $tradesActive, 'tradesHistory' => $tradesHistory]);
            }
            else {

                // LOG: Not authorized
                Log::error("User not authorized trying to retieve trades.");

            }
        }catch(Exception $e) {

                // LOG: Exception trying to show trades
                Log::critical("[TradeController] Exception: " . $e->getMessage());

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        try {
            // Double check for user to be authenticated
            if ( Auth::check() ) 
            {
                // Validate form
                $validatedData = $request->validate([
                    'exchange' => 'required',
                    'pair' => 'required',
                    'price' => 'required | numeric | min:0.00000001',
                    'amount' => 'required | numeric | min:0.00000001',
                    'total' => 'required | numeric | min:0.00000001',
                    'stop_loss' => 'numeric | min:0',
                    'take_profit' => 'numeric | min:0',
                    'condition_price' => 'numeric | min:0',
                ]);
                
                // Create new Trade model
                $this->trade = new Trade;

                // Trade status change
                $this->trade->status = "Opening";

                // Fill the properties of the new trade that
                // are common to conditional and not conditional
                $this->trade->user_id = Auth::id();
                $this->trade->order_id = "-";
                $this->trade->stop_id = "-";
                $this->trade->profit_id = "-";
                $this->trade->condition_id = "-";
                $this->trade->position = "long";
                $this->trade->exchange = $request->exchange;
                $this->trade->pair = $request->pair;
                $this->trade->price = $request->price;
                $this->trade->amount = floatval($request->amount);
                $this->trade->total = $request->total;
                $this->trade->stop_loss = $request->slSwitch ? $request->stop_loss : 0;
                $this->trade->take_profit = $request->tpSwitch ? $request->take_profit : 0;
                $this->trade->condition = $request->condition ? $request->condition : "now";
                $this->trade->condition_price = $request->condition_price;
                $this->trade->profit = 0.00000000;
                $this->trade->closing_price = 0.00000000;
                $this->trade->save();

                if ( $this->trade->condition == "now" ) {
                     /**********************************
                     *  INMEDIATE TRADE
                     **********************************/

                    // Launch order to the exchange to get the order uuid
                    $order = $this->newOrder($this->trade);

                    if ( $order['status'] == 'success' ) {

                        // LOG: Order created
                        Log::info("[TradeController] Order #" . $this->trade->order_id . " created for Trade #" . $this->trade->id);

                        // SESSION FLASH: New Trade
                        $request->session()->flash('status-text', 'New trade for ' . $this->trade->pair . ' launched!');
                         $request->session()->flash('status-class', 'success');

                        // Send the new trade to the client in json
                        //return response($this->trade->toJson(), 200)->header('Content-Type', 'application/json');
                        return redirect('/trades');

                    } else if ( $order['status'] == 'fail' ) {
                        
                        // LOG: Error creating order
                        Log::critical("[TradeController] Error creating Order for Trade #" . $this->trade->id . ": " . $order['message']);

                        // SESSION FLASH: New Trade Fails
                        $request->session()->flash('status-text', 'Error launching new trade for ' . $this->trade->pair . ': ' . $order['message']);
                        $request->session()->flash('status-class', 'alert');

                       
                        // return response($order['message'], 500)->header('Content-Type', 'text/plain');
                        return redirect('/trades');
                    }
                }
                /**********************************
                 *  CONDITIONAL TRADE
                 **********************************/
                else {
                    
                    // Stores a conditional order in the database to watch
                    $conditional = $this->newConditional($this->trade);

                    if ( $conditional['status'] == 'success' ) {
                        
                        // LOG: Conditional order created
                        Log::info("[TradeController] Conditional order #" . $this->trade->condition_id . " created for Trade #" . $this->trade->id);

                        // SESSION FLASH: New Trade
                        $request->session()->flash('status-text', 'New conditional trade for ' . $this->trade->pair . ' launched!');
                        $request->session()->flash('status-class', 'success');

                        // Send the new trade to the client in json
                        //return response($this->trade->toJson(), 200)->header('Content-Type', 'application/json');
                        return redirect('/trades');

                    } else if ( $conditional['status'] == 'fail' ) {
                        // Error creating conditional
                        
                        // LOG: Error creating order
                        Log::critical("[TradeController] Error creating conditional for Trade #" . $this->trade->id . ": " . $conditional['message']);

                        // SESSION FLASH: New Trade Fails
                        $request->session()->flash('status-text', 'Error launching new conditional trade for ' . $this->trade->pair . ': ' . $conditional['message']);
                        $request->session()->flash('status-class', 'alert');
                       
                        //return response($conditional['message'], 500 )->header( 'Content-Type', 'text/plain');
                        return redirect('/trades');
                    }
                }
            }
        } catch (Exception $e) {
            
            // LOG: Exception trying to create trade
            Log::critical("[TradeController] Exception: " . $e->getMessage());

            return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');

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
        try {
            // Validate form
            $validatedData = $request->validate([
                'newStopLoss' => 'numeric | min:0',
                'newTakeProfit' => 'numeric | min:0',
            ]);

            // Get the trade to edit
            $this->trade = Trade::find($id);

            // Process new Stop-Loss
            if( $this->trade->stop_id == "-" ) {

                if($request->newStopLoss != 0) 
                {
                    // Create a new Stop-Loss instance in the DB
                    $stopLoss = new Stop;

                    $stopLoss->trade_id = $this->trade->id;
                    $stopLoss->order_id = $this->trade->order_id;
                    $stopLoss->status = "Opened";
                    $stopLoss->exchange = $this->trade->exchange;
                    $stopLoss->pair = $this->trade->pair;
                    $stopLoss->price = $request->newStopLoss;
                    $stopLoss->amount = $this->trade->amount;
                    $stopLoss->cancel = false;

                    if ($this->trade->position = 'long') {

                        $stopLoss->type = 'sell';

                    }
                    else if ($this->trade->position = 'short') {

                        $stopLoss->type = 'buy';

                    }

                    $stopLoss->save();

                    // Update trade with stop-loss id and value
                    $this->trade->stop_id = $stopLoss->id;
                    $this->trade->stop_loss = $request->newStopLoss;
                    $this->trade->save();

                    // Log INFO: Stop-Loss launched
                    Log::info("Stop-Loss #" . $stopLoss->id . " launched by Trade #" . $this->trade->id);

                    // EVENT: StopLossLaunched
                    event(new StopLossLaunched($stopLoss));

                }

            }
            else {

                // Find stop-loss
                $stop = Stop::find($this->trade->stop_id);

                // If new stop-loss is 0 cancel stop-loss
                if ($request->newStopLoss == 0) 
                {
                    
                    $stop->cancel = true;
                    $stop->save();

                    $this->trade->stop_id = "-";
                    $this->trade->save();


                }
                else
                {
                    // Update stop-loss in db
                    $stop->price = $request->newStopLoss;
                    $stop->save();
                }

                // Update stop-loss value in trade
                $this->trade->stop_loss = $request->newStopLoss;
                $this->trade->save();

            }
           
            // Process new Take Profit
            if ( ($this->trade->profit_id == "-") ) 
            {
                 Log::info("NEW TAKE PROFIT: " . $request->newTakeProfit);
                if ($request->newTakeProfit != 0)
                {

                    $takeProfit = new Profit;

                    $takeProfit->trade_id = $this->trade->id;
                    $takeProfit->order_id = $this->trade->order_id;
                    $takeProfit->status = "Opened";
                    $takeProfit->exchange = $this->trade->exchange;
                    $takeProfit->pair = $this->trade->pair;
                    $takeProfit->price = $request->newTakeProfit;
                    $takeProfit->amount = $this->trade->amount;
                    $takeProfit->cancel = false;

                    if ($this->trade->position = 'long') {

                        $takeProfit->type = 'sell';

                    }
                    else if ($this->trade->position = 'short') {

                       $takeProfit->type = 'buy';

                    }

                    $takeProfit->save();

                    // Update trade with take-profit id and value
                    $this->trade->profit_id = $takeProfit->id;
                    $this->trade->take_profit = $request->newTakeProfit;
                    $this->trade->save();


                    // Log INFO: Take-Profit launched
                    Log::info("Take-Profit #" . $takeProfit->id . " launched by Trade #" . $this->trade->id);
                   
                    // EVENT: TakeProfitLaunched
                    event(new TakeProfitLaunched($takeProfit));
                }
            }
            else
            {
                // Find take-profit
                $profit = Profit::find($this->trade->profit_id);

                // If new stop-loss is 0 cancel stop-loss
                if ($request->newTakeProfit == 0) 
                {

                    $profit->cancel = true;
                    $profit->save();

                    $this->trade->profit_id = "-";
                    $this->trade->save();

                }
                else 
                {

                    // Update take-profit in db
                    $profit->price = $request->newTakeProfit;
                    $profit->save();

                }

                // Update take-profit value in trade
                $this->trade->take_profit = $request->newTakeProfit;
                $this->trade->save();
            }

            
            // NOTIFY: Trade Edited
            User::find($this->trade->user_id)->notify(new TradeEditedNotification($this->trade));

            // SESSION FLASH: New Trade
            $request->session()->flash('status-text', 'Trade on pair ' . $this->trade->pair . ' edited!');
            $request->session()->flash('status-class', 'success');

            // Log NOTICE: Trade edited
            Log::notice("Trade #" . $this->trade->id . " edited. New Stop-Loss at " . $this->trade->stop_loss . " and new Take-Profit at " . $this->trade->take_profit);

            return redirect('/trades');

        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[TradeController] Exception: " . $e->message());

            // If exeption return error 500
            return response($e->message(), 500)->header('Content-Type', 'text/plain');

        }
        
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {

            // Validate form
            $validatedData = $request->validate([
                'closingprice' => 'numeric | min:0',
            ]);

            // Get the trade to close
            $this->trade = Trade::find($id);


            /*****************************************
             *  CANCEL CONDITIONAL TRADE WAITING
             *****************************************/
            
            if ($this->trade->status == 'Waiting') {

                // Update trade status
                $this->trade->status = "Cancelling";
                $this->trade->save();

                // Set conditional to cancel
                $conditional = Conditional::find($this->trade->condition_id);
                $conditional->cancel = true;
                $conditional->save();

                // SESSION FLASH: New Trade
                $request->session()->flash('status-text', 'Conditional trade on pair ' . $this->trade->pair . ' canceled!');
                $request->session()->flash('status-class', 'success');

                // Send the cancelled trade to the client in json
                // return response($this->trade->toJson(), 200)->header('Content-Type', 'application/json');
                return redirect('/trades');
 
            }

            /*****************************************
             *  CLOSE OPENED TRADE
             *****************************************/
            else {

                // Update trade status
                $this->trade->status = "Closing";
                $this->trade->save();
                
                // Update stop-loss status if it exists
                $stop = Stop::find( $this->trade->stop_id);
                if ($stop) {
                    $stop->status = "Closing";
                    $stop->cancel = true;
                    $stop->save();
                }

                // Update take-profit status if it exists
                $profit = Profit::find( $this->trade->profit_id);
                if ($profit) {
                    $profit->status = "Closing";
                    $profit->cancel = true;
                    $profit->save();
                }

                // Get the user linked to the trade
                $user = User::find(Auth::id());

                // Check for order type
                if ($this->trade->position == "long") {
   
                    // Call to exchange API or a fakeOrder based on ENV->ORDERS_TEST
                    if ( env('ORDERS_TEST', true) == true ) {

                        // TESTING SUCCESS
                        $remoteOrder = FakeOrder::success();

                        // TESTING FAIL
                        // $order = FakeOrder::fail();
                        
                    }
                    else {

                        // SELL ORDER
                        $broker = new Broker;
                        $broker->setUser($user);
                        $broker->setExchange($this->trade->exchange);
                        $remoteOrder = $broker->sellLimit($this->trade->pair, $this->trade->amount, $request->closingprice);
                        
                    }
                    
                    // Check for remoteOrder success
                    if ($remoteOrder->success == true) {

                        // If we get a success response we create an Order in our database to track
                        $order = new Order;
                        $order->user_id = $this->trade->user_id;
                        $order->trade_id = $this->trade->id;
                        $order->exchange = 'bittrex';
                        $order->order_id = $remoteOrder->result->uuid;
                        $order->type = 'close';
                        $order->save();

                        // EVENT: OrderLaunched
                        event(new OrderLaunched($order));

                        // Log NOTICE: Order Launched
                        Log::notice("Order Launched: User action closing trade launched a SELL order (#" . $order->id .") at " . $request->closingprice  . " for trade #" . $this->trade->id . " for the pair " . $this->trade->pair . " at " . $this->trade->exchange);
                        
                        // SESSION FLASH: New Trade
                        $request->session()->flash('status-text', 'Cancel launched for trade on pair ' .$this->trade->pair);
                        $request->session()->flash('status-class', 'success');
                        
                        //return response($this->trade->toJson(), 200)->header('Content-Type', 'application/json');
                        return redirect('/trades');

                    }
                    else {

                        // Log ERROR: Bittrex API returned error
                        Log::error("[TradeController] Bittrex API: " . $remoteOrder->message);

                        // SESSION FLASH: New Trade Fails
                        $request->session()->flash('status-text', 'Error launching cancel order for trade on pair ' . $this->trade->pair . ': ' . $order['message']);
                        $request->session()->flash('status-class', 'alert');

                        // return response($remoteOrder->message, 500)->header('Content-Type', 'text/plain');
                        return redirect("/trades");

                    }

                }
        
            }
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[TradeController] Exception: " . $e->message());

            // If exeption return error 500
            return response($e->message(), 500)->header('Content-Type', 'text/plain');

        }
        

    }


    /**
     * Launch new order to the exchange
     * @param  trade $trade
     * @return array         
     */
    private function newOrder($trade) 
    {
        try {

            $stopLoss = new Stop;
            $takeProfit = new Profit;

            // Get the current user
            $user = User::find(Auth::id());

            // Call to exchange API or a fakeOrder based on ENV->ORDERS_TEST
            if ( env('ORDERS_TEST', true) == true ) {

                // TESTING SUCCESS
                $order = FakeOrder::success();

                // TESTING FAIL
                // $order = FakeOrder::fail();
                
            }
            else {

                // BUY ORDER
                $broker = new Broker;
                $broker->setExchange($trade->exchange);
                $order = $broker->buyLimit($trade->pair, $trade->amount, $trade->price);
                
            }
           
            // Check for order success
            if ($order->success == true) {

                // Save exchange order id in the trade
                $this->trade->order_id = $order->result->uuid;
                $this->trade->save();

                // Create an Order in our database to track
                $orderToTrack = new Order;
                $orderToTrack->user_id = $trade->user_id;
                $orderToTrack->trade_id = $trade->id;
                $orderToTrack->exchange = $trade->exchange;
                $orderToTrack->order_id = $trade->order_id;
                $orderToTrack->type = 'open';
                $orderToTrack->save();

                // EVENT: OrderLaunched
                event(new OrderLaunched($orderToTrack));

                // Return success if create order succeed
                return ['status' => 'success', 'order_id' => $orderToTrack->order_id, 'stop_id' => $stopLoss->id, 'profit_id' => $takeProfit->id];

            }
            else {
                // // If order fails set trade status as 'Aborted'
                $trade->status = "Aborted";
                $trade->save();

                // Return error if create order fails 
                return ['status' => 'fail', 'message' => $order->message];

            }
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[TradeController] Exception: " . $e->message());

            // If exeption return error 500
            return response($e->message(), 500)->header('Content-Type', 'text/plain');

        } 

    }


    /**
     * Stores a new Conditional order in conditionals table
     * @param  trade $trade
     * @return array        
     */
    private function newConditional($trade) 
    {   
        try {

            // Create a condition to be watched and lauched when reached
            $conditional = new Conditional;
            $conditional->user_id = Auth::id();
            $conditional->trade_id = $trade->id;
            $conditional->exchange = $trade->exchange;
            $conditional->pair = $trade->pair;
            $conditional->condition = $trade->condition;
            $conditional->condition_price = $trade->condition_price;
            $conditional->cancel = false;

            if ($conditional->save()) {

                 // Trade status change
                $trade->status = "Waiting";
                
                // Save conditional id into the trade
                $trade->condition_id = $conditional->id;
                $trade->save();

                // EVENT: ConditionalLaunched
                event(new ConditionalLaunched($conditional));

                return ["status"=>"success", "conditional_id"=>$conditional->id];

            }
            else {

                // If conditional fails set trade status as 'Aborted'
                $trade->status = "Aborted";
                $trade->save();
                
                return ["status"=>"fail", "message"=>"Error creating conditional trade."];

            }
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[TradeController] Exception: " . $e->message());

            // If exeption return error 500
            return response($e->message(), 500)->header('Content-Type', 'text/plain');

        } 

    }

}

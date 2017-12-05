<?php

namespace App\Listeners;

use App\Events\ConditionReached;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\User;
use App\Conditional;
use App\Trade;
use App\Stop;
use App\Profit;
use App\Order;
use App\Library\Services\Facades\Bittrex;

class ExecuteConditional
{
    /**
     * Conditional order object
     * @var App\Conditional
     */
    protected $conditional;

    /**
     * Trade associated to the stop-loss
     * @var App\Trade
     */
    protected $trade;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ConditionReached  $event
     * @return void
     */
    public function handle(ConditionReached $event)
    {
        // Get conditional to retrieve associated trade
        $this->conditional = $event->conditional;

        // Get price reached (condition)
        $price = $event->price;

        // Get trade linked to the conditional
        $this->trade = Trade::find($event->conditional->trade_id);

        // Destroy Conditional linked to the trade
        Conditional::destroy($event->conditional->id);

        // Launch a new order to the exchange according to the trade iformation
        $order = $this->newOrder($this->trade->user_id, $this->trade->exchange, $this->trade->pair, $this->trade->price, $this->trade->amount, $this->trade->stop_loss, $this->trade->take_profit, $this->trade->position);

        if ($order['status'] == 'success') {

            // If order succeeds fill the order id, stop id and profit id in the trade
            // and set status as 'Opened'
            $this->trade->order_id = $order['order_id'];
            // $this->trade->stop_id = $order['stop_id'];
            // $this->trade->profit_id = $order['profit_id'];
            $this->trade->status = "Opened";
            $this->trade->save();

            Log::notice('New Trade: Trade #' . $this->trade->id . ' opened at ' . $this->trade->exchange . ' for ' . $this->trade->pair . ' at ' . $this->trade->price . ' an amount of ' . $this->trade->amount . ' units for a total of ' . $this->trade->total .' with Stop-Loss at ' . $this->trade->stop_loss . ' and Take-Profit at ' . $this->trade->take_profit);

            $res = '#' . $this->trade->id . ' Trade Opened.' . 'Exchange: ' . $this->trade->exchange . ' Pair: ' . $this->trade->pair . ' Price: ' . $this->trade->price . ' Amount: ' . $this->trade->amount . ' Total: ' . $this->trade->total .' Stop-Loss: ' . $this->trade->stop_loss . ' Take-Profit: ' . $this->trade->take_profit;
            
            return response($res , 200)->header('Content-Type', 'text/plain');
       
        } else if ($order['status'] == 'fail') {

            // If order fails set trade status as 'Aborted'
            $this->trade->status = "Aborted";
            $this->trade->save();

            // Log ERROR: The trade couldn't be launched
            Log::error('New Trade: Trade #' . $this->trade->id . ' aborted due to ' . $order['message']);

            return response($order['message'], 500)->header('Content-Type', 'text/plain');
        }
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
    private function newOrder($user_id, $exchange, $pair, $price, $amount, $stop, $profit, $position) 
    {

        $stopLoss = new Stop;
        $takeProfit = new Profit;

        // Get the current user
        $user = User::find($user_id);

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

                    // If we get a success response we create an Order in our database to track
                    $orderToTrack = new Order;
                    $orderToTrack->user_id = $this->trade->user_id;
                    $orderToTrack->trade_id = $this->trade->id;
                    $orderToTrack->exchange = 'bittrex';
                    $orderToTrack->order_id = $order->result->uuid;
                    $orderToTrack->type = 'open';
                    $orderToTrack->save();

                    return ['status' => 'success', 'order_id' => $orderToTrack->order_id, 'stop_id' => $stopLoss->id, 'profit_id' => $takeProfit->id];

                }
                else {

                    return ['status' => 'fail', 'message' => $order->message];

                }

                break;
        }
    }
}

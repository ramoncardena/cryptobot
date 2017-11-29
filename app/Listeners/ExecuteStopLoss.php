<?php
namespace App\Listeners;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\OrderLaunched;
use App\Events\StopLossReached;
use App\Library\Services\Facades\Bittrex;
use App\Trade;
use App\Stop;
use App\User;
use App\Order;

class ExecuteStopLoss
{

    protected $trade;

    protected $last;

    protected $stop;
    
    protected $order;

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
     * @param  StopLossReached  $event
     * @return void
     */
    public function handle(StopLossReached $event)
    {
        try {
            // Update stop-profit status
            $event->stopLoss->status = "Closing";
            $event->stopLoss->save();

            // Get the trade to close
            $this->trade = Trade::find($event->stopLoss->trade_id);
            $this->last = $event->last;

            // Update trade status
            $this->trade->status = "Closing";
            $this->trade->save();

            // Get the user linked to the trade
            $user = User::find($this->trade['user_id']);

            // Check exchange
            if ($this->trade['exchange'] == 'bittrex') {

                // Initialize Bittrex with user info
                Bittrex::setAPI($user->settings()->get('bittrex_key'), $user->settings()->get('bittrex_secret'));
                
                // Check for order type
                if ($event->stopLoss->type == "sell") {
                
                    // Launch Bittrex sell order with Pair, Amount and Price as parameters
                    // $order = Bittrex::sellLimit($this->trade['pair'], $this->trade['amount'], $this->last);
                    // 
                
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
                        $this->order = new Order;
                        $this->order->user_id = $this->trade['user_id'];
                        $this->order->trade_id = $this->trade['id'];
                        $this->order->exchange = 'bittrex';
                        $this->order->order_id = $order->result->uuid;
                        $this->order->save();
                    }
                    else {
                        // Error: On submiting order to Bittrex.
                    }
                }
                else if ($event->stopLoss->type == "buy") {
                    // TODO next ver.
                }
                
                // Event: OrderLaunched
                event(new OrderLaunched($this->order, $this->trade));

                var_dump("Pair:" . $this->trade->pair . " Amount: " . $this->trade->amount . " Price: " . $this->last);
            }
            
        } catch(\Exception $e) {
               var_dump( $e->getMessage());
        }
    }
}

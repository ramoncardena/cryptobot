<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Notifications\TakeProfitNotification;
use App\Events\OrderLaunched;
use App\Events\TakeProfitReached;
use App\Library\Services\Facades\Bittrex;
use App\Trade;
use App\Profit;
use App\User;
use App\Order;

class ExecuteTakeProfit
{
    /**
     * Trade associated to the stop-loss
     * @var App\Trade
     */
    protected $trade;

    /**
     * Last price for the active pair
     * @var float
     */
    protected $last;

    /**
     * Take-profit object
     * @var App\Profit
     */
    protected $profit;
    
    /**
     * Order launched by the take-profit
     * @var App\Order
     */
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
     * @param  TakeProfitReached  $event
     * @return void
     */
    public function handle(TakeProfitReached $event)
    {
        try {
            // Update take-profit status
            $event->takeProfit->status = "Closing";
            $event->takeProfit->save();

            // Get the trade to close
            $this->trade = Trade::find($event->takeProfit->trade_id);
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
                if ($event->takeProfit->type == "sell") {
                
                    // Launch Bittrex sell order with Pair, Amount and Price as parameters
                    // $order = Bittrex::sellLimit($this->trade['pair'], $this->trade['amount'], $this->last); 
                
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
                        $this->order->type = 'close';
                        $this->order->save();

                    }
                    else {

                        // Error: On submiting order to Bittrex.
                        // Log ERROR: Bittrex API returned error
                        Log::error("Bittrex API: " . $order->message);

                    }

                    // Event: OrderLaunched
                    event(new OrderLaunched($this->order, $this->trade));

                    // Log NOTICE: Order Launched
                    Log::notice("Order Launched: Take Profit launched a SELL order (#" . $this->order->id .") at " . $this->last  . " for trade #" . $this->trade['id'] . " for the pair " . $this->trade['pair'] . " at " . $this->trade['exchange']);

                    // NOTIFY: TakeProfit
                    User::find($this->trade['user_id'])->notify(new TakeProfitNotification($this->trade));

                }
                else if ($event->takeProfit->type == "buy") {
                    // TODO next ver.
                }
                
            }
            
        } catch(\Exception $e) {
              
            // Log CRITICAL: Exception
            Log::critical("ExecuteTakePRofit Exception: " . $e->getMessage());

        }

    }

}

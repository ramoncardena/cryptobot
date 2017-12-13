<?php

namespace App\Listeners;

use App\Events\OrderNotCompleted;
use App\Events\CloseOrderCompleted;
use App\Events\OpenOrderCompleted;
use App\Events\OrderLaunched;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Library\FakeOrder;
use App\Library\Services\Facades\Bittrex;
use App\Trade;
use App\User;
use App\Order;

class TrackOrder implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'orders';
    
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
     * @param  OrderLaunched  $event
     * @return void
     */
    public function handle(OrderLaunched $event)
    {
        try {

            // Get user
            $user = User::find($event->order->user_id);

            // Select Exchange
            switch ($event->order->exchange) {

                // BITTREX
                case "bittrex":

                    // Initialize Bittrex API
                    Bittrex::setAPI($user->settings()->get('bittrex_key'), $user->settings()->get('bittrex_secret'));

                    // Call to exchange API or a fakeOrder based on ENV->ORDERS_TEST
                    if (env('ORDERS_TEST', true) == true) {

                        if( rand() % 2 == 0) {

                            // TESTING SUCCESS
                            $onlineOrder = FakeOrder::success();    

                        }
                        else {

                            // TESTING FAIL
                            $onlineOrder = FakeOrder::notClosed();

                        }
                        
                    }
                    else {

                        // Get order from Bittrex
                        $onlineOrder = Bittrex::getOrder($event->order->order_id);
                        
                    }
                    

                    // Check for success on API call
                    if (! $onlineOrder->success) {

                        // Log ERROR: Bittrex API returned error
                        Log::error("[TrackOrder] Bittrex API: " . $onlineOrder->message);

                        // Add delay before requeueing
                        sleep(env('FAILED_ORDER_DELAY', 5));

                        // Event: OrderNotCompleted
                        event(new OrderNotCompleted($event->order));
                        
                    }
                    else {

                        // If order is closed then update trade
                        if ( $onlineOrder->result->Closed != "" ) {

                            switch ($event->order->type) {
                                case 'open':

                                    // EVENT: OpenOrderCompleted
                                    event(new OpenOrderCompleted($event->order, $onlineOrder->result->PricePerUnit));
                                    break;
                                
                                case 'close':

                                    // EVENT: CloseOrderCompleted
                                    event(new CloseOrderCompleted($event->order, $onlineOrder->result->PricePerUnit));
                                    break;
                            }
                            
                        }
                        else {

                            // If the order is not completed
                            
                            // Add delay before requeueing
                            sleep(env('ORDER_DELAY', 0));

                            // Event: OrderNotCompleted
                            event(new OrderNotCompleted($event->order));

                        }
                    } 
                    break;
            }

        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[TrackOrder] Exception: " . $e->getMessage());
            
        }
        
    }
}

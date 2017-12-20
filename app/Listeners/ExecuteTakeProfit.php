<?php
namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Events\OrderLaunched;
use App\Events\TakeProfitReached;

use App\Notifications\TakeProfitNotification;

use App\Library\Services\Broker;
use App\Library\FakeOrder;

use App\Trade;
use App\Profit;
use App\Stop;
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

            // Update take-profit status and mark for cancel
            $event->takeProfit->status = "Closing";
            $event->takeProfit->cancel = true;
            $event->takeProfit->save();

            // Get the trade to close
            $this->trade = Trade::find($event->takeProfit->trade_id);

            // Mark the associated stop-loss to cancel if exists
            if ($this->trade->stop_id != "-") {
                $stopLoss = Stop::find($this->trade->stop_id);
                $stopLoss->status = "Closing";
                $stopLoss->cancel = true;
                $stopLoss->save();
            }

            // Update last price for the pair
            $this->last = $event->last;

            // Update trade status
            $this->trade->status = "Closing";
            $this->trade->save();

            // Get the user linked to the trade
            $user = User::find($this->trade['user_id']);

            // Check for order type
            if ($event->takeProfit->type == "sell") {
            
                if ( env('ORDERS_TEST', true) == true ) {

                    // TESTING SUCCESS
                    $order = FakeOrder::success();

                    // TESTING FAIL
                    // $order = FakeOrder::fail();
                    
                }
                else {

                    // SELL ORDER
                    $broker = new Broker;
                    $broker->setExchange($this->trade['exchange']);
                    $broker->setUser($user);
                    $order = $broker->sellLimit($this->trade['pair'], $this->trade['amount'], $event->takeProfit->price);
                    
                }
                
                // Check for order success
                if ($order->success == true) {

                    // If we get a success response we create an Order in our database to track
                    $this->order = new Order;
                    $this->order->user_id = $this->trade['user_id'];
                    $this->order->trade_id = $this->trade['id'];
                    $this->order->exchange = 'bittrex';
                    $this->order->order_id = $order->result->uuid;
                    $this->order->type = 'close';
                    $this->order->cancel = false;
                    $this->order->save();

                }
                else {

                    // Log ERROR: Bittrex API returned error
                    Log::error("[ExecuteTakeProfit] Bittrex API: " . $order->message);

                }

                // Event: OrderLaunched
                event(new OrderLaunched($this->order));

                // Destroy Profit
                Profit::destroy($event->takeProfit->id);

                // Log INFO: Order Launched
                Log::info("Order Launched: Take Profit launched a SELL order (#" . $this->order->id .") at " . $event->takeProfit->price  . " for trade #" . $this->trade['id'] . " for the pair " . $this->trade['pair'] . " at " . $this->trade['exchange']);

                // NOTIFY: TakeProfit
                User::find($this->trade['user_id'])->notify(new TakeProfitNotification($this->trade));

            }
            else if ($event->takeProfit->type == "buy") {

                // TODO next ver.
                
            }
            
        } catch(Exception $e) {
              
            // Log CRITICAL: Exception
            Log::critical("[ExecuteTakeProfit] Exception: " . $e->getMessage());

        }

    }

}

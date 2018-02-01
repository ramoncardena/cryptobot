<?php
namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Events\OrderLaunched;
use App\Events\StopLossReached;

use App\Notifications\StopLossNotification;

use App\Library\Services\Broker;
use App\Library\FakeOrder;

use App\Trade;
use App\Stop;
use App\Profit;
use App\User;
use App\Order;

class ExecuteStopLoss
{
    /**
     * Trade associated to the stop-loss
     * @var Trade
     */
    protected $trade;

    /**
     * Last price for the active pair
     * @var float
     */
    protected $last;

    /**
     * Stop-loss object
     * @var Stop
     */
    protected $stop;
    
    /**
     * Order launched by the stop-loss
     * @var Order
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
     * @param  StopLossReached  $event
     * @return void
     */
    public function handle(StopLossReached $event)
    {
        
        try {

            // Update stop-profit status and mark for cancel
            $event->stopLoss->status = "Closing";
            $event->stopLoss->cancel = true;
            $event->stopLoss->save();

            // Get the trade to close
            $this->trade = Trade::find($event->stopLoss->trade_id);

            // Mark the associated take-profit to cancel if exists
            if ($this->trade->profit_id != "-") {
                $takeProfit = Profit::find($this->trade->profit_id);
                $takeProfit->status = "Closing";
                $takeProfit->cancel = true;
                $takeProfit->save();
            }

            $this->last = $event->last;

            // Update trade status
            $this->trade->status = "Closing";
            $this->trade->save();

            // Get the user linked to the trade
            $user = User::find($this->trade['user_id']);

            // Check for order type
            if ($event->stopLoss->type == "sell") {
            
                if (env('ORDERS_TEST', true) == true) {

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
                    $order = $broker->sellLimit2($this->trade['pair'], $this->trade['amount'], $event->stopLoss->price);
                    
                }
                
                // Check for order success
                if ($order->success == true) {

                    // If we get a success response we create an Order in our database to track
                    $this->order = new Order;
                    $this->order->user_id = $this->trade['user_id'];
                    $this->order->trade_id = $this->trade['id'];
                    $this->order->exchange = $this->trade['exchange'];
                    $this->order->order_id = $order->result->uuid;
                    $this->order->type = 'close';
                    $this->order->cancel = false;
                    $this->order->save();

                }
                else {

                    // Log ERROR: Broker returned error
                    Log::error("[User " . $user->id . "] ExecuteStopLoss Broker: " . $order->message);

                    // TODO Launch event with error
                }

                // Event: OrderLaunched
                event(new OrderLaunched($this->order));

                // Destroy Stop
                Stop::destroy($event->stopLoss->id);

                // Log INFO: Order Launched
                Log::info("Order Launched: Stop-loss launched a SELL order (#" . $this->order->id .") at " . $event->stopLoss->price  . " for trade #" . $this->trade['id'] . " for the pair " . $this->trade['pair'] . " at " . $this->trade['exchange']);

                // NOTIFY: StopLoss
                User::find($this->trade['user_id'])->notify(new StopLossNotification($this->trade));

            }
            else if ($event->stopLoss->type == "buy") {

                // TODO next ver.
                
            }
            
        } catch(\Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[User " . $user->id . "] ExecuteStopLoss Exception: " . $e->getMessage());
            
            // TODO Launch event with error

        }

    }

}

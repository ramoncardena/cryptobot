<?php

namespace App\Listeners;

use App\Events\StopLossLaunched;
use App\Events\TakeProfitLaunched;
use App\Events\OpenOrderCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Notifications\TradeOpenedNotification;
use App\Stop;
use App\Profit;
use App\Trade;
use App\User;
use App\Order;

class OpenTrade implements ShouldQueue
{

     /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'trades';


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  OpenOrderCompleted  $event
     * @return void
     */
    public function handle(OpenOrderCompleted $event)
    {
        try {

            // Get trade linked to the order
            $trade = Trade::find($event->order->trade_id);

            // Update price with the final price from the order
            if (env('ORDERS_TEST', true) == false) {
                $trade->price = $event->price;
            }

            if ($trade->stop_loss > 0) {

                // Create a new Stop-Loss instance in the DB
                $stopLoss = new Stop;

                $stopLoss->trade_id = $trade->id;
                $stopLoss->order_id = $trade->order_id;
                $stopLoss->status = "Opened";
                $stopLoss->exchange = $trade->exchange;
                $stopLoss->pair = $trade->pair;
                $stopLoss->price = $trade->stop_loss;
                $stopLoss->amount = $trade->amount;
                $stopLoss->cancel = false;

                if ($trade->position = 'long') {

                    $stopLoss->type = 'sell';

                }
                else if ($trade->position = 'short') {

                    $stopLoss->type = 'buy';

                }

                $stopLoss->save();
                $trade->stop_id = $stopLoss->id;

                // Log INFO: Stop-Loss launched
                Log::info("Stop-Loss #" . $stopLoss->id . " launched by Trade #" . $trade->id);

                // EVENT: StopLossLaunched
                event(new StopLossLaunched($stopLoss));

            }

            if ($trade->take_profit > 0) {

                // Creat a new Take-Profit instance in the DB
                $takeProfit = new Profit;

                $takeProfit->trade_id = $trade->id;
                $takeProfit->order_id = $trade->order_id;
                $takeProfit->status = "Opened";
                $takeProfit->exchange = $trade->exchange;
                $takeProfit->pair = $trade->pair;
                $takeProfit->price = $trade->take_profit;
                $takeProfit->amount = $trade->amount;
                $takeProfit->cancel = false;

                if ($trade->position = 'long') {

                    $takeProfit->type = 'sell';

                }
                else if ($trade->position = 'short') {

                   $takeProfit->type = 'buy';

                }

                $takeProfit->save();
                $trade->profit_id = $takeProfit->id;

                // Log INFO: Take-Profit launched
                Log::info("Take-Profit #" . $takeProfit->id . " launched by Trade #" . $trade->id);
               
                // EVENT: TakeProfitLaunched
                event(new TakeProfitLaunched($takeProfit));
            }

            // Destroy order to stop tracking
            Order::destroy($event->order->id);

            // Set the trade as Opened and save it
            $trade->status = "Opened";
            $trade->save();

             // NOTIFY: TradeOpened
            User::find($trade->user_id)->notify(new TradeOpenedNotification($trade));

            // Log NOTICE: Trade opened
            Log::notice("Trade #" . $trade->id . ": Opened.  Exchange: " . $trade->exchange . " Pair: " . $trade->pair . " Price per unit: " . $trade->price);

           
       } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[OpenTrade] Exception: " . $e->getMessage());
       }
    }
}

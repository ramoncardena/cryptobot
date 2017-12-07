<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Notifications\TradeClosedNotification;
use App\Notifications\TradeOpenedNotification;
use App\Events\TradeClosed;
use App\Trade;
use App\Stop;
use App\Profit;
use App\User;
use App\Order;

class UpdateTrade
{
    /**
     * Trade associated to the order
     * @var Trade
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
     * @param  OrderCompleted  $event
     * @return void
     */
    public function handle(OrderCompleted $event)
    {
        try {
            // Get trade linked to the order
            $this->trade = Trade::find($event->order->trade_id);

            switch ($event->type) {
                case 'close':
                    // Close the trade updating final price
                    $this->closeTrade($event->order->trade_id, $event->order->id, $event->price);

                    // Log NOTICE: Order closed at Bittrex 
                    Log::notice("Order Completed: Order " . $event->order->order_id . " at " . $event->order->exchange  . " for " . $this->trade->pair . " was CLOSED with closing price " . $event->price);

                    // Log NOTICE: Trade close
                    Log::notice("Trade #" . $this->trade->id . " at " . $this->trade->exchange . " for " . $this->trade->pair . " was closed at " . $this->trade->closing_price ." with a profit of " . $this->trade->profit . "%");

                    // NOTIFY: TradeClosed
                    User::find($this->trade->user_id)->notify(new TradeClosedNotification($this->trade));

                    break;
                
                case 'open':
                    // Update the trade with actual rate and status opened
                    $this->updateTrade($event->order->trade_id, $event->order->id, $event->price);

                     // Log NOTICE: Order opened at Bittrex 
                    Log::notice("Order Completed: Order " . $event->order->order_id . " at " . $event->order->exchange . $event->order->exchange  . " for " . $this->trade->pair . " was OPENED with opening price " . $event->price);

                    // Log NOTICE: Trade opened
                    Log::notice("Trade #" . $this->trade->id . " at " . $this->trade->exchange . " for " . $this->trade->pair . " was opened at an actual price of " . $event->price . " per unit");

                    // NOTIFY: TradeOpened
                    User::find($this->trade->user_id)->notify(new TradeOpenedNotification($this->trade));

                    break;
            }

        } catch(\Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("UpdateTrade Exception: " . $e->getMessage());

        }

    }

    private function updateTrade($tradeId, $OrderId, $price) {
        try {
            
            // Get trade linked to the order
            $this->trade = Trade::find($tradeId);

            if ($this->trade->stop_loss > 0) {

                // Create a new Stop-Loss instance in the DB
                $stopLoss = new Stop;

                $stopLoss->trade_id = $this->trade->id;
                $stopLoss->order_id = $this->trade->order_id;
                $stopLoss->status = "Opened";
                $stopLoss->exchange = $this->trade->exchange;
                $stopLoss->pair = $this->trade->pair;
                $stopLoss->price = $this->trade->stop_loss;
                $stopLoss->amount = $this->trade->amount;

                if ($this->trade->position = 'long') {

                    $stopLoss->type = 'sell';

                }
                else if ($this->trade->position = 'short') {

                    $stopLoss->type = 'buy';

                }

                $stopLoss->save();
                $this->trade->stop_id = $stopLoss->id;

            }

            if ($this->trade->take_profit > 0) {

                // Creat a new Take-Profit instance in the DB
                $takeProfit = new Profit;

                $takeProfit->trade_id = $this->trade->id;
                $takeProfit->order_id = $this->trade->order_id;
                $takeProfit->status = "Opened";
                $takeProfit->exchange = $this->trade->exchange;
                $takeProfit->pair = $this->trade->pair;
                $takeProfit->price = $this->trade->take_profit;
                $takeProfit->amount = $this->trade->amount;

                if ($this->trade->position = 'long') {

                    $takeProfit->type = 'sell';

                }
                else if ($this->trade->position = 'short') {

                   $takeProfit->type = 'buy';

                }

                $takeProfit->save();
                $this->trade->profit_id = $takeProfit->id;

            }

            // Destroy order to stop tracking
            Order::destroy($OrderId);

            // Set the trade as Opened and save it
            $this->trade->status = "Opened";
            $this->trade->save();

            return true;

        } catch(\Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("UpdateTrade Exception: " . $e->getMessage());

            return $e->getMessage();
        }
    }

    private function closeTrade($tradeId, $OrderId, $price) {
        try {

            // Get trade linked to the order
            $this->trade = Trade::find($tradeId);

            // Destroy stop-loss and take-profit linked to the trade
            if ( $this->trade->stop_id != '-' ) Stop::destroy($this->trade->stop_id);
            
            if ( $this->trade->profit_id != '-' ) Profit::destroy($this->trade->profit_id);

            // Destroy order to stop tracking
            Order::destroy($OrderId);

            // Get actual final price of the order to calculate profit
            $this->trade->closing_price = $price;

            // Calculate profit
            $decreaseValue = $price - $this->trade->price;
            $this->trade->profit = ($decreaseValue / $this->trade->price) * 100;  // ADD: Fee calculation 

            $this->trade->status = "Closed";
            $this->trade->save();

            return true;

        } catch(\Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("UpdateTrade Exception: " . $e->getMessage());

            return $e->getMessage();
        }
    }
}

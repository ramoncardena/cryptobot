<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Events\TradeClosed;
use App\Trade;
use App\Stop;
use App\Profit;
use App\User;
use App\Order;

class CloseTrade
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

            // Destroy stop-loss and take-profit linked to the trade
            Stop::destroy($this->trade->stop_id);
            Profit::destroy($this->trade->profit_id);

            // Destroy order to stop tracking
            Order::destroy($event->order->id);

            // Get actual final price of the order to calculate profit
            $price = $event->price;
            $this->trade->closing_price = $price;

            // Calculate profit
            $decreaseValue = $price - $this->trade->price;
            $this->trade->profit = ($decreaseValue / $this->trade->price) * 100;
    
            $this->trade->status = "Closed";
            $this->trade->save();

            // Event: OrderLaunched
            event(new TradeClosed($this->trade));

            // Log NOTICE: Trade close
            Log::notice("Trade #" . $this->trade->id . " at " . $this->trade->exchange . " for " . $this->trade->pair . " was closed at " . $this->trade->closing_price ." with a profit of " . $this->trade->profit . "%");

        } catch(\Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("BittrexTradeWatcher Exception: " . $e->getMessage());

        }


    }
}

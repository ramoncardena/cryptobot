<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\TradeClosed;
use App\Trade;
use App\Stop;
use App\Profit;
use App\User;
use App\Order;

class CloseTrade
{
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
            $decreaseValue = $price - $this->trade->price;
            $this->trade->profit = ($decreaseValue / $this->trade->price) * 100;
                    
            $profit = decreaseValue.toFixed(2) + "%";

            $this->trade->status = "Closed";
            $this->trade->save();

            // Event: OrderLaunched
            event(new TradeClosed($this->trade));

        } catch(\Exception $e) {
               var_dump( $e->getMessage());
        }


    }
}

<?php

namespace App\Listeners;

use App\Events\CloseOrderCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Notifications\TradeClosedNotification;
use App\Stop;
use App\Profit;
use App\Trade;
use App\User;
use App\Order;
use App\Exchange;

class CloseTrade implements ShouldQueue
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
        //
    }

    /**
     * Handle the event.
     *
     * @param  CloseOrderCompleted  $event
     * @return void
     */
    public function handle(CloseOrderCompleted $event)
    {
        try {

            // Get trade linked to the order
            $trade = Trade::find($event->order->trade_id);

            $exchange =Exchange::where('name', $trade->exchange)->first();

            // Destroy order to stop tracking
            Order::destroy($event->order->id);

            // Get actual final price of the order to calculate profit
            $trade->closing_price = $event->price;

            // Calculate fee
            $fee = floatval($trade->total) * floatval($exchange->fee) / 100;

            // Calculate profit
            $decreaseValue = ( floatval($event->price) - floatval($fee) ) - floatval($trade->price);
            $trade->profit = ( floatval($decreaseValue) / floatval($trade->price) ) * 100;

            $trade->status = "Closed";
            $trade->save();

            // Log NOTICE: Trade close
            Log::notice("Trade #" . $trade->id . ": Closed. Exchange: " . $trade->exchange . " Pair: " . $trade->pair . " Final Price: " . $trade->closing_price ." Profit: " . $trade->profit . "%");

            // NOTIFY: TradeClosed
            User::find($trade->user_id)->notify(new TradeClosedNotification($trade));
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[CloseTrade] Exception: " . $e->getMessage());

        }
    }
}

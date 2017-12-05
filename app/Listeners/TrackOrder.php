<?php

namespace App\Listeners;

use App\Events\OrderLaunched;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Trade;

class TrackOrder
{
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

            Log::info("Tracking order: " . $event->order['order_id'] . " at " . $event->trade['exchange']);

        } catch (\Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("TrackOrder Exception: " . $e->getMessage());
            
        }
        
    }
}

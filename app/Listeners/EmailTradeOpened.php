<?php

namespace App\Listeners;

use App\Events\TradeOpened;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Trade;
use App\User;

class EmailTradeOpened
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
     * @param  TradeCancelled  $event
     * @return void
     */
    public function handle(TradeOpened $event)
    {

        try {

            // Mark event as cancelled
            $trade = Trade::find($event->trade->id);
           

            // NOTIFY: Trade Cancelled
            // User::find($trade->user_id)->notify(new TradeCancelledNotification($trade));

            // Log INFO: Event cancelled
             Log::info("Trade Opened EVENT!!");
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[EmailTradeOpened] Exception: " . $e->getMessage());

        }

    }
    
}

<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Events\TradeClosed;

use App\Trade;
use App\User;

class EmailTradeClosed
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
    public function handle(TradeClosed $event)
    {

        try {

            // Mark event as cancelled
            $trade = Trade::find($event->trade->id);
            

            // NOTIFY: Trade Cancelled
            //User::find($trade->user_id)->notify(new TradeCancelledNotification($trade));

            // Log INFO: Event cancelled
            // Log::info("Trade #" . $event->trade_id . ": Cancelled.");
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[EmailTradeClosed] Exception: " . $e->getMessage());

        }

    }
    
}

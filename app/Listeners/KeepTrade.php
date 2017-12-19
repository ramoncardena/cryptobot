<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Notifications\TradeKeptNotification;

use App\Events\TradeKept;

use App\Trade;
use App\User;

class KeepTrade implements ShouldQueue
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
     * @param  TradeKept  $event
     * @return void
     */
    public function handle(TradeKept $event)
    {
        try {

            // Mark event as cancelled
            $trade = Trade::find($event->trade->id);

            $trade->status = "Kept";
            $trade->save();
            
            // NOTIFY: Trade Cancelled
            User::find($trade->user_id)->notify(new TradeKeptNotification($trade));

            // Log INFO: Event cancelled
            Log::info("Trade #" . $event->trade->id . ": Kept.");
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[EmailTradeClosed] Exception: " . $e->getMessage());

        }
    }
}

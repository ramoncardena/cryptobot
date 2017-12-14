<?php

namespace App\Listeners;

use App\Events\StopLossLaunched;
use App\Events\StopLossReached;
use App\Events\StopLossNotReached;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Facades\Bittrex;
use App\Trade;
use App\User;
use App\Stop;

class TrackStopLoss implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'stops';

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
     * @param  StopLossLaunched  $event
     * @return void
     */
    public function handle(StopLossLaunched $event)
    {

        try {
            
            //Log::info("Tracking Stop-Loss #" . $event->stopLoss->id);

            // Get the current take-profit order to check if cancel was launched
            $stop = Stop::find($event->stopLoss->id);

            // If take-profit is marked to cancel proceed to delete
            if ($stop->cancel) {

                // Delete Take-Profit from the database
                Stop::destroy($stop->id);

                // Log INFO: Take-Profit deleted 
                Log::info("Stop-Loss #" . $event->stopLoss->id . " deleted.");

            }
            else {

                // Select exchange
                switch ($stop->exchange) {
                    // BITTREX
                    case "bittrex":

                        // Call to Bittrex API to get market ticker (last price)
                        $ticker = Bittrex::getTicker($stop->pair);

                        // Check for success on API call
                        if (! $ticker->success) {

                            // Log ERROR: Bittrex API returned error
                            Log::error("[TrackStopLoss] Bittrex API: " . $ticker->message);

                        }
                        else {

                            $ticker= $ticker->result;

                            if ( floatval($ticker->Last) <= floatval($stop->price)) {

                                // EVENT: StopLossReached
                                event(new StopLossReached($stop, $ticker->Last));

                                // Log NOTICE: Take-Profit reached
                                Log::notice("Stop-Loss: Trade #" . $stop->trade_id . " reached its stop-loss at " . $stop->price . " for the pair " . $stop->pair . " at " . $stop->exchange . " (last price: " . $ticker->Last . ")");

                            }
                            else {
                                
                                // Add delay before requeueing
                                sleep(env('STOPLOSS_DELAY', 5));

                                // EVENT: StopLossNotReached
                                event(new StopLossNotReached($stop));
                            }

                        }

                    break;
                }

            }

        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[TrackStopLoss] Exception: " . $e->getMessage());
            
        }
    }
}

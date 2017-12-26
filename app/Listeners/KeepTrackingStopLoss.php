<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Events\StopLossReached;
use App\Events\StopLossNotReached;

use App\Library\Services\Broker;

use App\Trade;
use App\User;
use App\Stop;

class KeepTrackingStopLoss implements ShouldQueue
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
     * @param  StopLossNotReached  $event
     * @return void
     */
    public function handle(StopLossNotReached $event)
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

                        // TICKER
                        $broker = new Broker;
                        $broker->setExchange($stop->exchange);
                        $ticker = $broker->getTicker($stop->pair);

                        // Check for success on API call
                        if (! $ticker->success) {

                            // Log ERROR: Bittrex API returned error
                            Log::error("[KeepTrackingStopLoss] Bittrex API: " . $ticker->message);

                            // Add delay before requeueing
                            sleep(env('FAILED_STOPLOSS_DELAY', 5));

                            // EVENT: StopLossNotReached
                            event(new StopLossNotReached($stop));

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
            Log::critical("[KeepTrackingStopLoss] Exception: " . $e->getMessage());

            // Add delay before requeueing
            sleep(env('FAILED_STOPLOSS_DELAY', 5));

            // EVENT: StopLossNotReached
            event(new StopLossNotReached($stop));
            
        }
    }
}

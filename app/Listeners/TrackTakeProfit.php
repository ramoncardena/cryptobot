<?php
namespace App\Listeners;

use App\Events\TakeProfitLaunched;
use App\Events\TakeProfitReached;
use App\Events\TakeProfitNotReached;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Facades\Bittrex;
use App\Trade;
use App\User;
use App\Profit;

class TrackTakeProfit implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'profits';

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
     * @param  TakeProfitLaunched  $event
     * @return void
     */
    public function handle(TakeProfitLaunched $event)
    {
        try {
            
            // Log::info("Tracking Take-Profit #" . $event->takeProfit->id);

            // Get the current take-profit order to check if cancel was launched
            $profit = Profit::find($event->takeProfit->id);

            // If take-profit is marked to cancel proceed to delete
            if ($profit->cancel) {

                // Delete Take-Profit from the database
                Profit::destroy($profit->id);

                // LOG Info: Take-Profit deleted 
                Log::info("Take-Profit #" . $event->takeProfit->id . " deleted.");

            }
            else {

                // Select exchange
                switch ($profit->exchange) {
                    // BITTREX
                    case "bittrex":

                        // Call to Bittrex API to get market ticker (last price)
                        $ticker = Bittrex::getTicker($profit->pair);

                        // Check for success on API call
                        if (! $ticker->success) {

                            // Log ERROR: Bittrex API returned error
                            Log::error("[TrackTakeProfit] Bittrex API: " . $ticker->message);

                        }
                        else {

                            $ticker= $ticker->result;

                            if ( floatval($ticker->Last) >= floatval($profit->price)) {

                                // EVENT: TakeProfitReached
                                event(new TakeProfitReached($profit, $ticker->Last));

                                // Log NOTICE: Take-Profit reached
                                Log::notice("Take-Profit: Trade #" . $profit->trade_id . " reached its take-profit at " . $profit->price . " for the pair " . $profit->pair . " at " . $profit->exchange . " (last price: " . $ticker->Last . ")");

                            }
                            else {
                                
                                // Add delay before requeueing
                                sleep(env('TAKEPROFIT_DELAY', 5));

                                // EVENT: TakeProfitNotReached
                                event(new TakeProfitNotReached($profit));
                            }

                        }

                    break;
                }

            }
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("TrackTakeProfit Exception: " . $e->getMessage());
            
        }
    }
}

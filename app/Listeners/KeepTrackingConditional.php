<?php

namespace App\Listeners;

use App\Events\ConditionNotReached;
use App\Events\ConditionReached;
use App\Events\TradeCancelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Facades\Bittrex;
use App\User;
use App\Conditional;

class KeepTrackingConditional implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'conditionals';


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
     * @param  ConditionNotReached  $event
     * @return void
     */
    public function handle(ConditionNotReached $event)
    {
        try {

            // Log::info("Tracking conditional: #" . $event->conditional->id);

            // Get user
            $user = User::find($event->conditional->user_id);

            // Get the current conditional order to check if cancel was launched
            $conditional = Conditional::find($event->conditional->id);

            // If order is marked to cancel proceed to cancel
            if ($conditional->cancel) {

                // EVENT: TradeCancelled
                event(new TradeCancelled($event->conditional->trade_id));

                // Delete conditional from the database
                Conditional::destroy($event->conditional->id);

                // Log NOTICE: Conditional cancelled
                Log::notice("Conditional order #" . $event->conditional->id . " cancelled.");

            }
            else {
                // Select Exchange
                switch ($conditional->exchange) {
                    // BITTREX
                    case "bittrex":

                        // Call to Bittrex API to get market ticker (last price)
                        $ticker = Bittrex::getTicker($conditional->pair);

                        // Check for success on API call
                        if (! $ticker->success) {

                            // Log ERROR: Bittrex API returned error
                            Log::error("[KeepTrackingConditional] Bittrex API: " . $ticker->message);

                        }
                        else {

                            $ticker= $ticker->result;

                            // Check the condition type: greater or less
                            switch ($conditional->condition) {
                                case 'greater':

                                    if ( floatval($ticker->Last) >= floatval($conditional->condition_price) ) {

                                        // EVENT ConditionReached
                                        event(new ConditionReached($conditional, $ticker->Last));

                                        // Log NOTICE: Condition reached in a conditional order
                                        Log::notice("Conditional Order: Trade #" . $conditional->trade_id . " reached its condition, current price (" . $ticker->Last . ") for " . $conditional->pair . " at " . $conditional->exchange .  " is " . $conditional->condition . " than " . $conditional->condition_price);
                                        
                                    }
                                    else {

                                        // Add delay before requeueing
                                        sleep(env('CONDITIONAL_DELAY', 5));

                                        // EVENT: ConditionNotReached
                                        event(new ConditionNotReached($conditional));

                                    }

                                    break;

                                case 'less':

                                    if ( floatval($ticker->Last) <= floatval($conditional->condition_price) ) {
                                        
                                        // EVENT ConditionReached
                                        event(new ConditionReached($conditional, $ticker->Last));

                                        // Log NOTICE: Condition reached in a conditional order
                                        Log::notice("Conditional Order: Trade #" . $conditional->trade_id . " reached its condition, current price (" . $ticker->Last . ") for " . $conditional->pair . " at " . $conditional->exchange .  " is " . $conditional->condition . " than " . $conditional->condition_price);
                                        
                                    }
                                    else {

                                        // Add delay before requeueing
                                        sleep(env('CONDITIONAL_DELAY', 5));

                                        // EVENT: ConditionNotReached
                                        event(new ConditionNotReached($conditional));

                                    }

                                    break;

                            }

                        }

                    break;

                }
            }
            
        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[KeepTrackingConditional] Exception: " . $e->getMessage());

        }
    }
}

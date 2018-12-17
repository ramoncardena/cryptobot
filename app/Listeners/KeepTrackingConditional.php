<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Events\ConditionNotReached;
use App\Events\ConditionReached;
use App\Events\TradeCancelled;

use App\Library\Services\Broker;
use App\User;
use App\Conditional;
use App\Trade;

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
                $trade = Trade::find($event->conditional->trade_id);
                event(new TradeCancelled($trade));

                // Delete conditional from the database
                Conditional::destroy($event->conditional->id);

                // Log NOTICE: Conditional cancelled
                Log::notice("Conditional order #" . $event->conditional->id . " cancelled.");

            }
            else {
                // TICKER
                $broker = new Broker;
                $broker->setExchange($conditional->exchange);
                $broker->setUser($user);
                $ticker = $broker->getTicker2($conditional->pair);

                // Check for success on call
                if (! $ticker->success) {

                    // Log ERROR: Broker returned error
                    Log::error("[KeepTrackingConditional] Broker: " . $ticker->message);

                    // Add delay before requeueing
                    sleep(env('FAILED_CONDITIONAL_DELAY', 5));

                    // EVENT: ConditionNotReached
                    event(new ConditionNotReached($conditional));

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
            }
            
        } catch (\Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[KeepTrackingConditional] Exception: " . $e->getMessage());

            // Add delay before requeueing
            sleep(env('FAILED_CONDITIONAL_DELAY', 5));

            // EVENT: ConditionNotReached
            event(new ConditionNotReached($conditional));

        }
    }
}

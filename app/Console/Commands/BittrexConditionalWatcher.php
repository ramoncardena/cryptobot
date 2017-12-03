<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Conditional;
use App\Events\ConditionReached;
use App\Library\Services\Facades\Bittrex;
use Illuminate\Support\Facades\Log;

class BittrexConditionalWatcher extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cryptobot:bittrex_conditional_watcher';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Watch conditional orders at Bittrex.';
	
	/**
     * @var currency pairs
     */
    protected $instruments;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {

		// Log INFO: BitttrexTradeWatcher launched
		Log::info("Bittrex Conditional Watcher launched.");

		while(1) {

			if(ord(fgetc(STDIN)) == 113) {

				// Log INFO: BitttrexTradeWatcher stopped
				Log::info("Bittrex Conditional Watcher halted by user (CTRL+C).");
				return null;

			}

			try {

				// Get conditional orders to check if condition is reached
				$conditionals = Conditional::where('exchange', 'bittrex')
						->get();

				// Log INFO: BitttrexTradeWatcher launched
				Log::info("Bittrex Conditional Watcher: " . $conditionals);

				// Get pairs
				$pairs = $conditionals->pluck('pair');

				// Set instruments with pairs form stops and profits
				$this->instruments = array_unique($pairs->all());

				// Iterate through all pairs to check last price
				foreach ($this->instruments as $market) {

					// Call to Bittrex API to get market ticker (last price)
					$ticker = Bittrex::getTicker($market);

					// Check for success on API call
					if (! $ticker->success) {

						// Log ERROR: Bittrex API returned error
						Log::error("Bittrex API: " . $ticker->message);

					}
					else {
						
						$ticker= $ticker->result;

						Log::info("Conditional Last: " . $ticker->Last);

						// Check last price with stop-loss for this pair
						$conditionalsToCheck = $conditionals->whereIn('pair', $market);

						foreach ($conditionalsToCheck as $conditional) {

							var_dump($conditional->pair . "/n");

							// Check the condition type: greater or less
							switch ($conditional->condition) {
								case 'greater':

									if ( floatval($ticker->Last) >= floatval($conditional->condition_price) ) {

										// Event ConditionReached
										event(new ConditionReached($conditional, $ticker->Last));

										// Log NOTICE: Condition reached in a conditional order
							    		Log::notice("Conditional Order: Trade #" . $conditional->trade_id . " reached its condition, current price (" . $ticker->Last . ") for " . $conditional->pair . " at " . $conditional->exchange .  " is " . $conditional->condition . " than " . $conditional->condition_price);
										
									}

									break;

								case 'less':

									if ( floatval($ticker->Last) <= floatval($conditional->condition_price) ) {
										
										// Event ConditionReached
										event(new ConditionReached($conditional, $ticker->Last));

										// Log NOTICE: Condition reached in a conditional order
							    		Log::notice("Conditional Order: Trade #" . $conditional->trade_id . " reached its condition, current price (" . $ticker->Last . ") for " . $conditional->pair . " at " . $conditional->exchange .  " is " . $conditional->condition . " than " . $conditional->condition_price);
										
									}

									break;

							}
							print_r(".");
						}

					}

				}

			} catch(\Exception $e) {

				// Log CRITICAL: Exception
				Log::critical("BittrexConditionalWatcher Exception: " . $e->getMessage());

				sleep(1);
				continue;

			}

			sleep(10);

		}

	}

}

<?php

namespace App\Console\Commands;

use App\Library\Console;
use Illuminate\Console\Command;
use Exception;

use App\User;
use App\Conditional;
use App\Events\ConditionReached;
use App\Library\Services\Facades\Bittrex;

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
		$this->console = $util = new Console();

		stream_set_blocking(STDIN, 0);
		while(1) {
			if(ord(fgetc(STDIN)) == 113) {
				echo "QUIT detected...";
				return null;
			}

			try {
				// Get conditional orders to check if condition is reached
				$conditionals = Conditional::where('exchange', 'bittrex')
						->get();

				// Get pairs
				$pairs = $conditionals->pluck('pair');

				// Set instruments with pairs form stops and profits
				$this->instruments = array_unique($pairs->all());

				// Iterate through all pairs to check last price
				foreach ($this->instruments as $market) {

					// Call to Bittrex API to get market ticker (last price)
					$ticker = Bittrex::getTicker($market)->result;

					// Check last price with stop-loss for this pair
					$conditionalsToCheck = $conditionals->whereIn('pair', $market);

					foreach ($conditionalsToCheck as $conditional) {

						// Check the condition type: greater or less
						switch ($conditional->condition) {
							case 'greater':
								if ( floatval($conditional->condition_price) >= floatval($ticker->Last) ) {
									event(new ConditionReached($conditional, $ticker->Last));
									//print_r("Trade #" . $stop->trade_id . " -> Stop-Loss at " . $ticker->Last ."\n");
								}
								break;
							case 'less':
								if ( floatval($conditional->condition_price) <= floatval($ticker->Last) ) {
									event(new ConditionReached($conditional, $ticker->Last));
									//print_r("Trade #" . $stop->trade_id . " -> Stop-Loss at " . $ticker->Last ."\n");
								}
								break;
						}
					}
				}

				print_r(".");

			} catch(\Exception $e) {
				var_dump( $e->getMessage());
				sleep(1);
				continue;
			}
			sleep(5);
		}
	}
}

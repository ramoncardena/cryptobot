<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Stop;
use App\Profit;
use App\Events\StopLossReached;
use App\Events\TakeProfitReached;
use App\Library\Services\Facades\Bittrex;
use Illuminate\Support\Facades\Log;

class BittrexTradeWatcher extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cryptobot:bittrex_trade_watcher';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Watch trades at Bittrex.';

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

		$this->instruments = [];

		// Log INFO: BitttrexTradeWatcher launched
		Log::info("Bittrex Trade Watcher launched.");

		// Initialize Bittrez API
		Bittrex::setAPI('','');

		stream_set_blocking(STDIN, 0);

		while(1) {
			if(ord(fgetc(STDIN)) == 113) {

				// Log INFO: BitttrexTradeWatcher stopped
				Log::info("Bittrex Trade Watcher halted by user (CTRL+C).");
				return null;

			}

			try {
				// Get pairs to watch for stop-loss
				$stops = Stop::where('exchange', 'bittrex')
						->where('status', 'Opened')
						->get();
				$stopPairs = $stops->pluck('pair');

				// Get pairs to watch for stop-loss
				$profits = Profit::where('exchange', 'bittrex')
						->where('status', 'Opened')
						->get();
				$profitPairs =  $profits->pluck('pair');

				// Merge stops and profits arrays into pairs array
				$pairs = $profitPairs->merge($stopPairs);

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

						// Check last price with stop-loss for this pair
						$stopsToCheck = $stops->whereIn('pair', $market);

						foreach ($stopsToCheck as $stop) {

							if (floatval($ticker->Last) <= floatval($stop->price)) {

								event(new StopLossReached($stop, $ticker->Last));

								// Log NOTICE: Stop-Loss reached
							    Log::notice("Stop-Loss: Trade #" . $stop->trade_id . " reached its stop-loss at " . $stop->price . " for the pair " . $stop->pair . " at " . $stop->exchange);

							}

						}

						// Check last price with take-profit for this pair
						$profitsToCheck = $profits->whereIn('pair', $market);

						foreach ($profitsToCheck as $profit) {
							if ( floatval($ticker->Last) >= floatval($profit->price)) {

								event(new TakeProfitReached($profit, $ticker->Last));

								// Log NOTICE: Take-Profit reached
								Log::notice("Take-Profit: Trade #" . $profit->trade_id . " reached its take-profit at " . $profit->price . " for the pair " . $profit->pair . " at " . $profit->exchange);

							}
						}
					}
				}

			} catch(Exception $e) {

				// Log CRITICAL: Exception
				Log::critical("BittrexTradeWatcher Exception: " . $e->getMessage());

				sleep(1);
				continue;

			}
			sleep(10);
		}
	}
}

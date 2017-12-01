<?php

namespace App\Console\Commands;

use App\Library\Console;
use Illuminate\Console\Command;
use Exception;

use App\Stop;
use App\Profit;
use App\Events\StopLossReached;
use App\Events\TakeProfitReached;
use App\Library\Services\Facades\Bittrex;

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
		$this->console = $util = new Console();

		$this->instruments = [];

		Bittrex::setAPI('','');
		

		// $krakenApi = new KrakenAPI($key, $secret, $url, $version, $sslVerify);

		stream_set_blocking(STDIN, 0);
		while(1) {
			if(ord(fgetc(STDIN)) == 113) {
				echo "QUIT detected...";
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
					$ticker = Bittrex::getTicker($market)->result;

					// Check last price with stop-loss for this pair
					$stopsToCheck = $stops->whereIn('pair', $market);
					foreach ($stopsToCheck as $stop) {
						if (floatval($ticker->Last) <= floatval($stop->price)) {
							event(new StopLossReached($stop, $ticker->Last));
							//print_r("Trade #" . $stop->trade_id . " -> Stop-Loss at " . $ticker->Last ."\n");
						}
					}

					// Check last price with take-profit for this pair
					$profitsToCheck = $profits->whereIn('pair', $market);
					foreach ($profitsToCheck as $profit) {
						if ( floatval($ticker->Last) >= floatval($profit->price)) {
							event(new TakeProfitReached($profit, $ticker->Last));
							//print_r("Trade #" . $profit->trade_id . " -> Take-Profit at " . $ticker->Last ."\n");
						}
					}
					print_r($market . " -> " . $ticker->Last ."\n");
				}

			} catch(\Exception $e) {
				sleep(1);
				continue;
			}
			sleep(5);
		}
	}
}

<?php

namespace App\Console\Commands;

use App\Library\Console;
use App\Library\Services\Facades\Bittrex;
use Illuminate\Console\Command;
use Exception;

use App\Stop;
use App\Profit;
use App\Events\StopLossReached;
use App\Events\TakeProfitReached;

class BittrexWatcher extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cryptobot:bittrex_watcher';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Watch prices at Bittrex.';

	 /**
     * @var currency pairs
     */
    protected $instrument;


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

		$this->instruments = ['BTC-ETH', 'BTC-XRP','BTC-THC', 'BTC-ETH', 'BTC-ADX','BTC-ADX'];

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
				$stops = Stop::where('exchange', 'bittrex')->get();
				$stopPairs = $stops->pluck('pair');
				// Get pairs to watch for stop-loss
				$profits = Profit::where('exchange', 'bittrex')->get();
				$profitPairs =  $stops->pluck('pair');

				// Merge stops and profits arrays into pairs array
				$pairs = $stopPairs->merge($profitPairs);
				// Set instruments with pairs form stops and profits
				$this->instruments = array_unique($pairs->all());

				

				foreach ($this->instruments as $market) {
					// Call to Bittrex API to get market ticker (last price)
					$ticker = Bittrex::getTicker($market)->result;

					// Check last price with stop-loss for this pair
					$stopsToCheck = $stops->whereIn('pair', $market);
					foreach ($stopsToCheck as $stop) {
						if (floatval($ticker->Last) <= floatval($stop->price)) {
							event(new StopLossReached($stop));
							//print_r("Trade #" . $stop->trade_id . " -> Stop-Loss at " . $ticker->Last ."\n");
						}
					}
					// Check last price with take-profit for this pair
					$profitsToCheck = $profits->whereIn('MarketName', $market);
					foreach ($profitsToCheck as $profit) {
						if ( floatval($ticker->Last) >= floatval($profit->price)) {
							event(new TakeProfitReached($profit));
							//print_r("Trade #" . $profit->trade_id . " -> Take-Profit at " . $ticker->Last ."\n");
						}
					}
					print_r($market . " -> " . $ticker->Last ."\n");
				}

			} catch(\Exception $e) {
				sleep(1);
				continue;
			}

			sleep(55);
		}
	}
}

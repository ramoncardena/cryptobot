<?php

namespace App\Console\Commands;

use App\Traits\OHLC;
use App\Library\Console;
use App\Library\Services\Facades\Bittrex;
use Illuminate\Console\Command;
use Exception;


/**
 * Command for Kraken Stream
 *
 * This class is filling the Database with data from the Kraken API
 *
 * @author    Lukas Kremsmayr
 * @copyright 2017 Lukas Kremsmayr
 * @license   http://www.gnu.org/licenses/ GNU General Public License, Version 3
 */
class BittrexStreamCommand extends Command {
	use OHLC;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cryptobot:bittrex_stream';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Bittrex data stream into database';

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

		$this->instruments = ['BTC-ETH', 'BTC-XRP','BTC-THC','BTC-ADX'];

		$key       = ''; //Don't need a key or secret for Public calls
		$secret    = '';
		$url       = 'https://api.kraken.com';
		$version   = 0;
		$sslVerify = true;

		Bittrex::setAPI('','');
		

		// $krakenApi = new KrakenAPI($key, $secret, $url, $version, $sslVerify);

		stream_set_blocking(STDIN, 0);
		while(1) {
			if(ord(fgetc(STDIN)) == 113) {
				echo "QUIT detected...";
				return null;
			}

			try {
				// $res = $krakenApi->QueryPublic('Ticker', array('pair' => implode(',', $instruments)));

				$res = collect(Bittrex::getMarketSummaries()->result);
				$filtered = $res->whereIn('MarketName', $this->instruments);
				//var_dump($filtered->all());
				
				foreach($filtered->all() as $data) {
					$ticker    = array();
					$ticker[7] = $data->Last;
					$ticker[8] = $data->Volume;
					$this->markOHLC($ticker, true, $data->MarketName);
					print_r($data->MarketName . " -> done!\n");
				}
			} catch(\Exception $e) {
				sleep(1);
				continue;
			}


			sleep(15);
		}
	}
}

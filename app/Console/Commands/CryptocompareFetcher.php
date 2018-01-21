<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\User;
use App\CryptocompareAsset;
use App\Library\Services\CoinGuru;


class CryptocompareFetcher extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cryptobot:FetchCryptocompare';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fetch data from CryptoCompare.';

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


		// Log INFO: BitttrexOrderWatcher launched
		Log::info("CryptoCompare Fetcher launched.");

		stream_set_blocking(STDIN, 0);

		if(ord(fgetc(STDIN)) == 113) {

			// Log INFO: BitttrexOrderWatcher stopped
			Log::info("CryptoCompare Fetcher halted by user (CTRL+C).");
			return null;

		}

		try {

			$guru = new CoinGuru;
            $coinList = $guru->cryptocompareCoingetList();
    
    		if ( $coinList && $coinList->Response == "Success" ) {

	            $logoBaseUrl = $coinList->BaseImageUrl;
	            $infoBaseUrl = $coinList->BaseLinkUrl;

	            CryptocompareAsset::truncate();

				// Iterate through all coins to save info
				foreach ($coinList->Data as $coin) {
					$asset = new CryptocompareAsset;
					$asset->id = $coin->Id;
		            property_exists($coin, 'Url') ? $asset->url = $logoBaseUrl . $coin->Url : $asset->url = "#";
		            property_exists($coin, 'ImageUrl') ? $asset->imageurl = $logoBaseUrl . $coin->ImageUrl : $asset->imageurl = "#";
		            $asset->name = $coin->Name;
		            $asset->symbol = $coin->Symbol;
		            $asset->coinname = $coin->CoinName;
		            $asset->fullname = $coin->FullName;
		            $asset->algorithm = $coin->Algorithm;
		            $asset->prooftype = $coin->ProofType;
		            $asset->fullypremined = $coin->FullyPremined;
		            $asset->totalcoinsupply = $coin->TotalCoinSupply;
		            $asset->preminedvalue = $coin->PreMinedValue;
		            $asset->totalcoinsfreefloat = $coin->TotalCoinsFreeFloat;
		            $asset->sortorder = $coin->SortOrder;
		            $asset->sponsored = $coin->Sponsored;
					$asset->save();
				}

				Log::info("CryptoCompare Fetcher finished.");
			}


		} catch(\Exception $e) {
			
			// Log CRITICAL: Exception
			Log::critical("[LoadPortfolio] Exception: " . $e->getMessage() . " " . $e->getCode() . " " . $e->getFile() . ":" . $e->getLine());
		}
			
	}
}

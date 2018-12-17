<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Events\PortfolioOpened;
use App\Events\PortfolioAssetLoaded;
use App\Events\PortfolioLoaded;

use App\Library\Services\Broker;
use App\Library\Services\CoinGuru;

use App\User;
use App\Portfolio;
use App\PortfolioAsset;

class LoadPortfolio implements ShouldQueue
{

     /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'portfolios';

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
     * @param  PortfolioOpened  $event
     * @return void
     */
    public function handle(PortfolioOpened $event)
    {
        try {

            if ($event->portfolio) {
              
                // Get user
                $user = User::find($event->portfolio->user_id);           

                // Reset portfolio totals
                $event->portfolio->balance = 0;
                $event->portfolio->balance_counter_value = 0;
                $event->portfolio->save();
                
                // New Coin Guru
                $guru = new CoinGuru;

                // New Broker
                $broker = new Broker;
                $broker->setUser($user);

                // Get user exchanges by retriveing connections
                // and making an array from column 'exchange'            
                $exchanges = $user->connections;
                if ($exchanges) $exchanges = $exchanges->pluck('exchange');
                else $exchanges = [];

                // Iterate each exchange to retrieve assets
                $start = microtime(true);
                foreach ($exchanges as $exchange) {
               
                    // Get balance from this exchange
                    $broker->setExchange($exchange);
                    $balance = $broker->getBalances2(); // Controlar si retorna error
                    if ($balance->success == true) {

                        // Save latest assets from exchange so we hage the actual
                        // list of assets
                        $latestAssets = $balance->result;
                       
                        // Retrieve current asset list for this exchange that the 
                        // user keeps in the system
                        $initialAssets = $user->assets->where('origin_name', ucfirst($exchange));
                        $initalAssetsSymbols = $initialAssets->pluck('symbol')->all();
                        
                        // Array to store the final list of assets from all exchanges
                        $finalAssets = [];

                        // Retrieve origin id to attach to each asset
                        $origin_id = $user->origins->where('name', ucfirst($exchange))->first()->id;

                    
                        // Iterate list of latest assets retrieved rom exchange                    
                        $start = microtime(true);
                        foreach ($latestAssets as $symbol => $amount) {
                            
                            $repeated = false;

                            // Special case of IOTA that is called IOT in CryptoCompare
                            $symbol = strtoupper($symbol);
                            if ($symbol == "IOTA") $symbol = "IOT"; 

                            // Check if asset was already in portfolio
                            if (in_array($symbol, $initalAssetsSymbols)) {
                                $repAsset =  $initialAssets->where('symbol', $symbol)->first();
                                $repeated = true;
                            }

                            // In case of an asset that we already have in portfolio
                            // we check if the amount has changed
                            if ($repeated) {

                                // Reset flag back to false
                                $repeated = false;

                                // If the repeated asset already exist check if the amount has changed
                                if ( $repAsset->amount != $amount) {

                                    // If the amount has changed calculate new purchase price
                                    $repAsset->amount = $amount;

                                    // Get exchange asset avarage purchase price
                                    $brokerResponse = $broker->getPurchasePrice2(strtoupper($symbol) . '/BTC', $amount);
                                    
                                    if ($brokerResponse->success) {

                                        $repAsset->initial_price = $brokerResponse->result->AvaragePrice;

                                    }
                                    else {

                                        $repAsset->initial_price = 0;

                                    }
                                    
                                }

                                $repAsset->update_id = $event->portfolio->update_id;
                                $repAsset->save();

                                // Add the asset to final assets list
                                $finalAssets = array_prepend($finalAssets, strtoupper($symbol));
                                
                            }
                            else {
                                
                                // Get coin info
                                // $symbol = strtoupper($symbol);
                                // if ($symbol == "IOTA") $symbol = "IOT"; 
                                //$coinInfo = $coinList->Data->$symbol;

                                // Create a new asset
                                $newAsset = new PortfolioAsset;
                                $newAsset->portfolio_id = $event->portfolio->id;
                                $newAsset->user_id = $user->id;
                                $newAsset->origin_id = $origin_id;
                                $newAsset->origin_name = ucfirst($exchange);
                                $newAsset->symbol = strtoupper($symbol);
                                $newAsset->full_name = $guru->getCoinInfo(strtoupper($symbol))->coinname;
                                $newAsset->logo_url = $guru->getCoinInfo(strtoupper($symbol))->imageurl;
                                $newAsset->info_url = $guru->getCoinInfo(strtoupper($symbol))->url;
                                $newAsset->price = 0;
                                $newAsset->balance = 0;
                                $counterValue = strtoupper($event->portfolio->counter_value);
                                $newAsset->counter_value = 0;
                                $newAsset->amount = $amount;
                                $newAsset->update_id = $event->portfolio->update_id;

                                // Get exchange asset avarage purchase price
                                $brokerResponse = $broker->getPurchasePrice2(strtoupper($symbol) . '/BTC', $amount);
                            
                                if ($brokerResponse->success) {
                                    $newAsset->initial_price = $brokerResponse->result->AvaragePrice;
                                }
                                else {
                                    $newAsset->initial_price = 0;
                                }
                                $newAsset->save();

                                // Add the asset to final assets list
                                $finalAssets = array_prepend($finalAssets, strtoupper($symbol));
                            }

                        }
                        $time_elapsed_secs = microtime(true) - $start;
                        var_dump("Iterate " . $exchange . ": " . $time_elapsed_secs . "s");

                        foreach ($initialAssets as $asset) {
                            $keep = in_array($asset->symbol, $finalAssets);
                            if (!$keep) {
                                PortfolioAsset::destroy($asset->id);
                            } 
                        }
                    }
                    else {
                        // We couldn't get balance from the exchange
                    }

                }

                $time_elapsed_secs = microtime(true) - $start;
                var_dump("Process exchanges: " . $time_elapsed_secs . "s");     
                
                // Iterate assets to count them and launch asset loaded event
                $assets = $event->portfolio->assets;
                $asset_count = 0;
                foreach ($assets as $asset) {
                    $asset_count++;
                    $asset->update_id = $event->portfolio->update_id;
                    $asset->save();
                    
                    // EVENT:  Portfolio Asset Loaded
                    event(new PortfolioAssetLoaded($asset));
                }

                // Save the number of assets in this portfolio
                $portfolio = $event->portfolio;
                $portfolio->asset_count = $asset_count;
                $portfolio->save();

                // EVENT:  Portfolio Loaded
                event(new PortfolioLoaded($portfolio, $asset_count));
            } 

        } catch (\Exception $e) {
            // Log CRITICAL: Exception
            Log::critical("[LoadPortfolio] Exception: " . $e->getMessage() . " " . $e->getCode() . " " . $e->getFile() . ":" . $e->getLine());
        }
    }
}

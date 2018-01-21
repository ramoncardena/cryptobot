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

                $start = microtime(true);
                // Get user
                $user = User::find($event->portfolio->user_id);
                $time_elapsed_secs = microtime(true) - $start;
                var_dump("Get user: " . $time_elapsed_secs . "s");

                // RESET PORTFOLIO TOTALS
                $event->portfolio->balance = 0;
                $event->portfolio->balance_counter_value = 0;
                $event->portfolio->save();
                
                $start = microtime(true);
                // Get Cryptocompare coin list properties
                $guru = new CoinGuru;
                $coinList = $guru->cryptocompareCoingetList();
                // TODO controlar si retorna error
                $logoBaseUrl = $coinList->BaseImageUrl;
                $infoBaseUrl = $coinList->BaseLinkUrl;
                $time_elapsed_secs = microtime(true) - $start;
                var_dump("Get coin list: " . $time_elapsed_secs . "s");

                // New Broker
                $broker = new Broker;
                $broker->setUser($user);

                // Get the user's exchanges
                // $exchanges = $user->settings()->get('exchanges');
                // if ($exchanges) $exchanges = array_divide($exchanges)[0];
                // else $exchanges = [];

                $start = microtime(true);
                $exchanges = $user->connections;
                if ($exchanges) $exchanges = $exchanges->pluck('exchange');
                else $exchanges = [];
                $time_elapsed_secs = microtime(true) - $start;
                var_dump("Get exchanges: " . $time_elapsed_secs . "s");

                //var_dump($exchanges);

                $start = microtime(true);
                foreach ($exchanges as $exchange) {
               

                    // Get balance from this exchange
                    $broker->setExchange($exchange);
                    $balance = $broker->getBalances2(); // Controlar si retorna error
                    $latestAssets = $balance->result;
                   
                    //var_dump($latestAssets);

                    // Retrieve current asset list for this exchange
                    $initialAssets = $user->assets->where('origin_name', ucfirst($exchange));
                    $finalAssets = [];

                    // Retrieve origin id to attach to each asset
                    $origin_id = $user->origins->where('name', ucfirst($exchange))->first()->id;

                    $initalAssetsSymbols = $initialAssets->pluck('symbol')->all();

                    foreach ($latestAssets as $symbol => $coin) {
                        $repeated = false;

                        // Special case of IOTA that is called IOT in CryptoCompare
                        $symbol = strtoupper($symbol);
                        if ($symbol == "IOTA") $symbol = "IOT"; 


                        // Find if the asset already exists
                        // foreach ($initialAssets as $currentAsset) {

                        //     if ( strtoupper($currentAsset->symbol) == strtoupper($symbol) ) {

                        //         // Save the asset if already exists and mark as repeated
                        //         $repAsset =  $currentAsset;
                        //         $repeated = true;

                        //     }

                        // }
                        
                        if (in_array($symbol, $initalAssetsSymbols)) {
                            $repAsset =  $initialAssets->where('symbol', $symbol)->first();
                            $repeated = true;
                        }

                        if ($repeated) {
                            $repeated = false;

                            // If the repAsset already exist check if the amount has changed
                            if ( $repAsset->amount != $coin) {

                                // If the amount has changed calculate new purchase price
                                $repAsset->amount = $coin;

                                // Get exchange asset avarage buy price
                               
                                $brokerResponse = $broker->getPurchasePrice2(strtoupper($symbol) . '/BTC', $coin);
                                

                                if ($brokerResponse->success) {
                                    $repAsset->initial_price = $brokerResponse->result->AvaragePrice;
                                }
                                else {
                                    $repAsset->initial_price = 0;
                                }
                                

                            }

                            $repAsset->update_id = $event->portfolio->update_id;
                            $repAsset->save();
                            $finalAssets = array_prepend($finalAssets, strtoupper($symbol));
                            
                        }
                        else {
                            
                            // Get coin info
                            // $symbol = strtoupper($symbol);
                            // if ($symbol == "IOTA") $symbol = "IOT"; 
                            $coinInfo = $coinList->Data->$symbol;

                            // Create a new asset
                            $newAsset = new PortfolioAsset;
                            $newAsset->portfolio_id = $event->portfolio->id;
                            $newAsset->user_id = $user->id;
                            $newAsset->origin_id = $origin_id;
                            $newAsset->origin_name = ucfirst($exchange);
                            $newAsset->symbol = strtoupper($symbol);
                            $newAsset->full_name = $coinInfo->CoinName;
                            $newAsset->logo_url = $logoBaseUrl . $coinInfo->ImageUrl;
                            $newAsset->info_url = $infoBaseUrl . $coinInfo->Url;
                            $newAsset->price = 0;
                            $newAsset->balance = 0;
                            $counterValue = strtoupper($event->portfolio->counter_value);
                            $newAsset->counter_value = 0;
                            $newAsset->amount = $coin;
                            $newAsset->update_id = $event->portfolio->update_id;

                            // Get exchange asset avarage buy price
                            // var_dump("New: " . strtoupper($symbol) . '/BTC');
                            
                            $brokerResponse = $broker->getPurchasePrice2(strtoupper($symbol) . '/BTC', $coin);
                        
                            if ($brokerResponse->success) {
                                $newAsset->initial_price = $brokerResponse->result->AvaragePrice;
                            }
                            else {
                                $newAsset->initial_price = 0;
                            }
                            $newAsset->save();

                            $finalAssets = array_prepend($finalAssets, strtoupper($symbol));
                        }

                    }

                    foreach ($initialAssets as $asset) {
                        $keep = in_array($asset->symbol, $finalAssets);
                        if (!$keep) {
                            PortfolioAsset::destroy($asset->id);
                        } 
                    }

                }
                $time_elapsed_secs = microtime(true) - $start;
                var_dump("Process exchanges: " . $time_elapsed_secs . "s");
                
                $start = microtime(true);
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
                $time_elapsed_secs = microtime(true) - $start;
                var_dump("Count assets: " . $time_elapsed_secs . "s");

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

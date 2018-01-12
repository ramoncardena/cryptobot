<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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

                // RESET PORTFOLIO TOTALS
                $event->portfolio->balance = 0;
                $event->portfolio->balance_counter_value = 0;
                $event->portfolio->save();
                
                // Get Cryptocompare coin list properties
                $guru = new CoinGuru;
                $coinList = $guru->cryptocompareCoingetList();
                // TODO controlar si retorna error
                $logoBaseUrl = $coinList->BaseImageUrl;
                $infoBaseUrl = $coinList->BaseLinkUrl;

                // New Broker
                $broker = new Broker;
                $broker->setUser($user);

                // Get the user's exchanges
                $exchanges = $user->settings()->get('exchanges');
                if ($exchanges) $exchanges = array_divide($exchanges)[0];
                else $exchanges = [];

                foreach ($exchanges as $exchange) {
               
                    // Delete assets from this exchange
                    // foreach ($user->assets->where('origin_name', ucfirst($exchange)) as $asset) {
                    //     PortfolioAsset::destroy($asset->id);
                    // }

                    // Get balance from this exchange
                    $broker->setExchange($exchange);
                    $balance = $broker->getBalances(); // Controlar si retorna error
                    $latestAssets = $balance->result;
                   

                    // Retrieve current asset list for this exchange
                    $initialAssets = $user->assets->where('origin_name', ucfirst($exchange));
                    $finalAssets = [];

                    // Retrieve origin id to attach to each asset
                    $origin_id = $user->origins->where('name', ucfirst($exchange))->first()->id;

                    foreach ($latestAssets as $coin) {
                        $repeated = false;

                        // Find if the asset already exists
                        foreach ($initialAssets as $currentAsset) {

                            if ( strtoupper($currentAsset->symbol) == strtoupper($coin->Currency) ) {

                                // Save the asset if already exists and mark as repeated
                                $asset =  $currentAsset;
                                $repeated = true;

                            }

                        }
                        
                        if ($repeated) {
                            $repeated = false;

                            // If the asset already exist check if the amount has changed
                            if ( $asset->amount != $coin->Balance) {

                                
                                // If the amount has changed calculate new purchase price
                                $asset->amount = $coin->Balance;

                                // Get exchange asset avarage buy price
                                $brokerResponse = $broker->getPurchasePrice('BTC-' . $coin->Currency, $coin->Balance);
                                
                                if ($brokerResponse->success) {
                                    $asset->initial_price = $brokerResponse->result->AvaragePrice;
                                }
                                else {
                                    $asset->initial_price = 0;
                                }
                                

                            }

                            $asset->update_id = $event->portfolio->update_id;
                            $asset->save();
                            $finalAssets = array_prepend($finalAssets, $coin->Currency);
                            
                        }
                        else {
                            
                            // Get coin info
                            $symbol = $coin->Currency;
                            $coinInfo = $coinList->Data->$symbol;

                            // Create a new asset
                            $asset = new PortfolioAsset;
                            $asset->portfolio_id = $event->portfolio->id;
                            $asset->user_id = $user->id;
                            $asset->origin_id = $origin_id;
                            $asset->origin_name = ucfirst($exchange);
                            $asset->symbol = $coin->Currency;
                            $asset->full_name = $coinInfo->CoinName;
                            $asset->logo_url = $logoBaseUrl . $coinInfo->ImageUrl;
                            $asset->info_url = $infoBaseUrl . $coinInfo->Url;
                            $asset->price = 0;
                            $asset->balance = 0;
                            $counterValue = strtoupper($event->portfolio->counter_value);
                            $asset->counter_value = 0;
                            $asset->amount = $coin->Balance;
                            $asset->update_id = $event->portfolio->update_id;

                            // Get exchange asset avarage buy price
                            $brokerResponse = $broker->getPurchasePrice('BTC-' . $coin->Currency, $coin->Balance);
                        
                            if ($brokerResponse->success) {
                                $asset->initial_price = $brokerResponse->result->AvaragePrice;
                            }
                            else {
                                $asset->initial_price = 0;
                            }
                            $asset->save();

                            $finalAssets = array_prepend($finalAssets, $coin->Currency);
                        }

                        var_dump($finalAssets);

                        foreach ($initialAssets as $asset) {

                            $keep = array_has($finalAssets, $asset->symbol);
                            if (!$keep) PortfolioAsset::destroy($asset->id);
                        }

                    }

                }

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

        } catch (Exception $e) {
            // Log CRITICAL: Exception
            Log::critical("[LoadPortfolio] Exception: " . $e->getMessage());
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\PortfolioAssetLoaded;
use App\Events\PortfolioAssetUpdated;
use Illuminate\Support\Facades\Log;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Library\Services\CoinGuru;

use App\User;
use App\Portfolio;
use App\PortfolioAsset;

class UpdateAssetData implements ShouldQueue
{

     /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'portfolios';

    /**
     * Handle the event.
     *
     * @param  PortfolioAssetLoaded  $event
     * @return void
     */
    
    public function handle(PortfolioAssetLoaded $event)
    {
        try {

            $portfolio = Portfolio::find($event->asset->portfolio_id);
            $updateId = $portfolio->update_id;
            $asset = $event->asset;

            // If portfolio ID is diffetent than asset ID that means that portfolio is reloading 
            // and we do nothing, if IDs match we update the asset
            if ($asset->update_id == $portfolio->update_id) {          
                // Get Cryptocompare coin list properties
                $guru = new CoinGuru;
                $assetPrice = $guru->cryptocomparePriceGetSinglePrice($asset->symbol, "BTC");
                $asset->price = $assetPrice->BTC;
                $asset->balance =  floatval($asset->amount) * floatval($assetPrice->BTC);
                $counterValue = strtoupper($portfolio->counter_value);
                $asset->counter_value = floatval($asset->amount) * floatval($guru->cryptocomparePriceGetSinglePrice($asset->symbol, $counterValue)->$counterValue);
                $asset->save();

                // EVENT:  Portfolio Asset Loaded
                event(new PortfolioAssetUpdated($asset));
            }

        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[UpdateAssetData] Exception: " . $e->getMessage());

        }
        

    }
}

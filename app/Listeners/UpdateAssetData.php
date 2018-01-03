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
    public $queue = 'assets';

    /**
     * Handle the event.
     *
     * @param  PortfolioAssetLoaded  $event
     * @return void
     */
    
    public function handle(PortfolioAssetLoaded $event)
    {
        try {

            // Get Cryptocompare coin list properties
            $guru = new CoinGuru;
            $portfolio = Portfolio::find($event->asset->portfolio_id);

            $asset = $event->asset;
            $assetPrice = $guru->cryptocomparePriceGetSinglePrice($asset->symbol, "BTC");
            $asset->price = $assetPrice->BTC;
            $asset->balance =  floatval($asset->amount) * floatval($assetPrice->BTC);
            $counterValue = strtoupper($portfolio->counter_value);
            $asset->counter_value = floatval($asset->amount) * floatval($guru->cryptocomparePriceGetSinglePrice($asset->symbol, $counterValue)->$counterValue);
            $asset->save();

            // EVENT:  Portfolio Asset Loaded
            event(new PortfolioAssetUpdated($asset));

        } catch (Exception $e) {

            // Log CRITICAL: Exception
            Log::critical("[UpdateAssetData] Exception: " . $e->getMessage());

        }
        

    }
}

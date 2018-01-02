<?php

namespace App\Listeners;

use App\Events\PortfolioAssetLoaded;
use App\Events\PortfolioAssetUpdated;

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
        // Get Cryptocompare coin list properties
        $guru = new CoinGuru;
        $portfolio = Portfolio::find($event->asset->portfolio_id);

        $assetPrice = $guru->cryptocomparePriceGetSinglePrice($event->asset->symbol, "BTC");
        $event->asset->price = $assetPrice->BTC;
        $event->asset->balance =  $event->asset->amount * $assetPrice->BTC;
        $counterValue = strtoupper($portfolio->counter_value);
        $event->asset->counter_value = $event->asset->amount * $guru->cryptocomparePriceGetSinglePrice($event->asset->symbol,$counterValue)->$counterValue;
        $event->asset->save();

        // EVENT:  Portfolio Asset Loaded
        event(new PortfolioAssetUpdated($event->asset));

    }
}

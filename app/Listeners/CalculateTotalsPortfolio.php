<?php

namespace App\Listeners;


use App\Events\PortfolioTotalsCalculated;
use App\Events\PortfolioLoaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Library\Services\CoinGuru;

use App\User;
use App\Portfolio;
use App\PortfolioAsset;

class CalculateTotalsPortfolio implements ShouldQueue
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
     * @param  PortfolioLoaded  $event
     * @return void
     */
    public function handle(PortfolioLoaded $event)
    {
        try {
            if ($event->portfolio) {

                // Get user
                $user = User::find($event->portfolio->user_id);

                // Get assets
                $assets = $event->portfolio->assets;

                $guru = new CoinGuru;
                $totalBtc = 0;
                $totalFiat = 0;

                foreach ($assets as $asset) {
                    $assetPriceBtc = $guru->cryptocomparePriceGetSinglePrice($asset->symbol, "BTC");
                    $counterValue = strtoupper($event->portfolio->counter_value);
                    $assetPriceFiat = $guru->cryptocomparePriceGetSinglePrice($asset->symbol, $counterValue);
                    $totalBtc = $totalBtc + ($asset->amount * $assetPriceBtc->BTC);     
                    if ($counterValue=="EUR") $totalFiat = $totalFiat + ($asset->amount * $assetPriceFiat->EUR);  
                    if ($counterValue=="USD") $totalFiat = $totalFiat + ($asset->amount * $assetPriceFiat->USD);           
                }

                $event->portfolio->balance = $totalBtc;
                $event->portfolio->balance_counter_value = $totalFiat;
                $event->portfolio->save();

                // EVENT:  Portfolio Loaded
                event(new PortfolioTotalsCalculated($event->portfolio));
            } 

        } catch (Exception $e) {
            // Log CRITICAL: Exception
            Log::critical("[CalculateTotalsPortfolio] Exception: " . $e->getMessage());

        }
    }
}

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
                // TODO controlar si retorna errot
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
                    foreach ($user->assets->where('origin_name', ucfirst($exchange)) as $asset) {
                        PortfolioAsset::destroy($asset->id);
                    }

                    // Get balance from this exchange
                    $broker->setExchange($exchange);
                    $balance = $broker->getBalances();
                    // Controlar si retorna error

                    
                    $origin_id = $user->origins->where('name', ucfirst($exchange))->first()->id;
                    foreach ($balance->result as $coin) {
             
                        $symbol = $coin->Currency;
                        $coinInfo = $coinList->Data->$symbol;
                        $asset = new PortfolioAsset;
                        $asset->portfolio_id = $event->portfolio->id;
                        $asset->user_id = $user->id;
                        $asset->origin_id = $origin_id;
                        $asset->origin_name = ucfirst($exchange);
                        $asset->symbol = $coin->Currency;
                        $asset->amount = $coin->Balance;
                        $asset->full_name = $coinInfo->CoinName;
                        $asset->logo_url = $logoBaseUrl . $coinInfo->ImageUrl;
                        $asset->info_url = $infoBaseUrl . $coinInfo->Url;
                        $asset->price = 0;
                        $asset->balance = 0;
                        $counterValue = strtoupper($event->portfolio->counter_value);
                        $asset->counter_value = 0;
                        $asset->initial_price = 0;
                        $asset->update_id = $event->portfolio->update_id;

                        $asset->save();
                    }
                }

                $assets = $event->portfolio->assets;
                $asset_count = 0;
                foreach ($assets as $asset) {
                    $asset_count++;
                    $asset->update_id = $event->portfolio->update_id;
                    $asset->save();
                    
                    // EVENT:  Portfolio Asset Loaded
                    event(new PortfolioAssetLoaded($asset));

                }
                
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

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
                    $start = microtime(true);
                    // Delete assets from this exchange
                    foreach ($user->assets->where('origin_name', ucfirst($exchange)) as $asset) {
                        PortfolioAsset::destroy($asset->id);
                    }

                    $time_elapsed_secs = microtime(true) - $start;
                    var_dump("Time to delete Assets: " . $time_elapsed_secs);

                    $start = microtime(true);
                    // Get balance from this exchange
                    $broker->setExchange($exchange);
                    $balance = $broker->getBalances();
                    // Controlar si retorna error
                     $time_elapsed_secs = microtime(true) - $start;
                     var_dump("Time to load exchange balances: " . $time_elapsed_secs);

                    $start = microtime(true);
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

                        $asset->save();
                    }
                    $time_elapsed_secs = microtime(true) - $start;
                    var_dump("Time to save exchange assets: " . $time_elapsed_secs);
                }

                $assets = $event->portfolio->assets;
                $start = microtime(true);
                foreach ($assets as $asset) {

                    // EVENT:  Portfolio Asset Loaded
                    event(new PortfolioAssetLoaded($asset));
                }
                $time_elapsed_secs = microtime(true) - $start;
                var_dump("Time to update assets: " . $time_elapsed_secs);

                // EVENT:  Portfolio Loaded
                event(new PortfolioLoaded($event->portfolio));
            } 

        } catch (Exception $e) {
            // Log CRITICAL: Exception
            Log::critical("[LoadPortfolio] Exception: " . $e->getMessage());

            // Add delay before requeueing
            sleep(env('FAILED_PORTFOFLIO_DELAY', 5));

            // EVENT:  PortfolioOpened
            event(new PortfolioOpened($event->portfolio));
        }
    }
}

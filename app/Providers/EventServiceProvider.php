<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\PortfolioLoaded' => [],
        'App\Events\PortfolioOpened' => [
            'App\Listeners\LoadPortfolio'
        ],
        'App\Events\PortfolioAssetLoaded' => [],
        'App\Events\TradeCancelled' => [
            'App\Listeners\CancelTrade',
        ],
        'App\Events\CloseOrderCompleted' => [
            'App\Listeners\CloseTrade',
        ],
        'App\Events\TradeClosed' => [
            'App\Listeners\EmailTradeClosed',
        ],
        'App\Events\TradeOpened' => [
            'App\Listeners\EmailTradeOpened',
        ],
        'App\Events\ConditionReached' => [
            'App\Listeners\ExecuteConditional',
        ],
        'App\Events\StopLossReached' => [
            'App\Listeners\ExecuteStopLoss',
        ],
        'App\Events\TakeProfitReached' => [
            'App\Listeners\ExecuteTakeProfit',
        ],
        'App\Events\ConditionNotReached' => [
            'App\Listeners\KeepTrackingConditional',
        ],
        'App\Events\OrderNotCompleted' => [
            'App\Listeners\KeepTrackingOrder',
        ],
        'App\Events\StopLossNotReached' => [
            'App\Listeners\KeepTrackingStopLoss',
        ],
        'App\Events\TakeProfitNotReached' => [
            'App\Listeners\KeepTrackingTakeProfit',
        ],
        'App\Events\TradeKept' => [
            'App\Listeners\KeepTrade',
        ],
        'App\Events\OpenOrderCompleted' => [
            'App\Listeners\OpenTrade',
        ],
        'App\Events\ConditionalLaunched' => [
            'App\Listeners\TrackConditional',
        ],
        'App\Events\OrderLaunched' => [
            'App\Listeners\TrackOrder',
        ],
        'App\Events\StopLossLaunched' => [
            'App\Listeners\TrackStopLoss',
        ],
        'App\Events\TakeProfitLaunched' => [
            'App\Listeners\TrackTakeProfit',
        ],
        
    ];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

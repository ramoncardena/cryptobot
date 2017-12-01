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
        'App\Events\TradeOpened' => [
            'App\Listeners\EmailTradeOpened',
        ],
        'App\Events\TradeClosed' => [
            'App\Listeners\EmailTradeClosed',
        ],
        'App\Events\StopLossReached' => [
            'App\Listeners\ExecuteStopLoss',
        ],
        'App\Events\TakeProfitReached' => [
            'App\Listeners\ExecuteTakeProfit',
        ],
        'App\Events\OrderLaunched' => [
            'App\Listeners\TrackOrder',
        ],
        'App\Events\OrderCompleted' => [
            'App\Listeners\CloseTrade',
        ],
        'App\Events\ConditionReached' => [
            'App\Listeners\ExecuteOrder',
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

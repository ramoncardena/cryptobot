<?php

namespace App\Listeners;

use App\Events\TradeOpened;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackStopLoss
{
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
     * @param  TradeOpened  $event
     * @return void
     */
    public function handle(TradeOpened $event)
    {
        var_dump($event->trade['name']);
    }
}

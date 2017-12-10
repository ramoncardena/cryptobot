<?php

namespace App\Listeners;

use App\Events\TradeStatusChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateTradeStatus
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
     * @param  TradeStatusChanged  $event
     * @return void
     */
    public function handle(TradeStatusChanged $event)
    {
        //
    }
}

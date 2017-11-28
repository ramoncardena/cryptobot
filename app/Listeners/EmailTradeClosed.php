<?php

namespace App\Listeners;

use App\Events\TradeClosed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailTradeClosed
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
     * @param  TradeClosed  $event
     * @return void
     */
    public function handle(TradeClosed $event)
    {
        //
    }
}

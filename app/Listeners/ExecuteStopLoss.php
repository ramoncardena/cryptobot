<?php

namespace App\Listeners;

use App\Events\StopLossReached;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExecuteStopLoss
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
     * @param  StopLossReached  $event
     * @return void
     */
    public function handle(StopLossReached $event)
    {
       var_dump($event);
    }
}

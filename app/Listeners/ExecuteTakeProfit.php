<?php

namespace App\Listeners;

use App\Events\TakeProfitReached;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExecuteTakeProfit
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
     * @param  TakeProfitReached  $event
     * @return void
     */
    public function handle(TakeProfitReached $event)
    {
        var_dump($event);
    }
}

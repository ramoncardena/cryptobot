<?php

namespace App\Listeners;

use App\Events\ConditionReached;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExecuteOrder
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
     * @param  ConditionReached  $event
     * @return void
     */
    public function handle(ConditionReached $event)
    {
        var_dump("Conditional order executed! (" . $event->conditional->pair . ")");
    }
}

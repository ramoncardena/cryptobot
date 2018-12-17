<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PortfolioOpened
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'portfolios';
    
    public $portfolio;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($portfolio)
    {
        $this->portfolio = $portfolio;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('portfolios.' . $this->portfolio->id);
    }
}

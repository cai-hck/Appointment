<?php

namespace App\Events;

use App\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InternalCallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $introom;
    public $status;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($introom, $status=0)
    {
        $this->introom = $introom;
        $this->status = $status; //0=> start, 1=>end, 2=>decline 
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('internalcall'. $this->introom);
    }
}

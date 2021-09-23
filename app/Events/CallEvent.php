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

class CallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bkid;
    public $status;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bkid, $status=0)
    {
        $this->bkid = $bkid;
        $this->status = $status; //0=> start, 1=>end, 2=>decline 
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('call'. $this->bkid);
    }
}

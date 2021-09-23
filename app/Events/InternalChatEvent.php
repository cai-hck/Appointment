<?php

namespace App\Events;

use App\Chat;
use App\InternalChat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InternalChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $status;
    public $mission;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(InternalChat $chat, $mission,$status=0)
    {
        $this->chat = $chat;
        $this->mission = $mission;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //dd($this->chat);
        return new PresenceChannel('internal'.$this->mission);
    }
}

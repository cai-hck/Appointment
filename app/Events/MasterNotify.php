<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MasterNotify implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $username;
    public $avatar;
    public $message;
    public $consultant_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($consultant_id, $username, $avatar, $message)
    {
        //
        $this->consultant_id = $consultant_id;
        $this->username = $username;
        $this->avatar = $avatar;//asset('client/assets/img/client_avatar.png');//$avatar;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['master-notify_'. $this->consultant_id ];
    }

  /*   public function broadcastAs()
    {
        return 'mission-notify';
    } */
}

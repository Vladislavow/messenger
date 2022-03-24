<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected User $user;
    protected $idd = 0;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $idd = 0)
    {
        $this->user = $user;
        $this->idd = $idd;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->idd);
    }

    public function broadcastWith()
    {
        return ['chat' =>  $this->user];
    }
}

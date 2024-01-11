<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatEvent implements ShouldBroadcastNow
{
    use Batchable, Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * message
     *
     * @var [type]
     */
    public $message;

    /**
     * message type
     *
     * @var [type]
     */
    public $messageType;


    public function __construct($message, $messageType)
    {
        $this->message = $message;
        $this->messageType = $messageType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('UserChatChannel'),
        ];
    }


    public function broadcastWith(): array
    {
        // type, wait, error,complete
        return [
            "message" => $this->message,
            "type" => $this->messageType
        ];
    }
}

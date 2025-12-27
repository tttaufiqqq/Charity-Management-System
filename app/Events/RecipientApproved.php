<?php

namespace App\Events;

use App\Models\Recipient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecipientApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $recipient;

    /**
     * Create a new event instance.
     */
    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('recipients');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'recipient.approved';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'recipient_id' => $this->recipient->Recipient_ID,
            'name' => $this->recipient->Name,
            'status' => $this->recipient->Status,
            'timestamp' => now()->toISOString(),
        ];
    }
}

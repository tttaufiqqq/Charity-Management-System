<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignUpdateFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $donationData;

    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(array $donationData, string $reason = '')
    {
        $this->donationData = $donationData;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('campaigns');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'campaign.update.failed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'donation_id' => $this->donationData['donation_id'],
            'campaign_id' => $this->donationData['campaign_id'],
            'amount' => $this->donationData['amount'],
            'reason' => $this->reason,
            'timestamp' => now()->toISOString(),
        ];
    }
}

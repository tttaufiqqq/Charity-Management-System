<?php

namespace App\Events;

use App\Models\Campaign;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $campaign;

    /**
     * Create a new event instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
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
        return 'campaign.approved';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'campaign_id' => $this->campaign->Campaign_ID,
            'organization_id' => $this->campaign->Organization_ID,
            'title' => $this->campaign->Title,
            'status' => $this->campaign->Status,
            'timestamp' => now()->toISOString(),
        ];
    }
}

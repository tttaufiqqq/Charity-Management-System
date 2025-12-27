<?php

namespace App\Events;

use App\Models\Donation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $donation;

    /**
     * Create a new event instance.
     */
    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('donations');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'donation.completed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'donation_id' => $this->donation->Donation_ID,
            'campaign_id' => $this->donation->Campaign_ID,
            'amount' => $this->donation->Amount,
            'donor_id' => $this->donation->Donor_ID,
            'timestamp' => now()->toISOString(),
        ];
    }
}

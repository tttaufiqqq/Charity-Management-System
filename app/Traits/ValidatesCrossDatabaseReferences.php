<?php

namespace App\Traits;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * ValidatesCrossDatabaseReferences Trait
 *
 * Provides application-layer validation for cross-database foreign key references
 * since database-level foreign key constraints cannot span multiple databases.
 *
 * Use this trait in controllers, form requests, or service classes where
 * cross-database referential integrity must be enforced.
 */
trait ValidatesCrossDatabaseReferences
{
    /**
     * Validate that a User exists in the izzhilmy database
     *
     * @param  string  $fieldName  The field name for error messages
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateUserExists(int $userId, string $fieldName = 'User_ID'): User
    {
        $user = User::on('izzhilmy')->find($userId);

        if (! $user) {
            throw ValidationException::withMessages([
                $fieldName => 'The selected user does not exist. (Cross-database validation: izzhilmy)',
            ]);
        }

        return $user;
    }

    /**
     * Validate that a Campaign exists in the izzati database
     *
     * @param  string  $fieldName  The field name for error messages
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateCampaignExists(int $campaignId, string $fieldName = 'Campaign_ID'): Campaign
    {
        $campaign = Campaign::on('izzati')->find($campaignId);

        if (! $campaign) {
            throw ValidationException::withMessages([
                $fieldName => 'The selected campaign does not exist. (Cross-database validation: izzati)',
            ]);
        }

        return $campaign;
    }

    /**
     * Validate that a Campaign is active before accepting donations
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateCampaignIsActive(int $campaignId, string $fieldName = 'Campaign_ID'): Campaign
    {
        $campaign = $this->validateCampaignExists($campaignId, $fieldName);

        if ($campaign->Status !== 'Active') {
            throw ValidationException::withMessages([
                $fieldName => "The selected campaign is not active. Current status: {$campaign->Status}",
            ]);
        }

        return $campaign;
    }

    /**
     * Validate that an Event exists in the izzati database
     *
     * @param  string  $fieldName  The field name for error messages
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateEventExists(int $eventId, string $fieldName = 'Event_ID'): Event
    {
        $event = Event::on('izzati')->find($eventId);

        if (! $event) {
            throw ValidationException::withMessages([
                $fieldName => 'The selected event does not exist. (Cross-database validation: izzati)',
            ]);
        }

        return $event;
    }

    /**
     * Validate that an Event is upcoming before allowing registrations
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateEventIsUpcoming(int $eventId, string $fieldName = 'Event_ID'): Event
    {
        $event = $this->validateEventExists($eventId, $fieldName);

        if ($event->Status !== 'Upcoming') {
            throw ValidationException::withMessages([
                $fieldName => "The selected event is not accepting registrations. Current status: {$event->Status}",
            ]);
        }

        return $event;
    }

    /**
     * Validate that a Recipient exists in the adam database
     *
     * @param  string  $fieldName  The field name for error messages
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRecipientExists(int $recipientId, string $fieldName = 'Recipient_ID'): Recipient
    {
        $recipient = Recipient::on('adam')->find($recipientId);

        if (! $recipient) {
            throw ValidationException::withMessages([
                $fieldName => 'The selected recipient does not exist. (Cross-database validation: adam)',
            ]);
        }

        return $recipient;
    }

    /**
     * Validate that a Recipient is approved before allocating funds
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRecipientIsApproved(int $recipientId, string $fieldName = 'Recipient_ID'): Recipient
    {
        $recipient = $this->validateRecipientExists($recipientId, $fieldName);

        if ($recipient->Status !== 'Approved') {
            throw ValidationException::withMessages([
                $fieldName => "The selected recipient is not approved. Current status: {$recipient->Status}",
            ]);
        }

        return $recipient;
    }

    /**
     * Validate multiple cross-database references at once
     *
     * Example usage:
     * $this->validateCrossDatabaseReferences([
     *     'user' => ['id' => $userId, 'field' => 'User_ID'],
     *     'campaign' => ['id' => $campaignId, 'field' => 'Campaign_ID'],
     * ]);
     *
     * @param  array  $references  Array of reference types and IDs
     * @return array Array of validated models
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateCrossDatabaseReferences(array $references): array
    {
        $validated = [];

        foreach ($references as $type => $data) {
            $id = $data['id'];
            $field = $data['field'] ?? ucfirst($type).'_ID';

            $validated[$type] = match ($type) {
                'user' => $this->validateUserExists($id, $field),
                'campaign' => $this->validateCampaignExists($id, $field),
                'campaignActive' => $this->validateCampaignIsActive($id, $field),
                'event' => $this->validateEventExists($id, $field),
                'eventUpcoming' => $this->validateEventIsUpcoming($id, $field),
                'recipient' => $this->validateRecipientExists($id, $field),
                'recipientApproved' => $this->validateRecipientIsApproved($id, $field),
                default => throw new \InvalidArgumentException("Unknown validation type: {$type}"),
            };
        }

        return $validated;
    }

    /**
     * Validate sufficient campaign funds for allocation
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateCampaignHasSufficientFunds(int $campaignId, float $allocationAmount): Campaign
    {
        $campaign = $this->validateCampaignExists($campaignId);

        // Calculate remaining funds
        $allocatedAmount = $campaign->donationAllocations()->sum('Amount_Allocated') ?? 0;
        $remainingFunds = $campaign->Collected_Amount - $allocatedAmount;

        if ($allocationAmount > $remainingFunds) {
            throw ValidationException::withMessages([
                'Amount_Allocated' => sprintf(
                    'Insufficient funds in campaign. Available: RM %.2f, Requested: RM %.2f',
                    $remainingFunds,
                    $allocationAmount
                ),
            ]);
        }

        return $campaign;
    }

    /**
     * Validate that event has capacity for volunteer registration
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateEventHasCapacity(int $eventId): Event
    {
        $event = $this->validateEventExists($eventId);

        $currentVolunteers = $event->volunteers()->count();

        if ($event->Capacity && $currentVolunteers >= $event->Capacity) {
            throw ValidationException::withMessages([
                'Event_ID' => "The event is at full capacity. ({$currentVolunteers}/{$event->Capacity} volunteers)",
            ]);
        }

        return $event;
    }

    /**
     * Validate that user doesn't already have a profile of a specific type
     *
     * This prevents duplicate Donor/Volunteer/Organization/PublicProfile records
     *
     * @param  string  $profileType  'donor', 'volunteer', 'organization', 'publicProfile'
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateUserDoesNotHaveProfile(int $userId, string $profileType): void
    {
        $user = $this->validateUserExists($userId);

        $hasProfile = match ($profileType) {
            'donor' => $user->donor()->exists(),
            'volunteer' => $user->volunteer()->exists(),
            'organization' => $user->organization()->exists(),
            'publicProfile' => $user->publicProfile()->exists(),
            default => throw new \InvalidArgumentException("Unknown profile type: {$profileType}"),
        };

        if ($hasProfile) {
            throw ValidationException::withMessages([
                'User_ID' => "This user already has a {$profileType} profile.",
            ]);
        }
    }

    /**
     * Validate donation amount against campaign goal
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateDonationAmount(int $campaignId, float $donationAmount): Campaign
    {
        $campaign = $this->validateCampaignIsActive($campaignId);

        // Check if donation would exceed goal
        $remainingToGoal = $campaign->Goal_Amount - $campaign->Collected_Amount;

        if ($donationAmount > $remainingToGoal && $campaign->Goal_Amount > 0) {
            // Warning, not error - allow over-goal donations
            logger()->warning('Donation exceeds campaign goal', [
                'campaign_id' => $campaignId,
                'donation_amount' => $donationAmount,
                'remaining_to_goal' => $remainingToGoal,
            ]);
        }

        // Validate minimum donation amount (optional)
        if ($donationAmount < 1.00) {
            throw ValidationException::withMessages([
                'Amount' => 'Donation amount must be at least RM 1.00',
            ]);
        }

        return $campaign;
    }
}

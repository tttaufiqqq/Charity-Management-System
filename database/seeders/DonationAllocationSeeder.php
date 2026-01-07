<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\DonationAllocation;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DonationAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Databases:
     * - hannah (MySQL): DonationAllocation
     * - adam (MySQL): Recipients
     * - izzati (PostgreSQL): Campaigns
     */
    public function run(): void
    {
        // Get only approved recipients from adam
        $approvedRecipients = Recipient::on('adam')->where('Status', 'Approved')->get();

        if ($approvedRecipients->isEmpty()) {
            $this->command->warn('No approved recipients found. Please run CampaignSeeder first.');

            return;
        }

        // Get campaigns that have collected some amount from izzati
        $campaigns = Campaign::on('izzati')->where('Collected_Amount', '>', 0)->get();

        if ($campaigns->isEmpty()) {
            $this->command->warn('No campaigns with donations found. Please run DonationSeeder first.');

            return;
        }

        $this->command->info("Found {$campaigns->count()} campaigns with collected donations");
        $this->command->info("Found {$approvedRecipients->count()} approved recipients");

        // Allocate funds from campaigns to recipients
        foreach ($campaigns as $campaign) {
            $this->allocateCampaignFunds($campaign, $approvedRecipients);
        }

        $this->displayAllocationStatistics();

        $this->command->info('✓ Donation allocations seeded successfully!');
    }

    private function allocateCampaignFunds($campaign, $approvedRecipients)
    {
        // Only allocate if campaign has collected significant amount (at least 10% of goal or minimum RM 1000)
        $collectedPercentage = ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100;

        if ($collectedPercentage < 10 && $campaign->Collected_Amount < 1000) {
            $this->command->info("Skipping '{$campaign->Title}' - only ".number_format($collectedPercentage, 1).'% funded (RM '.number_format($campaign->Collected_Amount, 2).')');

            return;
        }

        $this->command->info("Processing '{$campaign->Title}' - ".number_format($collectedPercentage, 1).'% funded (RM '.number_format($campaign->Collected_Amount, 2).')');

        // Decide how much to allocate (60-90% of collected amount)
        $allocationPercentage = rand(60, 90) / 100;
        $totalToAllocate = $campaign->Collected_Amount * $allocationPercentage;

        // Randomly select 2-4 recipients for this campaign
        $recipientCount = min(rand(2, 4), $approvedRecipients->count());
        $selectedRecipients = $approvedRecipients->random($recipientCount);

        $this->command->info('Allocating RM '.number_format($totalToAllocate, 2)." from '{$campaign->Title}' to {$recipientCount} recipients");

        // Calculate base allocation per recipient
        $baseAllocation = $totalToAllocate / $recipientCount;

        foreach ($selectedRecipients as $index => $recipient) {
            // Add some variance (-20% to +20%)
            $variance = rand(-20, 20) / 100;
            $allocation = $baseAllocation * (1 + $variance);

            // For the last recipient, allocate the remaining amount to ensure total matches
            if ($index === $recipientCount - 1) {
                $alreadyAllocated = DonationAllocation::on('hannah')
                    ->where('Campaign_ID', $campaign->Campaign_ID)
                    ->sum('Amount_Allocated');
                $allocation = $totalToAllocate - $alreadyAllocated;
            }

            // Ensure allocation is positive
            if ($allocation <= 0) {
                continue;
            }

            // Generate allocation date (after campaign started, within reasonable time)
            $allocationDate = $this->generateAllocationDate($campaign);

            try {
                // Check if allocation already exists in hannah
                $existingAllocation = DonationAllocation::on('hannah')
                    ->where('Recipient_ID', $recipient->Recipient_ID)
                    ->where('Campaign_ID', $campaign->Campaign_ID)
                    ->first();

                if ($existingAllocation) {
                    $this->command->warn("  ⚠ Allocation already exists for recipient '{$recipient->Name}' in campaign '{$campaign->Title}'");

                    continue;
                }

                // Create donation allocation in hannah database
                $donationAllocation = new DonationAllocation([
                    'Recipient_ID' => $recipient->Recipient_ID,
                    'Campaign_ID' => $campaign->Campaign_ID,
                    'Amount_Allocated' => round($allocation, 2),
                    'Allocated_At' => $allocationDate,
                    'created_at' => $allocationDate,
                    'updated_at' => $allocationDate,
                ]);
                $donationAllocation->setConnection('hannah');
                $donationAllocation->save();

                $this->command->info('  ✓ Allocated RM '.number_format($allocation, 2)." to '{$recipient->Name}'");
            } catch (\Exception $e) {
                $this->command->error("  ✗ Failed to allocate to '{$recipient->Name}': ".$e->getMessage());
            }
        }
    }

    private function generateAllocationDate($campaign)
    {
        $campaignStart = Carbon::parse($campaign->Start_Date);
        $now = Carbon::now();
        $campaignEnd = Carbon::parse($campaign->End_Date);

        $baseDate = null;

        // For completed campaigns, allocate shortly after end date
        if ($campaign->Status === 'Completed') {
            $baseDate = $campaignEnd->copy()->addDays(rand(1, 7));
        }
        // For active campaigns, allocate within the campaign period
        elseif ($now->greaterThan($campaignStart)) {
            $daysIntoActive = $campaignStart->diffInDays($now);
            if ($daysIntoActive > 14) {
                // If campaign has been running for more than 2 weeks, allocate recently
                $baseDate = $now->copy()->subDays(rand(1, 7));
            } else {
                // Allocate somewhere in the middle of the campaign so far
                $baseDate = $campaignStart->copy()->addDays(rand(7, $daysIntoActive));
            }
        }
        // Fallback
        else {
            $baseDate = $campaignStart->copy()->addDays(14);
        }

        // Add random hours and minutes to make it more realistic
        return $baseDate->addHours(rand(0, 23))->addMinutes(rand(0, 59));
    }

    private function displayAllocationStatistics()
    {
        $totalAllocations = DonationAllocation::on('hannah')->count();
        $totalAllocatedAmount = DonationAllocation::on('hannah')->sum('Amount_Allocated');

        $this->command->info('========================');
        $this->command->info('Allocation Statistics:');
        $this->command->info("Total Allocations: {$totalAllocations}");
        $this->command->info('Total Allocated Amount: RM '.number_format($totalAllocatedAmount, 2));
        $this->command->info('========================');

        // Show per campaign (from izzati, count allocations from hannah)
        $campaigns = Campaign::on('izzati')->get();
        foreach ($campaigns as $campaign) {
            $allocated = DonationAllocation::on('hannah')
                ->where('Campaign_ID', $campaign->Campaign_ID)
                ->sum('Amount_Allocated');

            if ($allocated > 0) {
                $collected = $campaign->Collected_Amount;
                $percentage = $collected > 0 ? ($allocated / $collected * 100) : 0;

                $allocationCount = DonationAllocation::on('hannah')
                    ->where('Campaign_ID', $campaign->Campaign_ID)
                    ->count();

                $this->command->info("Campaign: {$campaign->Title}");
                $this->command->info('  Allocated: RM '.number_format($allocated, 2).' / RM '.number_format($collected, 2).' ('.number_format($percentage, 1).'%)');
                $this->command->info("  Recipients: {$allocationCount}");
            }
        }

        // Show per recipient (from adam, count allocations from hannah)
        $this->command->info('========================');
        $this->command->info('Recipient Allocations:');
        $recipients = Recipient::on('adam')->where('Status', 'Approved')->get();
        foreach ($recipients as $recipient) {
            $received = DonationAllocation::on('hannah')
                ->where('Recipient_ID', $recipient->Recipient_ID)
                ->sum('Amount_Allocated');

            if ($received > 0) {
                $campaignCount = DonationAllocation::on('hannah')
                    ->where('Recipient_ID', $recipient->Recipient_ID)
                    ->count();

                $this->command->info("Recipient: {$recipient->Name}");
                $this->command->info('  Total Received: RM '.number_format($received, 2)." from {$campaignCount} campaign(s)");
            }
        }
    }
}

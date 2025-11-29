<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Campaign;
use Carbon\Carbon;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all donors
        $donors = Donor::all();

        if ($donors->isEmpty()) {
            $this->command->warn('No donors found. Please run UserRoleSeeder first.');
            return;
        }

        // Get all campaigns (only active or completed)
        $campaigns = Campaign::whereIn('Status', ['Active', 'Completed'])->get();

        if ($campaigns->isEmpty()) {
            $this->command->warn('No campaigns found. Please run CampaignSeeder first.');
            return;
        }

        $this->command->info("Found {$campaigns->count()} campaigns to receive donations");

        // Create donations for each donor
        foreach ($donors as $donor) {
            $this->createDonationsForDonor($donor, $campaigns);
        }

        // Display final statistics
        $this->displayStatistics();

        $this->command->info('Donations seeded successfully!');
    }

    private function createDonationsForDonor($donor, $campaigns)
    {
        // Each donor makes 8-15 random donations
        $donationCount = rand(8, 15);

        // Randomly select campaigns (can donate to same campaign multiple times)
        $totalDonated = 0;

        for ($i = 0; $i < $donationCount; $i++) {
            $campaign = $campaigns->random();

            // Generate random donation amounts
            $amount = $this->generateDonationAmount();

            // Generate random dates within the campaign period
            $donationDate = $this->generateDonationDate($campaign);

            // Generate receipt number
            $receiptNo = $this->generateReceiptNumber($donor, $donationDate, $i);

            // Random payment methods
            $paymentMethods = ['Credit Card', 'Online Banking', 'E-Wallet', 'Debit Card'];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            $donation = Donation::create([
                'Donor_ID' => $donor->Donor_ID,
                'Campaign_ID' => $campaign->Campaign_ID,
                'Amount' => $amount,
                'Donation_Date' => $donationDate,
                'Payment_Method' => $paymentMethod,
                'Receipt_No' => $receiptNo,
                'created_at' => $donationDate,
                'updated_at' => $donationDate,
            ]);

            $totalDonated += $amount;

            // Update campaign collected amount immediately
            $campaign->increment('Collected_Amount', $amount);

            $this->command->info("Created donation: RM {$amount} to '{$campaign->Title}' (ID: {$campaign->Campaign_ID})");
        }

        // Update donor's total donated amount
        $donor->update(['Total_Donated' => $totalDonated]);

        $this->command->info("Donor '{$donor->Full_Name}' total donated: RM {$totalDonated}");
    }

    private function generateDonationAmount()
    {
        // Generate realistic donation amounts with better distribution
        $amounts = [
            50, 50, 100, 100, 100, 150, 200, 200, 250, 300,
            500, 500, 750, 1000, 1000, 1500, 2000, 2500, 5000
        ];

        return $amounts[array_rand($amounts)];
    }

    private function generateDonationDate($campaign)
    {
        $startDate = Carbon::parse($campaign->Start_Date);
        $endDate = Carbon::parse($campaign->End_Date);
        $now = Carbon::now();

        // For completed campaigns, generate dates within the campaign period
        if ($campaign->Status === 'Completed') {
            $daysDiff = $startDate->diffInDays($endDate);
            if ($daysDiff < 1) $daysDiff = 1;
            return $startDate->copy()->addDays(rand(0, $daysDiff));
        }

        // For active campaigns, generate dates from start to now
        if ($now->greaterThan($startDate)) {
            $daysDiff = $startDate->diffInDays($now);
            if ($daysDiff < 1) $daysDiff = 1;
            return $startDate->copy()->addDays(rand(0, $daysDiff));
        }

        // If campaign hasn't started yet (shouldn't happen), use start date
        return $startDate;
    }

    private function generateReceiptNumber($donor, $donationDate, $sequence)
    {
        // Format: RCP-YYYYMMDD-DONOR_ID-SEQUENCE
        $date = Carbon::parse($donationDate)->format('Ymd');
        $seq = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return "RCP-{$date}-D{$donor->Donor_ID}-{$seq}";
    }

    private function displayStatistics()
    {
        $totalDonations = Donation::count();
        $totalAmount = Donation::sum('Amount');

        $this->command->info("========================");
        $this->command->info("Donation Statistics:");
        $this->command->info("Total Donations: {$totalDonations}");
        $this->command->info("Total Amount: RM " . number_format($totalAmount, 2));
        $this->command->info("========================");

        // Show per campaign
        $campaigns = Campaign::with('donations')->get();
        foreach ($campaigns as $campaign) {
            $collected = $campaign->Collected_Amount;
            $goal = $campaign->Goal_Amount;
            $percentage = $goal > 0 ? ($collected / $goal * 100) : 0;

            $this->command->info("Campaign: {$campaign->Title}");
            $this->command->info("  Collected: RM " . number_format($collected, 2) . " / RM " . number_format($goal, 2) . " ({$percentage}%)");
            $this->command->info("  Donations count: {$campaign->donations->count()}");
        }
    }
}

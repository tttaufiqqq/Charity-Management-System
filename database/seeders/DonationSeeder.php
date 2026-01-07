<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Databases:
     * - hannah (MySQL): Donors, Donations
     * - izzati (PostgreSQL): Campaigns (cross-database updates)
     */
    public function run(): void
    {
        // Get all donors from hannah
        $donors = Donor::on('hannah')->get();

        if ($donors->isEmpty()) {
            $this->command->warn('No donors found. Please run UserRoleSeeder first.');

            return;
        }

        // Get all campaigns (only active or completed) from izzati
        $campaigns = Campaign::on('izzati')->whereIn('Status', ['Active', 'Completed'])->get();

        if ($campaigns->isEmpty()) {
            $this->command->warn('No campaigns found. Please run CampaignSeeder first.');

            return;
        }

        $this->command->info("Found {$donors->count()} donors and {$campaigns->count()} campaigns");

        // Create donations for each donor
        foreach ($donors as $donor) {
            $this->createDonationsForDonor($donor, $campaigns);
        }

        // Display final statistics
        $this->displayStatistics();

        $this->command->info('✓ Donations seeded successfully!');
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

            // Create donation in hannah database
            $donation = new Donation([
                'Donor_ID' => $donor->Donor_ID,
                'Campaign_ID' => $campaign->Campaign_ID,
                'Amount' => $amount,
                'Donation_Date' => $donationDate,
                'Payment_Method' => $paymentMethod,
                'Receipt_No' => $receiptNo,
                'Payment_Status' => 'Completed', // ✅ All seeded donations are successful
                'Bill_Code' => 'SEED-'.strtoupper(uniqid()), // Seeded bill code
                'Transaction_ID' => 'TXN-'.strtoupper(uniqid()), // Seeded transaction
                'created_at' => $donationDate,
                'updated_at' => $donationDate,
            ]);
            $donation->setConnection('hannah');
            $donation->save();

            $totalDonated += $amount;

            // Update campaign collected amount immediately (cross-database update to izzati)
            $campaign->increment('Collected_Amount', $amount);

            $this->command->info("Created donation: RM {$amount} to '{$campaign->Title}' (ID: {$campaign->Campaign_ID})");
        }

        // Update donor's total donated amount (in hannah)
        $donor->update(['Total_Donated' => $totalDonated]);

        $this->command->info("Donor '{$donor->Full_Name}' total donated: RM {$totalDonated}");
    }

    private function generateDonationAmount()
    {
        // Generate realistic donation amounts with better distribution
        $amounts = [
            50, 50, 100, 100, 100, 150, 200, 200, 250, 300,
            500, 500, 750, 1000, 1000, 1500, 2000, 2500, 5000,
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
            if ($daysDiff < 1) {
                $daysDiff = 1;
            }

            return $startDate->copy()->addDays(rand(0, $daysDiff));
        }

        // For active campaigns, generate dates from start to now
        if ($now->greaterThan($startDate)) {
            $daysDiff = $startDate->diffInDays($now);
            if ($daysDiff < 1) {
                $daysDiff = 1;
            }

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
        $totalDonations = Donation::on('hannah')->count();
        $totalAmount = Donation::on('hannah')->sum('Amount');

        $this->command->info('========================');
        $this->command->info('Donation Statistics:');
        $this->command->info("Total Donations: {$totalDonations}");
        $this->command->info('Total Amount: RM '.number_format($totalAmount, 2));
        $this->command->info('========================');

        // Show per campaign (from izzati, count donations from hannah)
        $campaigns = Campaign::on('izzati')->get();
        foreach ($campaigns as $campaign) {
            $collected = $campaign->Collected_Amount;
            $goal = $campaign->Goal_Amount;
            $percentage = $goal > 0 ? ($collected / $goal * 100) : 0;

            // Count donations for this campaign from hannah
            $donationCount = Donation::on('hannah')->where('Campaign_ID', $campaign->Campaign_ID)->count();

            if ($donationCount > 0) {
                $this->command->info("Campaign: {$campaign->Title}");
                $this->command->info('  Collected: RM '.number_format($collected, 2).' / RM '.number_format($goal, 2).' ('.number_format($percentage, 1).'%)');
                $this->command->info("  Donations count: {$donationCount}");
            }
        }
    }
}

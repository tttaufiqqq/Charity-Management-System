<?php

namespace App\Console\Commands;

use App\Models\Donation;
use Illuminate\Console\Command;

class CleanPendingDonations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donations:clean-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all pending donations from the database (old records that never completed payment)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up pending donations...');

        // Find all pending donations
        $pendingDonations = Donation::where('Payment_Status', 'Pending')->get();

        $count = $pendingDonations->count();

        if ($count === 0) {
            $this->info('No pending donations found.');

            return 0;
        }

        $this->warn("Found {$count} pending donation(s).");

        if (! $this->confirm('Do you want to delete these records?', true)) {
            $this->info('Cancelled.');

            return 0;
        }

        // Delete all pending donations
        $deleted = Donation::where('Payment_Status', 'Pending')->delete();

        $this->info("Successfully deleted {$deleted} pending donation(s).");
        $this->info('Database now only contains Completed and Failed donations.');

        return 0;
    }
}

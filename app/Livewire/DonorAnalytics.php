<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Donor;
use App\Models\Donation;

class DonorAnalytics extends Component
{
    public $topDonors;
    public $donationsByMethod;
    public $averageDonation;
    public $totalDonors;
    public $activeDonors;

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // Top donors - using orderByDesc for better readability
        $this->topDonors = Donor::orderByDesc('Total_Donated')
            ->limit(10)
            ->get();

        // Donations by payment method - using Laravel's query builder
        $donationGroups = Donation::select('Payment_Method')
            ->groupBy('Payment_Method')
            ->get()
            ->countBy('Payment_Method')
            ->toArray();

        $this->donationsByMethod = $donationGroups;

        // Statistics
        $this->totalDonors = Donor::count();

        // Active donors - using where clause that works across all RDBMS
        $this->activeDonors = Donor::where('Total_Donated', '>', 0)->count();

        // Average donation - using Laravel's avg() method which handles null and cross-database compatibility
        $this->averageDonation = Donation::avg('Amount') ?? 0;
    }

    public function render()
    {
        return view('livewire.donor-analytics');
    }
}

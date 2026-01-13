<?php

namespace App\Livewire;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Views\DonorDonationSummary;
use Livewire\Component;

class DonorAnalytics extends Component
{
    public $topDonors;

    public $donationsByMethod;

    public $averageDonation;

    public $totalDonors;

    public $activeDonors;

    public $donorTierBreakdown;

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // Top donors - Using vw_donor_donation_summary view (hannah database)
        $this->topDonors = DonorDonationSummary::topDonors(10)->get();

        // Donations by payment method - using Laravel's query builder
        $donationGroups = Donation::select('Payment_Method')
            ->groupBy('Payment_Method')
            ->get()
            ->countBy('Payment_Method')
            ->toArray();

        $this->donationsByMethod = $donationGroups;

        // Statistics from view
        $this->totalDonors = DonorDonationSummary::count();

        // Active donors - using view's active scope
        $this->activeDonors = DonorDonationSummary::active()->count();

        // Average donation from view
        $this->averageDonation = DonorDonationSummary::avg('avg_donation_amount') ?? 0;

        // Donor tier breakdown - new metric from view
        $this->donorTierBreakdown = DonorDonationSummary::selectRaw('donor_tier, COUNT(*) as count')
            ->groupBy('donor_tier')
            ->orderByRaw("FIELD(donor_tier, 'Platinum', 'Gold', 'Silver', 'Bronze', 'Supporter')")
            ->get()
            ->pluck('count', 'donor_tier')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.donor-analytics');
    }
}

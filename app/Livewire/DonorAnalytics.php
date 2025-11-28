<?php

namespace App\Livewire;

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Donor;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;

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
        // Top donors
        $this->topDonors = Donor::orderBy('Total_Donated', 'desc')
            ->limit(10)
            ->get();

        // Donations by payment method
        $this->donationsByMethod = Donation::select('Payment_Method', DB::raw('COUNT(*) as count'))
            ->groupBy('Payment_Method')
            ->get()
            ->pluck('count', 'Payment_Method')
            ->toArray();

        // Statistics
        $this->totalDonors = Donor::count();
        $this->activeDonors = Donor::where('Total_Donated', '>', 0)->count();
        $this->averageDonation = Donation::avg('Amount') ?? 0;
    }

    public function render()
    {
        return view('livewire.donor-analytics');
    }
}

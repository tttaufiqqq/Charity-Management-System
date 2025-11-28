<?php

// ============================================================================
// File: app/Http/Livewire/CampaignAnalytics.php
// ============================================================================

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;

class CampaignAnalytics extends Component
{
    public $topCampaigns;
    public $campaignsByStatus;
    public $averageProgress;
    public $totalGoal;
    public $totalCollected;

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // Top campaigns by amount raised
        $this->topCampaigns = Campaign::orderBy('Collected_Amount', 'desc')
            ->limit(10)
            ->get();

        // Campaigns by status
        $this->campaignsByStatus = Campaign::select('Status', DB::raw('COUNT(*) as count'))
            ->groupBy('Status')
            ->get()
            ->pluck('count', 'Status')
            ->toArray();

        // Financial summary
        $this->totalGoal = Campaign::sum('Goal_Amount');
        $this->totalCollected = Campaign::sum('Collected_Amount');
        $this->averageProgress = $this->totalGoal > 0
            ? ($this->totalCollected / $this->totalGoal) * 100
            : 0;
    }

    public function render()
    {
        return view('livewire.campaign-analytics');
    }
}

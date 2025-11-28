<?php

// ============================================================================
// File: app/Http/Livewire/CampaignAnalytics.php
// ============================================================================

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campaign;

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
        // Top campaigns by amount raised - using orderByDesc for better readability
        $this->topCampaigns = Campaign::orderByDesc('Collected_Amount')
            ->limit(10)
            ->get();

        // Campaigns by status - using Laravel's query builder
        $campaignGroups = Campaign::select('Status')
            ->groupBy('Status')
            ->get()
            ->countBy('Status')
            ->toArray();

        $this->campaignsByStatus = $campaignGroups;

        // Financial summary - using Laravel's sum() method which handles cross-database compatibility
        $this->totalGoal = Campaign::sum('Goal_Amount') ?? 0;
        $this->totalCollected = Campaign::sum('Collected_Amount') ?? 0;

        // Calculate average progress
        $this->averageProgress = $this->totalGoal > 0
            ? ($this->totalCollected / $this->totalGoal) * 100
            : 0;
    }

    public function render()
    {
        return view('livewire.campaign-analytics');
    }
}

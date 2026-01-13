<?php

// ============================================================================
// File: app/Http/Livewire/CampaignAnalytics.php
// ============================================================================

namespace App\Livewire;

use App\Models\Views\CampaignProgress;
use Livewire\Component;

class CampaignAnalytics extends Component
{
    public $topCampaigns;

    public $campaignsByStatus;

    public $averageProgress;

    public $totalGoal;

    public $totalCollected;

    public $fundingStatusBreakdown;

    public $campaignsNeedingAttention;

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // Top campaigns - Using vw_campaign_progress view (izzati database)
        $this->topCampaigns = CampaignProgress::topPerforming(10)->get();

        // Campaigns by status - using view
        $this->campaignsByStatus = CampaignProgress::selectRaw('campaign_status, COUNT(*) as count')
            ->groupBy('campaign_status')
            ->get()
            ->pluck('count', 'campaign_status')
            ->toArray();

        // Financial summary - using view's aggregated data
        $this->totalGoal = CampaignProgress::sum('Goal_Amount') ?? 0;
        $this->totalCollected = CampaignProgress::sum('Collected_Amount') ?? 0;

        // Calculate average progress from view
        $this->averageProgress = CampaignProgress::avg('progress_percentage') ?? 0;

        // Funding status breakdown - new metric from view
        $this->fundingStatusBreakdown = CampaignProgress::selectRaw('funding_status, COUNT(*) as count')
            ->groupBy('funding_status')
            ->get()
            ->pluck('count', 'funding_status')
            ->toArray();

        // Campaigns needing attention (low progress, ending soon)
        $this->campaignsNeedingAttention = CampaignProgress::needingAttention()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.campaign-analytics');
    }
}

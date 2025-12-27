<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use App\Services\Api\OrganizationApiService;
use App\Services\Api\UserApiService;
use Illuminate\Http\Request;

class CampaignApiController extends Controller
{
    protected $organizationService;

    protected $userService;

    public function __construct(OrganizationApiService $organizationService, UserApiService $userService)
    {
        $this->organizationService = $organizationService;
        $this->userService = $userService;
    }

    /**
     * Get all campaigns
     */
    public function index(Request $request)
    {
        $query = Campaign::query();

        if ($request->has('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->has('organization_id')) {
            $query->where('Organization_ID', $request->organization_id);
        }

        $campaigns = $query->with('organization')->get();

        return response()->json($campaigns);
    }

    /**
     * Get single campaign
     */
    public function show($id)
    {
        $campaign = Campaign::with('organization')->findOrFail($id);

        return response()->json($campaign);
    }

    /**
     * Create a new campaign
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Organization_ID' => 'required|integer',
            'Title' => 'required|string|max:255',
            'Description' => 'required|string',
            'Goal_Amount' => 'required|numeric|min:0',
            'Start_Date' => 'required|date',
            'End_Date' => 'required|date|after:Start_Date',
        ]);

        // Validate organization exists via API
        if (! $this->organizationService->exists($validated['Organization_ID'])) {
            return response()->json(['error' => 'Organization not found'], 404);
        }

        $campaign = Campaign::create(array_merge($validated, [
            'Collected_Amount' => 0,
            'Status' => 'Pending', // Requires admin approval
        ]));

        return response()->json($campaign, 201);
    }

    /**
     * Update campaign
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'Title' => 'sometimes|string|max:255',
            'Description' => 'sometimes|string',
            'Goal_Amount' => 'sometimes|numeric|min:0',
            'Start_Date' => 'sometimes|date',
            'End_Date' => 'sometimes|date|after:Start_Date',
            'Status' => 'sometimes|in:Active,Completed,Pending',
        ]);

        $campaign->update($validated);

        return response()->json($campaign);
    }

    /**
     * Update campaign collected amount (called from Donation service)
     */
    public function updateCollectedAmount(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $campaign->increment('Collected_Amount', $validated['amount']);

        return response()->json($campaign);
    }

    /**
     * Sync campaign collected amount (for reconciliation)
     */
    public function syncCollectedAmount(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
        ]);

        $campaign->update(['Collected_Amount' => $validated['total_amount']]);

        return response()->json($campaign);
    }

    /**
     * Get available funds for allocation
     */
    public function getAvailableFunds($id)
    {
        $campaign = Campaign::findOrFail($id);

        // Query donation allocations from the same database
        $allocated = $campaign->donationAllocations()->sum('Amount_Allocated');
        $available = $campaign->Collected_Amount - $allocated;

        return response()->json(['available_funds' => max(0, $available)]);
    }

    /**
     * Approve campaign
     */
    public function approve($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update(['Status' => 'Active']);

        return response()->json($campaign);
    }

    /**
     * Reject campaign
     */
    public function reject(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $campaign->update(['Status' => 'Rejected']);

        return response()->json($campaign);
    }
}

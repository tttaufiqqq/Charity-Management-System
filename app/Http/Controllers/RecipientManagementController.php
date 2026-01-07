<?php

// ================================
// RecipientManagementController.php
// ================================

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignRecipientSuggestion;
use App\Models\DonationAllocation;
use App\Models\Recipient;
use App\Traits\ValidatesCrossDatabaseReferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecipientManagementController extends Controller
{
    use ValidatesCrossDatabaseReferences;

    /**
     * Show list of admin-suggested recipients for allocation
     */
    public function showRecipients($campaignId)
    {
        $campaign = Campaign::with('organization')->findOrFail($campaignId);
        if (Auth::user()->organization->Organization_ID !== $campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Get only recipients suggested by admin (Pending or Accepted, but not Rejected) - cross-database safe
        // Step 1: Get recipient IDs from campaign_recipient_suggestions (izzati)
        $recipientIds = CampaignRecipientSuggestion::where('Campaign_ID', $campaignId)
            ->whereIn('Status', ['Pending', 'Accepted'])
            ->pluck('Recipient_ID')
            ->toArray();

        // Step 2: Query recipients from adam database
        $recipients = ! empty($recipientIds)
            ? Recipient::where('Status', 'Approved')
                ->whereIn('Recipient_ID', $recipientIds)
                ->with(['donationAllocations' => function ($query) use ($campaignId) {
                    $query->where('Campaign_ID', $campaignId);
                }])
                ->paginate(10)
            : new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                10,
                1,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

        // Calculate total allocated for this campaign
        $totalAllocated = DonationAllocation::where('Campaign_ID', $campaignId)
            ->sum('Amount_Allocated');

        $remainingAmount = $campaign->Collected_Amount - $totalAllocated;

        return view('recipient-management.allocate', compact('campaign', 'recipients', 'totalAllocated', 'remainingAmount'));
    }

    /**
     * Allocate funds to a recipient
     */
    public function allocateFunds(Request $request, $campaignId)
    {
        $request->validate([
            'recipient_id' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        // Cross-database validation: hannah -> izzati (Campaign) and hannah -> adam (Recipient)
        $campaign = $this->validateCampaignExists($campaignId);
        $recipient = $this->validateRecipientIsApproved($request->recipient_id);

        // Verify user is the organizer
        if (Auth::user()->organization->Organization_ID !== $campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Validate campaign has sufficient funds for allocation (cross-database validation)
        $this->validateCampaignHasSufficientFunds($campaignId, $request->amount);

        DB::beginTransaction();
        try {
            // Check if allocation already exists
            $allocation = DonationAllocation::where('Recipient_ID', $request->recipient_id)
                ->where('Campaign_ID', $campaignId)
                ->first();

            if ($allocation) {
                // Update existing allocation
                $allocation->increment('Amount_Allocated', $request->amount);
                $allocation->update(['Allocated_At' => now()]);
            } else {
                // Create new allocation
                DonationAllocation::create([
                    'Recipient_ID' => $request->recipient_id,
                    'Campaign_ID' => $campaignId,
                    'Amount_Allocated' => $request->amount,
                    'Allocated_At' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('recipients.allocate', $campaignId)
                ->with('success', 'Funds allocated successfully! (Database: Hannah)');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Allocation failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Allocation failed: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * View allocation history for a campaign
     */
    public function allocationHistory($campaignId)
    {
        $campaign = Campaign::with('organization')->findOrFail($campaignId);

        // Verify user is the organizer
        if (Auth::user()->organization->Organization_ID !== $campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $allocations = DonationAllocation::where('Campaign_ID', $campaignId)
            ->with('recipient')
            ->orderBy('Allocated_At', 'desc')
            ->paginate(15);

        $totalAllocated = $allocations->sum('Amount_Allocated');
        $remainingAmount = $campaign->Collected_Amount - $totalAllocated;

        return view('recipient-management.history', compact('campaign', 'allocations', 'totalAllocated', 'remainingAmount'));
    }

    /**
     * View recipient's received allocations (for public users who registered the recipient)
     */
    public function recipientAllocations($recipientId)
    {
        $recipient = Recipient::with('publicProfile.user')->findOrFail($recipientId);

        // Verify user registered this recipient
        if (! Auth::user()->publicProfile || Auth::user()->publicProfile->Public_ID !== $recipient->Public_ID) {
            abort(403, 'Unauthorized action.');
        }

        $allocations = $recipient->donationAllocations()
            ->with('campaign.organization')
            ->orderBy('Allocated_At', 'desc')
            ->paginate(10);

        $totalReceived = $allocations->sum('Amount_Allocated');

        return view('recipient-management.received', compact('recipient', 'allocations', 'totalReceived'));
    }

    /**
     * View all allocations across all campaigns for organizer
     */
    public function allAllocations()
    {
        $organization = Auth::user()->organization;

        if (! $organization) {
            return redirect()->route('dashboard')->with('error', 'Organization profile not found. (Database: Izzati)');
        }

        // Get all campaigns belonging to this organizer
        $campaigns = Campaign::where('Organization_ID', $organization->Organization_ID)->get();
        $campaignIds = $campaigns->pluck('Campaign_ID');

        // Get all allocations for these campaigns
        $allocations = DonationAllocation::whereIn('Campaign_ID', $campaignIds)
            ->with(['recipient', 'campaign'])
            ->orderBy('Allocated_At', 'desc')
            ->paginate(20);

        // Calculate statistics
        $totalAllocated = DonationAllocation::whereIn('Campaign_ID', $campaignIds)->sum('Amount_Allocated');
        $totalCollected = $campaigns->sum('Collected_Amount');
        $remainingAmount = $totalCollected - $totalAllocated;
        $allocationRate = $totalCollected > 0 ? ($totalAllocated / $totalCollected) * 100 : 0;

        return view('recipient-management.all-allocations', compact(
            'allocations',
            'campaigns',
            'totalAllocated',
            'totalCollected',
            'remainingAmount',
            'allocationRate'
        ));
    }

    /**
     * Remove/adjust allocation
     */
    public function removeAllocation(Request $request, $campaignId, $recipientId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        // Verify user is the organizer
        if (Auth::user()->organization->Organization_ID !== $campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $allocation = DonationAllocation::where('Campaign_ID', $campaignId)
            ->where('Recipient_ID', $recipientId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $allocation->delete();
            DB::commit();

            return redirect()->back()->with('success', 'Allocation removed successfully! (Database: Hannah)');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to remove allocation.');
        }
    }

    public function myCampaigns(Request $request)
    {
        $query = Auth::user()->organization->campaigns()->with('donationAllocations');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('Status', $request->status);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(6);

        return view('organizer.campaigns.index', compact('campaigns'));
    }

    /**
     * Show pending recipients for admin approval
     */
    public function pendingRecipients(Request $request)
    {
        $query = Recipient::with('publicProfile.user')->where('Status', 'Pending');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'ILIKE', "%{$search}%")
                    ->orWhere('Contact', 'ILIKE', "%{$search}%")
                    ->orWhere('Address', 'ILIKE', "%{$search}%");
            });
        }

        $recipients = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('recipient-management.admin.pending', compact('recipients'));
    }

    /**
     * Show all recipients (all statuses) for admin
     */
    public function allRecipients(Request $request)
    {
        $query = Recipient::with('publicProfile.user');

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('Status', $request->status);
        }

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'ILIKE', "%{$search}%")
                    ->orWhere('Contact', 'ILIKE', "%{$search}%")
                    ->orWhere('Address', 'ILIKE', "%{$search}%");
            });
        }

        $recipients = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => Recipient::count(),
            'pending' => Recipient::where('Status', 'Pending')->count(),
            'approved' => Recipient::where('Status', 'Approved')->count(),
            'rejected' => Recipient::where('Status', 'Rejected')->count(),
        ];

        return view('recipient-management.admin.all', compact('recipients', 'stats'));
    }

    /**
     * Show recipient detail for admin review
     */
    public function adminShowRecipient($id)
    {
        $recipient = Recipient::with(['publicProfile.user', 'donationAllocations.campaign.organization'])->findOrFail($id);

        return view('recipient-management.admin.show', compact('recipient'));
    }

    /**
     * Approve recipient
     */
    public function approveRecipient($id)
    {
        $recipient = Recipient::findOrFail($id);

        if ($recipient->Status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending recipients can be approved.');
        }

        $recipient->update(['Status' => 'Approved']);

        return redirect()->back()->with('success', 'Recipient approved successfully! (Database: Adam)');
    }

    /**
     * Reject recipient
     */
    public function rejectRecipient(Request $request, $id)
    {
        $recipient = Recipient::findOrFail($id);

        if ($recipient->Status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending recipients can be rejected.');
        }

        $recipient->update(['Status' => 'Rejected']);

        return redirect()->back()->with('success', 'Recipient rejected. (Database: Adam)');
    }

    /**
     * Update recipient status (for quick actions)
     */
    public function updateRecipientStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        $recipient = Recipient::findOrFail($id);
        $recipient->update(['Status' => $request->status]);

        return redirect()->back()->with('success', 'Recipient status updated to '.$request->status.' (Database: Adam)');
    }

    /**
     * Delete recipient (admin only)
     */
    public function adminDeleteRecipient($id)
    {
        $recipient = Recipient::findOrFail($id);

        // Check if recipient has allocations (cross-database safe - uses setConnection in model)
        if ($recipient->donationAllocations()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete recipient with existing allocations.');
        }

        $recipient->delete();

        return redirect()->route('admin.recipients.all')->with('success', 'Recipient deleted successfully. (Database: Adam)');
    }

    /**
     * Show campaigns for admin to suggest recipients (Task 3)
     */
    public function adminCampaignsForSuggestion(Request $request)
    {
        $query = Campaign::with('organization.user');

        // Filter by status - only active campaigns
        if ($request->has('status') && $request->status != '') {
            $query->where('Status', $request->status);
        } else {
            $query->where('Status', 'Active');
        }

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Title', 'ILIKE', "%{$search}%")
                    ->orWhere('Description', 'ILIKE', "%{$search}%");
            });
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('recipient-management.admin.campaigns-for-suggestion', compact('campaigns'));
    }

    /**
     * Show form to suggest recipients for a specific campaign (Task 3)
     */
    public function suggestRecipientsForCampaign($campaignId)
    {
        $campaign = Campaign::with(['organization.user', 'recipientSuggestions.recipient'])->findOrFail($campaignId);

        // Get approved recipients not yet suggested for this campaign
        $existingSuggestionIds = $campaign->recipientSuggestions->pluck('Recipient_ID')->toArray();
        $recipients = Recipient::where('Status', 'Approved')
            ->whereNotIn('Recipient_ID', $existingSuggestionIds)
            ->with('publicProfile.user')
            ->paginate(15);

        // Get existing suggestions for this campaign
        $suggestions = CampaignRecipientSuggestion::where('Campaign_ID', $campaignId)
            ->with(['recipient', 'suggestedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('recipient-management.admin.suggest-recipients', compact('campaign', 'recipients', 'suggestions'));
    }

    /**
     * Store recipient suggestion for campaign (Task 3)
     */
    public function storeSuggestion(Request $request, $campaignId)
    {
        $request->validate([
            'recipient_id' => 'required|exists:recipient,Recipient_ID',
            'suggestion_reason' => 'nullable|string|max:1000',
        ]);

        $campaign = Campaign::findOrFail($campaignId);
        $recipient = Recipient::findOrFail($request->recipient_id);

        if ($recipient->Status !== 'Approved') {
            return redirect()->back()->with('error', 'Can only suggest approved recipients.');
        }

        // Check if suggestion already exists
        $exists = CampaignRecipientSuggestion::where('Campaign_ID', $campaignId)
            ->where('Recipient_ID', $request->recipient_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This recipient has already been suggested for this campaign.');
        }

        CampaignRecipientSuggestion::create([
            'Campaign_ID' => $campaignId,
            'Recipient_ID' => $request->recipient_id,
            'Suggested_By' => Auth::id(),
            'Suggestion_Reason' => $request->suggestion_reason,
            'Status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Recipient suggested successfully! (Database: Izzati)');
    }

    /**
     * Show suggested recipients for organizer's campaign
     */
    public function viewSuggestionsForCampaign($campaignId)
    {
        $campaign = Campaign::with('organization')->findOrFail($campaignId);

        // Verify user is the organizer
        if (Auth::user()->organization->Organization_ID !== $campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $suggestions = CampaignRecipientSuggestion::where('Campaign_ID', $campaignId)
            ->with(['recipient.publicProfile.user', 'suggestedBy'])
            ->orderBy('Status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('recipient-management.organizer.suggestions', compact('campaign', 'suggestions'));
    }

    /**
     * Accept a recipient suggestion
     */
    public function acceptSuggestion($suggestionId)
    {
        $suggestion = CampaignRecipientSuggestion::with('campaign')->findOrFail($suggestionId);

        // Verify user is the organizer
        if (Auth::user()->organization->Organization_ID !== $suggestion->campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        if ($suggestion->Status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending suggestions can be accepted.');
        }

        $suggestion->update(['Status' => 'Accepted']);

        // Smart redirect: Go to allocation page with recipient highlighted
        return redirect()
            ->route('recipients.allocate', $suggestion->Campaign_ID)
            ->with('success', 'Suggestion accepted! Recipient "'.($suggestion->recipient->Name ?? '').'" is now available for allocation. (Database: Izzati)')
            ->with('highlight_recipient', $suggestion->Recipient_ID);
    }

    /**
     * Reject a recipient suggestion
     */
    public function rejectSuggestion($suggestionId)
    {
        $suggestion = CampaignRecipientSuggestion::with('campaign')->findOrFail($suggestionId);

        // Verify user is the organizer
        if (Auth::user()->organization->Organization_ID !== $suggestion->campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        if ($suggestion->Status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending suggestions can be rejected.');
        }

        $suggestion->update(['Status' => 'Rejected']);

        return redirect()->back()->with('success', 'Suggestion rejected. (Database: Izzati)');
    }

    /**
     * Accept suggestion AND allocate funds in one action
     */
    public function acceptAndAllocateSuggestion(Request $request, $suggestionId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $suggestion = CampaignRecipientSuggestion::with('campaign')->findOrFail($suggestionId);

        // Verify organizer owns the campaign
        if (Auth::user()->organization->Organization_ID !== $suggestion->campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Verify status is pending
        if ($suggestion->Status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending suggestions can be accepted.');
        }

        // Verify recipient is approved
        $recipient = Recipient::findOrFail($suggestion->Recipient_ID);
        if ($recipient->Status !== 'Approved') {
            return redirect()->back()->with('error', 'Recipient must be approved before allocation.');
        }

        // Check available funds
        $totalAllocated = DonationAllocation::where('Campaign_ID', $suggestion->Campaign_ID)->sum('Amount_Allocated');
        $availableFunds = $suggestion->campaign->Collected_Amount - $totalAllocated;

        if ($request->amount > $availableFunds) {
            return redirect()->back()
                ->with('error', 'Insufficient funds. Available: RM '.number_format($availableFunds, 2))
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Accept the suggestion
            $suggestion->update(['Status' => 'Accepted']);

            // 2. Create or update allocation
            $allocation = DonationAllocation::where('Recipient_ID', $suggestion->Recipient_ID)
                ->where('Campaign_ID', $suggestion->Campaign_ID)
                ->first();

            if ($allocation) {
                // Update existing allocation
                $allocation->increment('Amount_Allocated', $request->amount);
                $allocation->update(['Allocated_At' => now()]);
            } else {
                // Create new allocation
                DonationAllocation::create([
                    'Recipient_ID' => $suggestion->Recipient_ID,
                    'Campaign_ID' => $suggestion->Campaign_ID,
                    'Amount_Allocated' => $request->amount,
                    'Allocated_At' => now(),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success',
                'Suggestion accepted and RM '.number_format($request->amount, 2).' allocated successfully! 🎉 (Databases: Izzati + Hannah)');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to process: '.$e->getMessage());
        }
    }

    /**
     * Admin report: View each campaign's recipients (Task 4)
     */
    public function adminCampaignRecipientsReport(Request $request)
    {
        $query = Campaign::with(['organization.user']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('Status', $request->status);
        }

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Title', 'ILIKE', "%{$search}%");
            });
        }

        $campaigns = $query->withCount('donationAllocations')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('recipient-management.admin.campaign-recipients-report', compact('campaigns'));
    }

    /**
     * Admin view: View specific campaign's recipients detail (Task 4)
     */
    public function adminViewCampaignRecipients($campaignId)
    {
        $campaign = Campaign::with('organization.user')->findOrFail($campaignId);

        $allocations = DonationAllocation::where('Campaign_ID', $campaignId)
            ->with('recipient.publicProfile.user')
            ->orderBy('Allocated_At', 'desc')
            ->paginate(15);

        $totalAllocated = $allocations->sum('Amount_Allocated');
        $remainingAmount = $campaign->Collected_Amount - $totalAllocated;

        // Get statistics
        $stats = [
            'total_recipients' => $allocations->unique('Recipient_ID')->count(),
            'total_allocated' => $totalAllocated,
            'total_collected' => $campaign->Collected_Amount,
            'remaining_amount' => $remainingAmount,
            'allocation_percentage' => $campaign->Collected_Amount > 0
                ? ($totalAllocated / $campaign->Collected_Amount) * 100
                : 0,
        ];

        return view('recipient-management.admin.campaign-recipients-detail', compact('campaign', 'allocations', 'stats'));
    }

    /**
     * Show form for admin to register a new recipient
     */
    public function adminCreateRecipient()
    {
        return view('recipient-management.admin.create');
    }

    /**
     * Store new recipient created by admin (auto-approved)
     */
    public function adminStoreRecipient(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'contact' => ['required', 'string', 'max:20'],
            'need_description' => ['required', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $recipient = Recipient::create([
                'Public_ID' => null, // Admin-registered recipients don't need public profile
                'Name' => $validated['name'],
                'Address' => $validated['address'],
                'Contact' => $validated['contact'],
                'Need_Description' => $validated['need_description'],
                'Status' => 'Approved', // Admin registrations are auto-approved
                'Approved_At' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.recipients.show', $recipient->Recipient_ID)
                ->with('success', 'Recipient registered and approved successfully! (Database: Adam)');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to register recipient: '.$e->getMessage());
        }
    }
}

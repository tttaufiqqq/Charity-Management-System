<?php

// ================================
// RecipientManagementController.php
// ================================

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\DonationAllocation;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecipientManagementController extends Controller
{
    /**
     * Show list of approved recipients for allocation
     */
    public function showRecipients($campaignId)
    {
        $campaign = Campaign::with('organization')->findOrFail($campaignId);
        if (Auth::user()->organization->Organization_ID !== $campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Get approved recipients
        $recipients = Recipient::where('Status', 'Approved')
            ->with(['donationAllocations' => function ($query) use ($campaignId) {
                $query->where('Campaign_ID', $campaignId);
            }])
            ->paginate(10);

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
            'recipient_id' => 'required|exists:recipient,Recipient_ID',
            'amount' => 'required|numeric|min:1',
        ]);

        $campaign = Campaign::findOrFail($campaignId);

        // Verify user is the organizer
        if (Auth::user()->organization->Organization_ID !== $campaign->Organization_ID) {
            abort(403, 'Unauthorized action.');
        }

        $recipient = Recipient::findOrFail($request->recipient_id);

        // Check if recipient is approved
        if ($recipient->Status !== 'Approved') {
            return redirect()->back()->with('error', 'Can only allocate to approved recipients.');
        }

        // Calculate total already allocated
        $totalAllocated = DonationAllocation::where('Campaign_ID', $campaignId)
            ->sum('Amount_Allocated');

        $remainingAmount = $campaign->Collected_Amount - $totalAllocated;

        // Check if enough funds available
        if ($request->amount > $remainingAmount) {
            return redirect()->back()
                ->with('error', 'Insufficient funds. Available: RM '.number_format($remainingAmount, 2))
                ->withInput();
        }

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
                ->with('success', 'Funds allocated successfully!');

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
            return redirect()->route('dashboard')->with('error', 'Organization profile not found.');
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

            return redirect()->back()->with('success', 'Allocation removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to remove allocation.');
        }
    }

    public function myCampaigns(Request $request)
    {
        $query = Auth::user()->organization->campaigns()->with('allocations');

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

        return redirect()->back()->with('success', 'Recipient approved successfully!');
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

        return redirect()->back()->with('success', 'Recipient rejected.');
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

        return redirect()->back()->with('success', 'Recipient status updated to '.$request->status);
    }

    /**
     * Delete recipient (admin only)
     */
    public function adminDeleteRecipient($id)
    {
        $recipient = Recipient::findOrFail($id);

        // Check if recipient has allocations
        if ($recipient->allocations()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete recipient with existing allocations.');
        }

        $recipient->delete();

        return redirect()->route('admin.recipients.all')->with('success', 'Recipient deleted successfully.');
    }
}

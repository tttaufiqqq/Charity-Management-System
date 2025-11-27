<?php
// ================================
// DonationManagementController.php
// ================================

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\PublicProfile;
use App\Models\Event;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // You'll need: composer require barryvdh/laravel-dompdf

class DonationManagementController extends Controller
{
    /**
     * Browse all active campaigns
     */
    public function browseCampaigns(Request $request)
    {
        $query = Campaign::with('organization')
            ->where('Status', 'Active');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Title', 'ILIKE', "%{$search}%")
                    ->orWhere('Description', 'ILIKE', "%{$search}%");
            });
        }

        // Category filter (if you have categories)
        if ($request->has('category') && $request->category != '') {
            $query->where('Category', $request->category);
        }

        // Sort by
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'ending_soon':
                $query->orderBy('End_Date', 'asc');
                break;
            case 'most_funded':
                $query->orderBy('Collected_Amount', 'desc');
                break;
            case 'goal_amount':
                $query->orderBy('Goal_Amount', 'desc');
                break;
        }

        $campaigns = $query->paginate(9);

        return view('donation-management.browse-campaigns', compact('campaigns'));
    }

    /**
     * Show single campaign detail
     */
    public function showCampaign($id)
    {
        $campaign = Campaign::with(['organization', 'donations.donor.user'])
            ->findOrFail($id);

        // Calculate progress percentage
        $progressPercentage = $campaign->Goal_Amount > 0
            ? min(($campaign->Collected_Amount / $campaign->Goal_Amount) * 100, 100)
            : 0;

        // Get recent donations for this campaign
        $recentDonations = $campaign->donations()
            ->with('donor.user')
            ->orderBy('Donation_Date', 'desc')
            ->take(10)
            ->get();

        // Check if current user has donated
        $userDonated = false;
        if (Auth::check() && Auth::user()->donor) {
            $userDonated = $campaign->donations()
                ->where('Donor_ID', Auth::user()->donor->Donor_ID)
                ->exists();
        }

        return view('donation-management.campaign.show', compact(
            'campaign',
            'progressPercentage',
            'recentDonations',
            'userDonated'
        ));
    }

    /**
     * Show donation form for a campaign
     */
    public function showDonationForm($campaignId)
    {
        $campaign = Campaign::with('organization')->findOrFail($campaignId);

        // Check if campaign is active
        if ($campaign->Status !== 'Active') {
            return redirect()->back()->with('error', 'This campaign is not accepting donations.');
        }

        $donor = Auth::user()->donor;
        if (!$donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found.');
        }

        return view('donation-management.donate', compact('campaign', 'donor'));
    }

    /**
     * Process donation
     */
    public function processDonation(Request $request, $campaignId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:Credit Card,Debit Card,Online Banking,E-Wallet',
        ]);

        $campaign = Campaign::findOrFail($campaignId);
        $donor = Auth::user()->donor;

        if (!$donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found.');
        }

        if ($campaign->Status !== 'Active') {
            return redirect()->back()->with('error', 'This campaign is not accepting donations.')->withInput();
        }

        DB::beginTransaction();
        try {
            // Create donation record
            $donation = Donation::create([
                'Donor_ID' => $donor->Donor_ID,
                'Campaign_ID' => $campaign->Campaign_ID,
                'Amount' => $request->amount,
                'Donation_Date' => now(),
                'Payment_Method' => $request->payment_method,
                'Receipt_No' => 'RCP-' . strtoupper(uniqid()),
            ]);

            // Update campaign total collected
            $campaign->increment('Collected_Amount', $request->amount);

            // Update donor total donated
            $donor->increment('Total_Donated', $request->amount);

            DB::commit();

            return redirect()->route('donation.success', $donation->Donation_ID)
                ->with('success', 'Thank you for your donation!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the actual error for debugging
            \Log::error('Donation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Donation failed: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Show donation success page
     */
    public function donationSuccess($donationId)
    {
        $donation = Donation::with(['campaign', 'donor'])->findOrFail($donationId);

        // Verify this donation belongs to current user
        if ($donation->donor->User_ID !== Auth::id()) {
            abort(403);
        }

        return view('donation-management.success', compact('donation'));
    }

    /**
     * View donor's donation history
     */
    public function myDonations(Request $request)
    {
        $donor = Auth::user()->donor;

        if (!$donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found.');
        }

        $query = $donor->donations()
            ->with('campaign.organization')
            ->orderBy('Donation_Date', 'desc');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->where('Donation_Date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->where('Donation_Date', '<=', $request->to_date);
        }

        $donations = $query->paginate(10);

        // Calculate statistics
        $stats = [
            'total_donated' => $donor->Total_Donated,
            'total_donations' => $donor->donations()->count(),
            'campaigns_supported' => $donor->donations()->distinct('Campaign_ID')->count(),
            'average_donation' => $donor->donations()->avg('Amount') ?? 0,
        ];

        return view('donation-management.my-donations', compact('donations', 'stats', 'donor'));
    }

    /**
     * Download receipt for a specific donation
     */
    public function downloadReceipt($donationId)
    {
        $donation = Donation::with(['campaign.organization', 'donor.user'])
            ->findOrFail($donationId);

        // Verify this donation belongs to current user
        if ($donation->donor->User_ID !== Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('donation-management.receipt-pdf', compact('donation'));

        return $pdf->download('receipt-' . $donation->Receipt_No . '.pdf');
    }

    /**
     * Download all receipts as ZIP (optional)
     */
    public function downloadAllReceipts()
    {
        $donor = Auth::user()->donor;

        if (!$donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found.');
        }

        // Load donor with user relationship
        $donor->load('user');

        $donations = $donor->donations()
            ->with(['campaign.organization'])
            ->orderBy('Donation_Date', 'desc')
            ->get();

        if ($donations->isEmpty()) {
            return redirect()->back()->with('error', 'No donations found.');
        }

        // Create PDF for all donations with both donations and donor data
        $pdf = Pdf::loadView('donation-management.all-receipts-pdf', [
            'donations' => $donations,
            'donor' => $donor
        ]);

        return $pdf->download('all-receipts-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Browse campaigns (Public view)
     */
    public function publicBrowseCampaigns()
    {
        $campaigns = Campaign::where('Status', 'Active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('donation-management.public-user.campaign-browse', compact('campaigns'));
    }

    /**
     * Show campaign details (Public view)
     */
    public function publicShowCampaign(Campaign $campaign)
    {
        // Calculate progress
        $progress = $campaign->Goal_Amount > 0
            ? ($campaign->Collected_Amount / $campaign->Goal_Amount) * 100
            : 0;

        // Get recent donations
        $recentDonations = $campaign->donations()
            ->with('donor')
            ->orderBy('Donation_Date', 'desc')
            ->limit(5)
            ->get();

        return view('donation-management.public-user.campaign-detail', compact('campaign', 'progress', 'recentDonations'));
    }

    /**
     * Browse events (Public view)
     */
    public function publicBrowseEvents()
    {
        $events = Event::whereIn('Status', ['Upcoming', 'Ongoing'])
            ->orderBy('Start_Date', 'asc')
            ->paginate(12);

        return view('donation-management.public-user.event-browse', compact('events'));
    }

    /**
     * Show event details (Public view)
     */
    public function publicShowEvent(Event $event)
    {
        $volunteerCount = $event->volunteers()->count();
        $spotsLeft = $event->Capacity ? ($event->Capacity - $volunteerCount) : null;

        return view('donation-management.public-user.event-detail', compact('event', 'volunteerCount', 'spotsLeft'));
    }

    /**
     * Show recipient registration form
     */
    public function publicCreateRecipient()
    {
        $publicProfile = Auth::user()->publicProfile;

        if (!$publicProfile) {
            return redirect()->route('dashboard')->with('error', 'Public profile not found.');
        }

        return view('donation-management.public-user.recipient-create');
    }

    /**
     * Store new recipient
     */
    public function publicStoreRecipient(Request $request)
    {
        $publicProfile = Auth::user()->publicProfile;

        if (!$publicProfile) {
            return redirect()->route('dashboard')->with('error', 'Public profile not found.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'contact' => ['required', 'string', 'max:20'],
            'need_description' => ['required', 'string'],
        ]);

        DB::beginTransaction();

        try {
            Recipient::create([
                'Public_ID' => $publicProfile->Public_ID,
                'Name' => $validated['name'],
                'Address' => $validated['address'],
                'Contact' => $validated['contact'],
                'Need_Description' => $validated['need_description'],
                'Status' => 'Pending',
                'Approved_At' => null,
            ]);

            DB::commit();

            return redirect()
                ->route('public.recipients.index')
                ->with('success', 'Recipient registered successfully! Pending review.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to register recipient. Please try again.'])->withInput();
        }
    }

    /**
     * Show all recipients registered by this public user
     */
    public function publicIndexRecipients()
    {
        $publicProfile = Auth::user()->publicProfile;

        if (!$publicProfile) {
            return redirect()->route('dashboard')->with('error', 'Public profile not found.');
        }

        $recipients = Recipient::where('Public_ID', $publicProfile->Public_ID)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('donation-management.public-user.recipient-index', compact('recipients'));
    }

    /**
     * Show recipient details
     */
    public function publicShowRecipient(Recipient $recipient)
    {
        $publicProfile = Auth::user()->publicProfile;

        // Check if user owns this recipient


        // Get allocations for this recipient
        $allocations = $recipient->donationAllocations()
            ->with('campaign')
            ->orderBy('Allocated_At', 'desc')
            ->get();

        $totalAllocated = $allocations->sum('Amount_Allocated');

        return view('donation-management.public-user.recipient-detail', compact('recipient', 'allocations', 'totalAllocated'));
    }

    /**
     * Edit recipient
     */
    public function publicEditRecipient(Recipient $recipient)
    {
        $publicProfile = Auth::user()->publicProfile;

        // Check if user owns this recipient
        if ($recipient->Public_ID !== $publicProfile->Public_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Can only edit pending recipients
        if ($recipient->Status !== 'Pending') {
            return back()->with('error', 'Cannot edit recipients that are already approved or rejected.');
        }

        return view('donation-management.public-user.recipient-edit', compact('recipient'));
    }

    /**
     * Update recipient
     */
    public function publicUpdateRecipient(Request $request, Recipient $recipient)
    {
        $publicProfile = Auth::user()->publicProfile;

        // Check if user owns this recipient
        if ($recipient->Public_ID !== $publicProfile->Public_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Can only edit pending recipients
        if ($recipient->Status !== 'Pending') {
            return back()->with('error', 'Cannot edit recipients that are already approved or rejected.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'contact' => ['required', 'string', 'max:20'],
            'need_description' => ['required', 'string'],
        ]);

        $recipient->update([
            'Name' => $validated['name'],
            'Address' => $validated['address'],
            'Contact' => $validated['contact'],
            'Need_Description' => $validated['need_description'],
        ]);

        return redirect()
            ->route('public.recipients.show', $recipient->Recipient_ID)
            ->with('success', 'Recipient updated successfully!');
    }

    /**
     * Delete recipient
     */
    public function publicDestroyRecipient(Recipient $recipient)
    {
        $publicProfile = Auth::user()->publicProfile;

        // Check if user owns this recipient
        if ($recipient->Public_ID !== $publicProfile->Public_ID) {
            abort(403, 'Unauthorized action.');
        }

        // Can only delete pending recipients
        if ($recipient->Status !== 'Pending') {
            return back()->with('error', 'Cannot delete recipients that are already approved.');
        }

        $recipient->delete();

        return redirect()
            ->route('public.recipients.index')
            ->with('success', 'Recipient deleted successfully!');
    }


}

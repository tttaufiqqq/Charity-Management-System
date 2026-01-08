<?php

// ================================
// DonationManagementController.php
// ================================

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Recipient;
use App\Traits\ValidatesCrossDatabaseReferences;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // You'll need: composer require barryvdh/laravel-dompdf

class DonationManagementController extends Controller
{
    use ValidatesCrossDatabaseReferences;

    /**
     * Browse all active campaigns
     */
    public function browseCampaigns(Request $request)
    {
        $query = Campaign::with('organization.user')
            ->where('Status', 'Active');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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
        $campaign = Campaign::with('organization.user')->findOrFail($campaignId);

        // Check if campaign is active
        if ($campaign->Status !== 'Active') {
            return redirect()->back()->with('error', 'This campaign is not accepting donations.');
        }

        $donor = Auth::user()->donor;
        if (! $donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found. (Database: Hannah)');
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
            'payment_method' => 'required|in:FPX Online Banking',
        ]);

        // Validate campaign exists and is active (cross-database validation: hannah -> izzati)
        $campaign = $this->validateCampaignIsActive($campaignId);

        $donor = Auth::user()->donor;

        if (! $donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found. (Database: Hannah)');
        }

        // Check if donation would exceed campaign goal
        $remainingAmount = $campaign->Goal_Amount - $campaign->Collected_Amount;
        if ($remainingAmount <= 0) {
            return redirect()->back()
                ->with('error', 'This campaign has already reached its funding goal. Thank you for your interest!')
                ->withInput();
        }

        if ($request->amount > $remainingAmount) {
            return redirect()->back()
                ->with('error', sprintf(
                    'Your donation of RM %s exceeds the remaining amount needed (RM %s). Please adjust your donation amount or donate the remaining amount.',
                    number_format($request->amount, 2),
                    number_format($remainingAmount, 2)
                ))
                ->withInput();
        }

        try {
            // Generate unique reference for this donation attempt
            $receiptNo = 'RCP-'.strtoupper(uniqid());
            $donationRef = 'DON-'.time().'-'.$donor->Donor_ID;

            // Store donation data in session (don't create in database yet)
            session([
                'pending_donation' => [
                    'donation_ref' => $donationRef,
                    'donor_id' => $donor->Donor_ID,
                    'campaign_id' => $campaign->Campaign_ID,
                    'amount' => $request->amount,
                    'receipt_no' => $receiptNo,
                    'payment_method' => $request->payment_method,
                    'donation_date' => now()->toDateTimeString(),
                ],
            ]);

            // Initialize ToyyibPay service
            $toyyibpay = new \App\Services\ToyyibPayService;

            // Create bill for payment
            // ToyyibPay billName has a max length of 30 characters
            $billName = 'Donation to '.$campaign->Title;
            if (strlen($billName) > 30) {
                $billName = substr($billName, 0, 27).'...';
            }

            $billData = [
                'billName' => $billName,
                'billDescription' => 'Charity donation via CharityHub',
                'billAmount' => $request->amount,
                'billReturnUrl' => route('donation.payment.return', ['ref' => $donationRef]),
                'billCallbackUrl' => route('donation.payment.callback'),
                'billExternalReferenceNo' => $donationRef,
                'billTo' => $donor->Full_Name,
                'billEmail' => Auth::user()->email,
                'billPhone' => $donor->Phone_Num ?? '0123456789',
            ];

            $result = $toyyibpay->createBill($billData);

            if ($result['success']) {
                // Store bill code in session
                session(['pending_donation.bill_code' => $result['billCode']]);

                // Redirect to ToyyibPay payment page (sandbox or production)
                return redirect()->away($result['paymentUrl']);
            } else {
                return redirect()->back()
                    ->with('error', 'Payment gateway error: '.$result['message'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Donation failed: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Donation failed: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show donation success page
     */
    public function donationSuccess($donationId)
    {
        $donation = Donation::with(['campaign.organization.user', 'donor'])->findOrFail($donationId);

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

        if (! $donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found. (Database: Hannah)');
        }

        // Show all donations (Completed and Failed - no Pending exists anymore)
        $query = $donor->donations()
            ->with('campaign.organization.user')
            ->whereIn('Payment_Status', ['Completed', 'Failed']) // Show both Completed and Failed
            ->orderBy('Donation_Date', 'desc');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->where('Donation_Date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->where('Donation_Date', '<=', $request->to_date);
        }

        $donations = $query->paginate(10);

        // Calculate statistics (only for completed donations)
        $stats = [
            'total_donated' => $donor->Total_Donated,
            'total_donations' => $donor->donations()->where('Payment_Status', 'Completed')->count(),
            'total_failed' => $donor->donations()->where('Payment_Status', 'Failed')->count(),
            'campaigns_supported' => $donor->donations()->where('Payment_Status', 'Completed')->distinct('Campaign_ID')->count(),
            'average_donation' => $donor->donations()->where('Payment_Status', 'Completed')->avg('Amount') ?? 0,
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

        return $pdf->download('receipt-'.$donation->Receipt_No.'.pdf');
    }

    /**
     * Download all receipts as ZIP (optional)
     */
    public function downloadAllReceipts()
    {
        $donor = Auth::user()->donor;

        if (! $donor) {
            return redirect()->route('dashboard')->with('error', 'Donor profile not found. (Database: Hannah)');
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
            'donor' => $donor,
        ]);

        return $pdf->download('all-receipts-'.date('Y-m-d').'.pdf');
    }

    /**
     * Browse campaigns (Public view)
     */
    public function publicBrowseCampaigns()
    {
        $campaigns = Campaign::with(['organization.user', 'donations'])
            ->where('Status', 'Active')
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
        // Count volunteers (cross-database safe)
        $volunteerCount = EventParticipation::where('Event_ID', $event->Event_ID)->count();
        $spotsLeft = $event->Capacity ? ($event->Capacity - $volunteerCount) : null;

        return view('donation-management.public-user.event-detail', compact('event', 'volunteerCount', 'spotsLeft'));
    }

    /**
     * Show recipient registration form
     */
    public function publicCreateRecipient()
    {
        $publicProfile = Auth::user()->publicProfile;

        if (! $publicProfile) {
            return redirect()->route('dashboard')->with('error', 'Public profile not found. (Database: Adam)');
        }

        return view('donation-management.public-user.recipient-create');
    }

    /**
     * Store new recipient
     */
    public function publicStoreRecipient(Request $request)
    {
        $publicProfile = Auth::user()->publicProfile;

        if (! $publicProfile) {
            return redirect()->route('dashboard')->with('error', 'Public profile not found. (Database: Adam)');
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
                ->with('success', 'Recipient registered successfully! Pending review. (Database: Adam)');
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

        if (! $publicProfile) {
            return redirect()->route('dashboard')->with('error', 'Public profile not found. (Database: Adam)');
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
            ->with('success', 'Recipient updated successfully! (Database: Adam)');
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
            ->with('success', 'Recipient deleted successfully! (Database: Adam)');
    }

    /**
     * Handle payment return from ToyyibPay
     */
    public function paymentReturn(Request $request)
    {
        // Get donation data from session
        $donationData = session('pending_donation');

        if (! $donationData) {
            return redirect()->route('campaigns.browse')
                ->with('error', 'Donation session expired. Please try again.');
        }

        // Get donor to verify ownership
        $donor = Auth::user()->donor;
        if (! $donor || $donor->Donor_ID != $donationData['donor_id']) {
            abort(403, 'Unauthorized donation access');
        }

        // Get campaign for later updates
        $campaign = Campaign::findOrFail($donationData['campaign_id']);

        // Check URL parameters for payment status (ToyyibPay passes status_id)
        $statusId = $request->input('status_id');

        if ($statusId == '1') {
            // ✅ PAYMENT SUCCESSFUL - Create donation with Completed status
            DB::beginTransaction();
            try {
                $donation = Donation::create([
                    'Donor_ID' => $donationData['donor_id'],
                    'Campaign_ID' => $donationData['campaign_id'],
                    'Amount' => $donationData['amount'],
                    'Donation_Date' => $donationData['donation_date'],
                    'Payment_Method' => $donationData['payment_method'],
                    'Receipt_No' => $donationData['receipt_no'],
                    'Payment_Status' => 'Completed', // SUCCESS - insert as Completed
                    'Bill_Code' => $donationData['bill_code'] ?? null,
                    'Transaction_ID' => $request->input('transaction_id') ?? 'TOYYIB-'.strtoupper(uniqid()),
                ]);

                // Update campaign total
                $campaign->increment('Collected_Amount', $donation->Amount);

                // Update donor total
                $donor->increment('Total_Donated', $donation->Amount);

                DB::commit();

                // Clear session
                session()->forget('pending_donation');

                return redirect()->route('campaigns.browse')
                    ->with('payment_success', [
                        'donation_id' => $donation->Donation_ID,
                        'amount' => $donation->Amount,
                        'receipt_no' => $donation->Receipt_No,
                        'campaign_title' => $campaign->Title,
                    ]);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to create donation after successful payment: '.$e->getMessage());

                return redirect()->route('campaigns.browse')
                    ->with('error', 'Failed to process donation. Please contact support.');
            }
        } elseif (in_array($statusId, ['2', '3'])) {
            // ❌ PAYMENT FAILED - Create donation with Failed status
            DB::beginTransaction();
            try {
                $donation = Donation::create([
                    'Donor_ID' => $donationData['donor_id'],
                    'Campaign_ID' => $donationData['campaign_id'],
                    'Amount' => $donationData['amount'],
                    'Donation_Date' => $donationData['donation_date'],
                    'Payment_Method' => $donationData['payment_method'],
                    'Receipt_No' => $donationData['receipt_no'],
                    'Payment_Status' => 'Failed', // FAILED - insert as Failed
                    'Bill_Code' => $donationData['bill_code'] ?? null,
                    'Transaction_ID' => null,
                ]);

                DB::commit();

                // Clear session
                session()->forget('pending_donation');

                return redirect()->route('campaigns.browse')
                    ->with('payment_failed', [
                        'amount' => $donation->Amount,
                        'receipt_no' => $donation->Receipt_No,
                        'campaign_id' => $campaign->Campaign_ID,
                        'campaign_title' => $campaign->Title,
                    ]);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to create failed donation record: '.$e->getMessage());
            }
        }

        // If no status_id or unknown status, try querying ToyyibPay API
        if (isset($donationData['bill_code'])) {
            $toyyibpay = new \App\Services\ToyyibPayService;
            $result = $toyyibpay->getBillTransactions($donationData['bill_code']);

            if ($result['success'] && ! empty($result['data'])) {
                $transactions = $result['data'];
                $successfulTransaction = collect($transactions)->firstWhere('billpaymentStatus', '1');

                if ($successfulTransaction) {
                    // Payment was successful
                    DB::beginTransaction();
                    try {
                        $donation = Donation::create([
                            'Donor_ID' => $donationData['donor_id'],
                            'Campaign_ID' => $donationData['campaign_id'],
                            'Amount' => $donationData['amount'],
                            'Donation_Date' => $donationData['donation_date'],
                            'Payment_Method' => $donationData['payment_method'],
                            'Receipt_No' => $donationData['receipt_no'],
                            'Payment_Status' => 'Completed',
                            'Bill_Code' => $donationData['bill_code'],
                            'Transaction_ID' => $successfulTransaction['billpaymentInvoiceNo'] ?? 'TOYYIB-'.strtoupper(uniqid()),
                        ]);

                        $campaign->increment('Collected_Amount', $donation->Amount);
                        $donor->increment('Total_Donated', $donation->Amount);

                        DB::commit();
                        session()->forget('pending_donation');

                        return redirect()->route('campaigns.browse')
                            ->with('payment_success', [
                                'donation_id' => $donation->Donation_ID,
                                'amount' => $donation->Amount,
                                'receipt_no' => $donation->Receipt_No,
                                'campaign_title' => $campaign->Title,
                            ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Log::error('Failed to create donation: '.$e->getMessage());
                    }
                }
            }
        }

        // Default: Payment failed - create failed record
        DB::beginTransaction();
        try {
            $donation = Donation::create([
                'Donor_ID' => $donationData['donor_id'],
                'Campaign_ID' => $donationData['campaign_id'],
                'Amount' => $donationData['amount'],
                'Donation_Date' => $donationData['donation_date'],
                'Payment_Method' => $donationData['payment_method'],
                'Receipt_No' => $donationData['receipt_no'],
                'Payment_Status' => 'Failed',
                'Bill_Code' => $donationData['bill_code'] ?? null,
                'Transaction_ID' => null,
            ]);

            DB::commit();
            session()->forget('pending_donation');

            return redirect()->route('campaigns.browse')
                ->with('payment_failed', [
                    'amount' => $donation->Amount,
                    'receipt_no' => $donation->Receipt_No,
                    'campaign_id' => $campaign->Campaign_ID,
                    'campaign_title' => $campaign->Title,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create failed donation: '.$e->getMessage());

            return redirect()->route('campaigns.browse')
                ->with('error', 'Payment failed. Please try again.');
        }
    }

    /**
     * Handle payment callback from ToyyibPay (server-to-server notification)
     * Note: Callback is async and may arrive after user return, so we check if donation already exists
     */
    public function paymentCallback(Request $request)
    {
        \Log::info('ToyyibPay Callback', $request->all());

        $toyyibpay = new \App\Services\ToyyibPayService;

        // Verify payment
        if ($toyyibpay->verifyPayment($request->all())) {
            $refNo = $request->input('refno'); // Format: DON-timestamp-donorId
            $billCode = $request->input('billcode');
            $statusId = $request->input('status_id');

            // Check if donation already exists for this bill
            $existingDonation = Donation::where('Bill_Code', $billCode)->first();

            if ($existingDonation) {
                // Donation already created by paymentReturn, just log
                \Log::info('Callback received for existing donation: '.$existingDonation->Donation_ID);
            } else {
                // This shouldn't happen normally, but handle it as backup
                \Log::warning('Callback received but no donation record found for bill: '.$billCode);
                // We can't create the donation here because we don't have the session data
                // This would only happen if callback arrives before user return (very rare)
            }
        }

        return response('OK', 200);
    }
}

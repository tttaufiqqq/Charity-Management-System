<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Services\DatabaseProcedureService;
use Illuminate\Http\Request;

class ProcedureTestController extends Controller
{
    /**
     * Test sp_get_user_role_stats procedure (izzhilmy database)
     * Shows user statistics by role
     */
    public function userRoleStats(Request $request)
    {
        $roleName = $request->get('role');

        try {
            $stats = DatabaseProcedureService::getUserRoleStats($roleName);

            return view('procedure-tests.user-role-stats', [
                'stats' => $stats,
                'filterRole' => $roleName,
                'procedureName' => 'sp_get_user_role_stats',
                'database' => 'izzhilmy (PostgreSQL)',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Procedure error: '.$e->getMessage());
        }
    }

    /**
     * Test sp_update_campaign_collected_amount procedure (izzati database)
     * Shows campaign update form and results
     */
    public function campaignCollectedAmount(Request $request)
    {
        $campaigns = Campaign::where('Status', 'Active')
            ->orderBy('Title')
            ->get();

        $result = null;
        $selectedCampaign = null;

        if ($request->isMethod('post')) {
            $request->validate([
                'campaign_id' => 'required|integer',
                'amount' => 'required|numeric|min:0',
                'operation' => 'required|in:ADD,SUBTRACT,SET',
            ]);

            $selectedCampaign = Campaign::find($request->campaign_id);

            try {
                $result = DatabaseProcedureService::updateCampaignCollectedAmount(
                    $request->campaign_id,
                    $request->amount,
                    $request->operation
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Procedure error: '.$e->getMessage())->withInput();
            }
        }

        return view('procedure-tests.campaign-collected-amount', [
            'campaigns' => $campaigns,
            'result' => $result,
            'selectedCampaign' => $selectedCampaign,
            'procedureName' => 'sp_update_campaign_collected_amount',
            'database' => 'izzati (PostgreSQL)',
        ]);
    }

    /**
     * Index page showing all available procedure tests
     */
    public function index()
    {
        $procedures = [
            [
                'name' => 'sp_get_user_role_stats',
                'database' => 'izzhilmy (PostgreSQL)',
                'description' => 'Returns user statistics grouped by role, including user count and registration dates.',
                'route' => route('procedures.user-role-stats'),
                'type' => 'READ',
            ],
            [
                'name' => 'sp_update_campaign_collected_amount',
                'database' => 'izzati (PostgreSQL)',
                'description' => 'Updates campaign collected amount with ADD, SUBTRACT, or SET operations.',
                'route' => route('procedures.campaign-collected'),
                'type' => 'WRITE',
            ],
            [
                'name' => 'sp_get_donation_stats',
                'database' => 'hannah (MySQL)',
                'description' => 'Returns donation statistics for campaigns including totals and averages.',
                'route' => route('procedures.donation-stats'),
                'type' => 'READ',
            ],
            [
                'name' => 'sp_get_volunteer_hours',
                'database' => 'sashvini (MariaDB)',
                'description' => 'Returns volunteer participation hours and event statistics.',
                'route' => route('procedures.volunteer-hours'),
                'type' => 'READ',
            ],
            [
                'name' => 'sp_get_recipient_summary',
                'database' => 'adam (MySQL)',
                'description' => 'Returns recipient application summary with status and review information.',
                'route' => route('procedures.recipient-summary'),
                'type' => 'READ',
            ],
        ];

        return view('procedure-tests.index', compact('procedures'));
    }

    /**
     * Test sp_get_donation_stats procedure (hannah database)
     */
    public function donationStats(Request $request)
    {
        $campaigns = Campaign::where('Status', '!=', 'Pending')
            ->orderBy('Title')
            ->get();

        $result = null;

        $campaignId = $request->get('campaign_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($campaignId || $startDate || $endDate) {
            try {
                $result = DatabaseProcedureService::getDonationStats(
                    $campaignId ?: null,
                    $startDate ?: null,
                    $endDate ?: null
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Procedure error: '.$e->getMessage())->withInput();
            }
        }

        return view('procedure-tests.donation-stats', [
            'campaigns' => $campaigns,
            'result' => $result,
            'filters' => [
                'campaign_id' => $campaignId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'procedureName' => 'sp_get_donation_stats',
            'database' => 'hannah (MySQL)',
        ]);
    }

    /**
     * Test sp_get_volunteer_hours procedure (sashvini database)
     */
    public function volunteerHours(Request $request)
    {
        $volunteers = \App\Models\Volunteer::with('user')->get();

        $results = [];

        $volunteerId = $request->get('volunteer_id');
        $statusFilter = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($volunteerId || $statusFilter || $startDate || $endDate) {
            try {
                $results = DatabaseProcedureService::getVolunteerHours(
                    $volunteerId ?: null,
                    $statusFilter ?: null,
                    $startDate ?: null,
                    $endDate ?: null
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Procedure error: '.$e->getMessage())->withInput();
            }
        }

        return view('procedure-tests.volunteer-hours', [
            'volunteers' => $volunteers,
            'results' => $results,
            'filters' => [
                'volunteer_id' => $volunteerId,
                'status' => $statusFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'procedureName' => 'sp_get_volunteer_hours',
            'database' => 'sashvini (MariaDB)',
        ]);
    }

    /**
     * Test sp_get_recipient_summary procedure (adam database)
     */
    public function recipientSummary(Request $request)
    {
        $recipients = \App\Models\Recipient::orderBy('Name')->get();

        $results = [];

        $recipientId = $request->get('recipient_id');
        $statusFilter = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($recipientId || $statusFilter || $startDate || $endDate || $request->has('show_all')) {
            try {
                $results = DatabaseProcedureService::getRecipientSummary(
                    $recipientId ?: null,
                    $statusFilter ?: null,
                    $startDate ?: null,
                    $endDate ?: null
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Procedure error: '.$e->getMessage())->withInput();
            }
        }

        return view('procedure-tests.recipient-summary', [
            'recipients' => $recipients,
            'results' => $results,
            'filters' => [
                'recipient_id' => $recipientId,
                'status' => $statusFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'procedureName' => 'sp_get_recipient_summary',
            'database' => 'adam (MySQL)',
        ]);
    }
}

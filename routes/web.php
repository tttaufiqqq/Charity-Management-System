<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonationManagementController;
use App\Http\Controllers\EventManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipientManagementController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');  // This looks for welcome.blade.php
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* volunteer-management */
Route::middleware(['auth'])->group(function () {
    Route::get('/volunteer/dashboard', [VolunteerController::class, 'dashboard'])->name('volunteer.dashboard');
    Route::get('/volunteer/schedule', [VolunteerController::class, 'schedule'])->name('volunteer.schedule');
    Route::get('/volunteer/profile', [VolunteerController::class, 'profile'])->name('volunteer.profile');
    Route::get('/volunteer/profile/edit', [VolunteerController::class, 'editProfile'])->name('volunteer.profile.edit');
    Route::put('/volunteer/profile', [VolunteerController::class, 'updateProfile'])->name('volunteer.profile.update');
    Route::get('/volunteer/events', [VolunteerController::class, 'browseEvents'])->name('volunteer.events.browse');
    Route::get('/volunteer/events/{event}', [VolunteerController::class, 'showEvent'])->name('volunteer.events.show');
    Route::post('/volunteer/events/{event}/register', [VolunteerController::class, 'registerForEvent'])->name('volunteer.events.register');
    Route::delete('/volunteer/events/{event}/cancel', [VolunteerController::class, 'cancelRegistration'])->name('volunteer.events.cancel');
    Route::get('/volunteer/my-events', [VolunteerController::class, 'myEvents'])->name('volunteer.events.my-events');

    Route::get('/volunteer/skills', [VolunteerController::class, 'showSkills'])->name('volunteer.skills.index');
    Route::post('/volunteer/skills', [VolunteerController::class, 'storeSkill'])->name('volunteer.skills.store');
    Route::put('/volunteer/skills/{skillId}', [VolunteerController::class, 'updateSkill'])->name('volunteer.skills.update');
    Route::delete('/volunteer/skills/{skillId}', [VolunteerController::class, 'deleteSkill'])->name('volunteer.skills.delete');
});

/* event-management */
Route::middleware(['auth'])->group(function () {

    // Campaign Routes
    Route::get('/campaigns/all', [EventManagementController::class, 'indexCampaigns'])->name('campaigns.index');
    Route::get('/campaigns/create', [EventManagementController::class, 'createCampaign'])->name('campaigns.create');
    Route::post('/campaigns', [EventManagementController::class, 'storeCampaign'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [EventManagementController::class, 'showCampaign'])->whereNumber('campaign')->name('campaigns.show');
    Route::get('/campaigns/{campaign}/edit', [EventManagementController::class, 'editCampaign'])->name('campaigns.edit');
    Route::put('/campaigns/{campaign}', [EventManagementController::class, 'updateCampaign'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [EventManagementController::class, 'destroyCampaign'])->name('campaigns.destroy');

    // Event Routes
    Route::get('/events', [EventManagementController::class, 'indexEvents'])->name('events.index');
    Route::get('/events/create', [EventManagementController::class, 'createEvent'])->name('events.create');
    Route::post('/events', [EventManagementController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{event}', [EventManagementController::class, 'showEvent'])->name('events.show');
    Route::get('/events/{event}/edit', [EventManagementController::class, 'editEvent'])->name('events.edit');
    Route::put('/events/{event}', [EventManagementController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/{event}', [EventManagementController::class, 'destroyEvent'])->name('events.destroy');

    // Volunteer Management for Events
    Route::get('/events/{event}/volunteers', [EventManagementController::class, 'manageVolunteers'])->name('events.manage-volunteers');
    Route::post('/events/{event}/volunteers/{volunteer}/hours', [EventManagementController::class, 'updateVolunteerHours'])->name('events.update-volunteer-hours');
    Route::post('/events/{event}/auto-calculate-hours', [EventManagementController::class, 'autoCalculateHours'])->name('events.auto-calculate-hours');
    Route::post('/events/{event}/bulk-update-volunteers', [EventManagementController::class, 'bulkUpdateVolunteers'])->name('events.bulk-update-volunteers');

    Route::get('/admin/dashboard', [EventManagementController::class, 'adminDashboard'])->name('admin.dashboard');

    // User Management
    Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.manage.users');

    // Campaign Approval
    Route::get('/admin/campaigns/pending', [EventManagementController::class, 'adminPendingCampaigns'])->name('admin.campaigns.pending');
    Route::post('/admin/campaigns/{campaign}/approve', [EventManagementController::class, 'adminApproveCampaign'])->name('admin.campaigns.approve');
    Route::post('/admin/campaigns/{campaign}/reject', [EventManagementController::class, 'adminRejectCampaign'])->name('admin.campaigns.reject');

    // Event Approval
    Route::get('/admin/events/pending', [EventManagementController::class, 'adminPendingEvents'])->name('admin.events.pending');
    Route::post('/admin/events/{event}/approve', [EventManagementController::class, 'adminApproveEvent'])->name('admin.events.approve');
    Route::post('/admin/events/{event}/reject', [EventManagementController::class, 'adminRejectEvent'])->name('admin.events.reject');

});

/* donation-management */
Route::middleware(['auth'])->group(function () {
    // Browse campaigns
    Route::get('/campaigns', [DonationManagementController::class, 'browseCampaigns'])->name('campaigns.browse');
    Route::get('/campaigns/donate/{id}', [DonationManagementController::class, 'showCampaign'])->name('campaigns.show.donate');
    // Donation process
    Route::get('/campaigns/{campaignId}/donate', [DonationManagementController::class, 'showDonationForm'])->name('campaigns.donate');
    Route::post('/campaigns/{campaignId}/donate', [DonationManagementController::class, 'processDonation'])->name('campaigns.donate.process');
    Route::get('/donation/success/{donationId}', [DonationManagementController::class, 'donationSuccess'])->name('donation.success');

    // ToyyibPay Payment Routes
    Route::get('/donation/payment/return/{donationId}', [DonationManagementController::class, 'paymentReturn'])->name('donation.payment.return');

    // My donations
    Route::get('/my-donations', [DonationManagementController::class, 'myDonations'])->name('donations.my');

    // Receipts
    Route::get('/donation/{donationId}/receipt', [DonationManagementController::class, 'downloadReceipt'])->name('donation.receipt');
    Route::get('/donations/receipts/all', [DonationManagementController::class, 'downloadAllReceipts'])->name('donations.receipts.all');

    Route::get('/public/campaigns', [DonationManagementController::class, 'publicBrowseCampaigns'])->name('public.campaigns.browse');
    Route::get('/public/campaigns/{campaign}', [DonationManagementController::class, 'publicShowCampaign'])->name('public.campaigns.show');
    Route::get('/public/events', [DonationManagementController::class, 'publicBrowseEvents'])->name('public.events.browse');
    Route::get('/public/events/{event}', [DonationManagementController::class, 'publicShowEvent'])->name('public.events.show');

    // Recipient Management
    Route::get('/public/recipients', [DonationManagementController::class, 'publicIndexRecipients'])->name('public.recipients.index');
    Route::get('/public/recipients/create', [DonationManagementController::class, 'publicCreateRecipient'])->name('public.recipients.create');
    Route::post('/public/recipients', [DonationManagementController::class, 'publicStoreRecipient'])->name('public.recipients.store');
    Route::get('/public/recipients/{recipient}', [DonationManagementController::class, 'publicShowRecipient'])->name('public.recipients.show');
    Route::get('/public/recipients/{recipient}/edit', [DonationManagementController::class, 'publicEditRecipient'])->name('public.recipients.edit');
    Route::put('/public/recipients/{recipient}', [DonationManagementController::class, 'publicUpdateRecipient'])->name('public.recipients.update');
    Route::delete('/public/recipients/{recipient}', [DonationManagementController::class, 'publicDestroyRecipient'])->name('public.recipients.destroy');
});

/* recipient-management */
Route::middleware(['auth'])->group(function () {
    Route::get('/campaigns/{campaignId}/allocate', [RecipientManagementController::class, 'showRecipients'])->name('recipients.allocate');
    Route::post('/campaigns/{campaignId}/allocate', [RecipientManagementController::class, 'allocateFunds'])->name('recipients.allocate.store');
    Route::get('/campaigns/{campaignId}/allocations/history', [RecipientManagementController::class, 'allocationHistory'])->name('recipients.allocations.history');
    Route::delete('/campaigns/{campaignId}/allocations/{recipientId}', [RecipientManagementController::class, 'removeAllocation'])->name('recipients.allocations.remove');

    Route::get('/recipients/{recipientId}/allocations', [RecipientManagementController::class, 'recipientAllocations'])->name('recipients.allocations.view');
    Route::get('/organizer/campaigns', [RecipientManagementController::class, 'myCampaigns'])->name('organizer.campaigns');
    Route::get('/organizer/allocations', [RecipientManagementController::class, 'allAllocations'])->name('organizer.allocations.all');

    Route::get('/recipients/pending', [RecipientManagementController::class, 'pendingRecipients'])->name('admin.recipients.pending');
    Route::get('/recipients/all', [RecipientManagementController::class, 'allRecipients'])->name('admin.recipients.all');
    Route::get('/recipients/{id}', [RecipientManagementController::class, 'adminShowRecipient'])->name('admin.recipients.show');

    // Approval Actions
    Route::post('/recipients/{id}/approve', [RecipientManagementController::class, 'approveRecipient'])->name('admin.recipients.approve');
    Route::post('/recipients/{id}/reject', [RecipientManagementController::class, 'rejectRecipient'])->name('admin.recipients.reject');
    Route::put('/recipients/{id}/status', [RecipientManagementController::class, 'updateRecipientStatus'])->name('admin.recipients.status');
    Route::delete('/recipients/{id}', [RecipientManagementController::class, 'adminDeleteRecipient'])->name('admin.recipients.delete');
});

/* reporting */
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/analytics', function () {
        return view('reporting.dashboard');
    })->name('admin.analytics.dashboard');

    Route::get('/admin/analytics/campaigns', function () {
        return view('reporting.campaigns');
    })->name('admin.analytics.campaigns');

    Route::get('/admin/analytics/events', function () {
        return view('reporting.events');
    })->name('admin.analytics.events');

    Route::get('/admin/analytics/donors', function () {
        return view('reporting.donors');
    })->name('admin.analytics.donors');

    Route::get('/admin/analytics/donations', function () {
        return view('reporting.donations');
    })->name('admin.analytics.donations');
});

// ToyyibPay Callback (No auth middleware - called by ToyyibPay server)
Route::post('/donation/payment/callback', [DonationManagementController::class, 'paymentCallback'])->name('donation.payment.callback');

require __DIR__.'/auth.php';

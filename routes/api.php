<?php

use App\Http\Controllers\Api\CampaignApiController;
use App\Http\Controllers\Api\DonationApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\OrganizationApiController;
use App\Http\Controllers\Api\RecipientApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| Service Distribution:
| - User Service (izz - PostgreSQL 5432): Users, Roles, Permissions
| - Volunteer Service (sashvini - MariaDB 3307): Volunteers, Skills, Events, Event Participation
| - Event Management (izati - PostgreSQL 5433): Organizations, Campaigns, Event Roles
| - Donation Service (hannah - MySQL 3306): Donors, Donations, Allocations
| - Recipient Service (adam - MySQL 3308): Public Profiles, Recipients
|
*/

Route::prefix('v1')->group(function () {
    // =========================================================================
    // User Service APIs (PostgreSQL - izz)
    // =========================================================================
    Route::prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index']);
        Route::get('/{id}', [UserApiController::class, 'show']);
        Route::post('/', [UserApiController::class, 'store']);
        Route::put('/{id}', [UserApiController::class, 'update']);
        Route::delete('/{id}', [UserApiController::class, 'destroy']);
        Route::post('/{id}/has-role', [UserApiController::class, 'hasRole']);
    });

    // =========================================================================
    // Event Management Service APIs (PostgreSQL - izati)
    // =========================================================================
    Route::prefix('campaigns')->group(function () {
        Route::get('/', [CampaignApiController::class, 'index']);
        Route::get('/{id}', [CampaignApiController::class, 'show']);
        Route::post('/', [CampaignApiController::class, 'store']);
        Route::put('/{id}', [CampaignApiController::class, 'update']);

        // Special endpoints for cross-service sync
        Route::put('/{id}/collected-amount', [CampaignApiController::class, 'updateCollectedAmount']);
        Route::put('/{id}/sync-collected-amount', [CampaignApiController::class, 'syncCollectedAmount']);
        Route::get('/{id}/available-funds', [CampaignApiController::class, 'getAvailableFunds']);

        // Admin actions
        Route::put('/{id}/approve', [CampaignApiController::class, 'approve']);
        Route::put('/{id}/reject', [CampaignApiController::class, 'reject']);
    });

    Route::prefix('organizations')->group(function () {
        Route::get('/', [OrganizationApiController::class, 'index']);
        Route::get('/{id}', [OrganizationApiController::class, 'show']);
        Route::post('/', [OrganizationApiController::class, 'store']);
        Route::put('/{id}', [OrganizationApiController::class, 'update']);
        Route::delete('/{id}', [OrganizationApiController::class, 'destroy']);
        Route::get('/{id}/campaigns', [OrganizationApiController::class, 'getCampaigns']);
        Route::get('/{id}/events', [OrganizationApiController::class, 'getEvents']);
    });

    // =========================================================================
    // Volunteer Service APIs (MariaDB - sashvini)
    // =========================================================================
    Route::prefix('events')->group(function () {
        Route::get('/', [EventApiController::class, 'index']);
        Route::get('/{id}', [EventApiController::class, 'show']);
        Route::post('/', [EventApiController::class, 'store']);
        Route::put('/{id}', [EventApiController::class, 'update']);

        // Event participation management
        Route::get('/{id}/participants', [EventApiController::class, 'getParticipants']);
        Route::post('/{id}/register', [EventApiController::class, 'registerVolunteer']);
        Route::delete('/{eventId}/participants/{volunteerId}', [EventApiController::class, 'cancelRegistration']);
        Route::put('/{eventId}/participants/{volunteerId}/hours', [EventApiController::class, 'updateParticipantHours']);
    });

    // Event Roles would be added here (split between Event Management and Volunteer services)
    // For now, handled via Event Management service

    // =========================================================================
    // Donation Service APIs (MySQL - hannah)
    // =========================================================================
    Route::prefix('donations')->group(function () {
        Route::get('/', [DonationApiController::class, 'index']);
        Route::get('/{id}', [DonationApiController::class, 'show']);
        Route::post('/', [DonationApiController::class, 'store']);
        Route::put('/{id}/payment-status', [DonationApiController::class, 'updatePaymentStatus']);
    });

    Route::prefix('allocations')->group(function () {
        Route::post('/', [DonationApiController::class, 'createAllocation']);
        Route::get('/campaign/{campaignId}', [DonationApiController::class, 'getAllocationsByCampaign']);
        Route::get('/recipient/{recipientId}', [DonationApiController::class, 'getAllocationsByRecipient']);
    });

    // =========================================================================
    // Recipient Service APIs (MySQL - adam)
    // =========================================================================
    Route::prefix('recipients')->group(function () {
        Route::get('/', [RecipientApiController::class, 'index']);
        Route::get('/{id}', [RecipientApiController::class, 'show']);
        Route::post('/', [RecipientApiController::class, 'store']);
        Route::put('/{id}', [RecipientApiController::class, 'update']);

        // Admin actions
        Route::put('/{id}/approve', [RecipientApiController::class, 'approve']);
        Route::put('/{id}/reject', [RecipientApiController::class, 'reject']);

        // Allocations (calls Donation service)
        Route::get('/{id}/allocations', [RecipientApiController::class, 'getAllocations']);
    });

    // Health check endpoint for each service
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'service' => config('app.name'),
            'timestamp' => now(),
            'databases' => [
                'izz' => DB::connection('izz')->getPdo() ? 'connected' : 'disconnected',
                'sashvini' => DB::connection('sashvini')->getPdo() ? 'connected' : 'disconnected',
                'izati' => DB::connection('izati')->getPdo() ? 'connected' : 'disconnected',
                'hannah' => DB::connection('hannah')->getPdo() ? 'connected' : 'disconnected',
                'adam' => DB::connection('adam')->getPdo() ? 'connected' : 'disconnected',
            ],
        ]);
    });
});

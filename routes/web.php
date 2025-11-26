<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventManagementController;
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

/*volunteer-management*/
Route::middleware(['auth'])->group(function () {
    Route::get('/volunteer/dashboard', [App\Http\Controllers\VolunteerController::class, 'dashboard'])->name('volunteer.dashboard');
    Route::get('/volunteer/events', [App\Http\Controllers\VolunteerController::class, 'browseEvents'])->name('volunteer.events.browse');
    Route::get('/volunteer/events/{event}', [App\Http\Controllers\VolunteerController::class, 'showEvent'])->name('volunteer.events.show');
    Route::post('/volunteer/events/{event}/register', [App\Http\Controllers\VolunteerController::class, 'registerForEvent'])->name('volunteer.events.register');
    Route::delete('/volunteer/events/{event}/cancel', [App\Http\Controllers\VolunteerController::class, 'cancelRegistration'])->name('volunteer.events.cancel');
    Route::get('/volunteer/my-events', [App\Http\Controllers\VolunteerController::class, 'myEvents'])->name('volunteer.events.my-events');
});

/*event-management*/
Route::middleware(['auth'])->group(function () {

    // Campaign Routes
    Route::get('/campaigns', [EventManagementController::class, 'indexCampaigns'])->name('campaigns.index');
    Route::get('/campaigns/create', [EventManagementController::class, 'createCampaign'])->name('campaigns.create');
    Route::post('/campaigns', [EventManagementController::class, 'storeCampaign'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [EventManagementController::class, 'showCampaign'])->name('campaigns.show');
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

});

/*donation-management*/

/*recipient-management*/

/*reporting*/

require __DIR__.'/auth.php';

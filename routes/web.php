<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\AdminClientController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\PartnerDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/send-test-email', function () {
    Mail::to('your-email@example.com')->send(new TestEmail());
    return 'Test email sent!';
});

/*
|--------------------------------------------------------------------------
| Authenticated & Verified Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Show pending approval view for guests
    Route::get('/approval-pending', function () {
        return view('auth.approval-pending');
    })->name('approval.pending');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth'])
        ->name('dashboard');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/{client}/documents', [ClientController::class, 'storeDocument'])->name('clients.documents.store');
    Route::post('/clients/{client}/communications', [ClientController::class, 'storeCommunication'])->name('clients.communications.store');

    /*
    |--------------------------------------------------------------------------
    | Admin Routs (Optional)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,superadmin')
    ->prefix('admin')
    ->group(function () {
        Route::get('/users', [ApprovalController::class, 'index'])->name('approve_users');
        Route::patch('/users/{user}/approve', [ApprovalController::class, 'approve'])->name('approve_user');
        Route::get('/clients', [AdminClientController::class, 'index'])->name('admin.clients.index');
        Route::get('/documents', [AdminDocumentController::class, 'index'])->name('admin.documents.index');
        Route::get('/documents/create', [AdminDocumentController::class, 'create'])->name('admin.documents.create');
        Route::post('/documents', [AdminDocumentController::class, 'store'])->name('admin.documents.store');
        Route::patch('/documents/{id}/approve', [AdminDocumentController::class, 'approve'])->name('admin.documents.approve');
        Route::patch('/responses/{id}/approve', [AdminDocumentController::class, 'approveResponse'])->name('admin.responses.approve');
        Route::get('documents/{id}', [AdminDocumentController::class, 'show'])->name('admin.documents.show');
        Route::resource('meetings', CalendarController::class)->except(['index', 'show']);
    });

        /*
    |--------------------------------------------------------------------------
    | Channel Partner Routs (Optional)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:channel_partner')
    ->prefix('partner') // ✅ NEW PREFIX
    ->group(function () {
        Route::resource('lead-sources', \App\Http\Controllers\LeadSourceController::class);
        Route::get('/lead-sources', [LeadSourceController::class, 'index'])->name('lead-sources.index');
        Route::post('/lead-sources', [LeadSourceController::class, 'store'])->name('lead-sources.store');
        Route::get('/documents', [PartnerDocumentController::class, 'index'])->name('partner.documents.index');
        Route::post('/documents/{id}/upload-response', [PartnerDocumentController::class, 'uploadResponse'])->name('partner.documents.uploadResponse');
        Route::post('/documents/{id}/acknowledge', [PartnerDocumentController::class, 'acknowledge'])->name('partner.documents.acknowledge');
        Route::post('/meetings/{meeting}/respond', [CalendarController::class, 'respond'])->name('meetings.respond');
    });
    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

      /*
    |-------------------------------------------------------------------------- 
    | Calendar Routes (Shared)
    |-------------------------------------------------------------------------- 
    */
    Route::get('/calendar/fetch', [\App\Http\Controllers\CalendarController::class, 'fetchMeetings']);
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
});

// Optional standalone route (not protected)
    Route::get('/test-role', function () {
        return 'Role middleware works!';
    })->middleware('role:admin,superadmin');

    /*
    |--------------------------------------------------------------------------
    | Auth Routes (Laravel Breeze or Fortify)
    |--------------------------------------------------------------------------
    */
    require __DIR__.'/auth.php';


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LeadSourceController;

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

    // Shared dashboard for all approved users
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if (! $user->is_approved) {
            return redirect()->route('approval.pending');
        }

        return view('dashboard', compact('user'));
    })->name('dashboard');

     Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

    /*
    |--------------------------------------------------------------------------
    | Admin Routs (Optional)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::get('/admin/users', [ApprovalController::class, 'index'])->name('approve_users');
        Route::patch('/admin/users/{user}/approve', [ApprovalController::class, 'approve'])->name('approve_user');
    });

        /*
    |--------------------------------------------------------------------------
    | Channel Partner Routs (Optional)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:channel_partner')->group(function () {
        Route::resource('lead-sources', \App\Http\Controllers\LeadSourceController::class);
        Route::get('/lead-sources', [LeadSourceController::class, 'index'])->name('lead-sources.index');
        Route::post('/lead-sources', [LeadSourceController::class, 'store'])->name('lead-sources.store');
    });
    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-role', function () {
    return 'Role middleware works!';
})->middleware('role:admin,superadmin');

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze or Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';


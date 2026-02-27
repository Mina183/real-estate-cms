<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\CapitalCallController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\DataRoomController;
use App\Http\Controllers\InvestorAuthController;
use App\Http\Controllers\InvestorPortalController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\InvestorTwoFactorController;
use App\Http\Controllers\InvestorPasswordResetController;
use App\Http\Controllers\InvestorEmailController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Investor Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('investor')->name('investor.')->group(function () {
    
    Route::middleware('guest:investor')->group(function () {
        Route::get('/login', [InvestorAuthController::class, 'showLoginForm'])
            ->name('login');
        Route::post('/login', [InvestorAuthController::class, 'login']);

            // Password Reset
    Route::get('/forgot-password', [InvestorPasswordResetController::class, 'showForgotForm'])
        ->name('password.request');
    Route::post('/forgot-password', [InvestorPasswordResetController::class, 'sendResetLink'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [InvestorPasswordResetController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [InvestorPasswordResetController::class, 'resetPassword'])
        ->name('password.update');
    });

    Route::middleware('investor')->group(function () {
        Route::post('/logout', [InvestorAuthController::class, 'logout'])
            ->name('logout');

        // 2FA
        Route::get('/2fa/setup', [InvestorTwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('/2fa/enable', [InvestorTwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::get('/2fa/verify', [InvestorTwoFactorController::class, 'verify'])->name('2fa.verify');
        Route::post('/2fa/check', [InvestorTwoFactorController::class, 'check'])->name('2fa.check');    
        
        // Dashboard
        Route::get('/dashboard', [InvestorPortalController::class, 'dashboard'])
            ->name('dashboard');
        
        // Profile (placeholder for later)
        Route::get('/profile', [InvestorPortalController::class, 'profile'])
            ->name('profile');
        
        // Documents (placeholder for later)
        Route::get('/documents', [InvestorPortalController::class, 'documents'])
            ->name('documents');

        // Document download
        Route::get('/documents/download/{document}', [InvestorPortalController::class, 'downloadDocument'])
        ->name('documents.download');
     });
});

/*
|--------------------------------------------------------------------------
| Authenticated Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Approval pending (placeholder for Phase 7 invite system)
    Route::get('/approval-pending', function () {
        return view('auth.approval-pending');
    })->name('approval.pending');

    // Dashboard - all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile Management - all authenticated users
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('profile.edit');
        Route::patch('/', 'update')->name('profile.update');
        Route::delete('/', 'destroy')->name('profile.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Investor Management
    | Accessible by: superadmin, admin, operations, compliance_officer
    |--------------------------------------------------------------------------
    */
    Route::prefix('investors')->controller(InvestorController::class)->group(function () {
        Route::get('/', 'index')->name('investors.index');
        Route::get('/create', 'create')->name('investors.create');
        Route::post('/', 'store')->name('investors.store');
        Route::get('/{investor}', 'show')->name('investors.show');
        Route::get('/{investor}/edit', 'edit')->name('investors.edit');
        Route::put('/{investor}', 'update')->name('investors.update');
        Route::delete('/{investor}', 'destroy')->name('investors.destroy');
        
        // Stage management
        Route::get('/{investor}/change-stage', 'changeStageForm')
            ->name('investors.change-stage.form');
        Route::post('/{investor}/change-stage', 'changeStage')
            ->name('investors.change-stage');
        
        // Activity log
        Route::get('/{investor}/activity', 'activityLog')
            ->name('investors.activity');

        // Email sending
        Route::get('/{investor}/send-email', [InvestorEmailController::class, 'compose'])->name('investors.send-email.form');
        Route::post('/send-email', [InvestorEmailController::class, 'send'])->name('investors.send-email');
        Route::get('/send-email/bulk', [InvestorEmailController::class, 'composeBulk'])->name('investors.send-email.bulk');
        Route::get('/investors/send-email/preview', [InvestorEmailController::class, 'preview'])->name('investors.send-email.preview');

        Route::post('/{investor}/contacts', [ContactController::class, 'store'])->name('investors.contacts.store');
        Route::delete('/{investor}/contacts/{contact}', [ContactController::class, 'destroy'])->name('investors.contacts.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Capital Calls Management
    | Accessible by: superadmin, admin, operations
    | Gate: manage-capital-calls
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:manage-capital-calls'])->group(function () {
        Route::prefix('capital-calls')->controller(CapitalCallController::class)->group(function () {
            Route::get('/', 'index')->name('capital-calls.index');
            Route::get('/create', 'create')->name('capital-calls.create');
            Route::post('/', 'store')->name('capital-calls.store');
            Route::get('/{capitalCall}', 'show')->name('capital-calls.show');
            Route::get('/{capitalCall}/edit', 'edit')->name('capital-calls.edit');
            Route::put('/{capitalCall}', 'update')->name('capital-calls.update');
            Route::delete('/{capitalCall}', 'destroy')->name('capital-calls.destroy');
            Route::post('/{capitalCall}/issue', 'issue')->name('capital-calls.issue');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Distributions Management
    | Accessible by: superadmin, admin, operations
    | Gate: manage-distributions
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:manage-distributions'])->group(function () {
        Route::prefix('distributions')->controller(DistributionController::class)->group(function () {
            Route::get('/', 'index')->name('distributions.index');
            Route::get('/create', 'create')->name('distributions.create');
            Route::post('/', 'store')->name('distributions.store');
            Route::get('/{distribution}', 'show')->name('distributions.show');
            Route::get('/{distribution}/edit', 'edit')->name('distributions.edit');
            Route::put('/{distribution}', 'update')->name('distributions.update');
            Route::delete('/{distribution}', 'destroy')->name('distributions.destroy');
            Route::post('/{distribution}/approve', 'approve')->name('distributions.approve');
            Route::post('/{distribution}/process', 'process')->name('distributions.process');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Payment Transactions
    | Accessible by: superadmin, admin, operations
    |--------------------------------------------------------------------------
    */
    Route::prefix('payments')->controller(PaymentTransactionController::class)->group(function () {
        Route::post('/{payment}/mark-paid', 'markAsPaid')->name('payments.mark-paid');
        Route::post('/{payment}/mark-failed', 'markAsFailed')->name('payments.mark-failed');
        Route::post('/{payment}/reverse', 'reverse')->name('payments.reverse');
        Route::put('/{payment}', 'update')->name('payments.update');
        Route::delete('/{payment}', 'destroy')->name('payments.destroy');
        Route::post('/bulk/mark-paid', 'bulkMarkAsPaid')->name('payments.bulk-mark-paid');
    });

    /*
    |--------------------------------------------------------------------------
    | Data Room
    | Upload restricted by policy, download/view have role-based access
    |--------------------------------------------------------------------------
    */
    Route::prefix('data-room')->controller(DataRoomController::class)->group(function () {
        Route::get('/', 'index')->name('data-room.index');
        Route::post('/upload', 'upload')->name('data-room.upload');
        Route::get('/download/{document}', 'download')->name('data-room.download');
        Route::get('/export-index', 'exportIndex')->name('data-room.export-index');
    });

    /*
    |--------------------------------------------------------------------------
    | Two Factor Authentication
    |--------------------------------------------------------------------------
    */
    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::get('/setup', [TwoFactorController::class, 'setup'])->name('setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
        Route::get('/verify', [TwoFactorController::class, 'verify'])->name('verify');
        Route::post('/check', [TwoFactorController::class, 'check'])->name('check');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
    });

});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
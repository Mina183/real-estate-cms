<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Models
use App\Models\DataRoomFolder;
use App\Models\DataRoomDocument;
use App\Models\Investor;
use App\Models\CapitalCall;
use App\Models\Distribution;
use App\Models\PaymentTransaction;

// Policies
use App\Policies\DataRoomPolicy;
use App\Policies\InvestorPolicy;
use App\Policies\CapitalCallPolicy;
use App\Policies\DistributionPolicy;
use App\Policies\PaymentTransactionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Data Room Policies
        DataRoomFolder::class => DataRoomPolicy::class,
        DataRoomDocument::class => DataRoomPolicy::class,
        
        // Phase 4 - Investment Management Policies
        Investor::class => InvestorPolicy::class,
        CapitalCall::class => CapitalCallPolicy::class,
        Distribution::class => DistributionPolicy::class,
        PaymentTransaction::class => PaymentTransactionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // ============================================
        // GATES (General Permissions)
        // ============================================
        
        // Admin dashboard access
        Gate::define('access-admin-dashboard', function ($user) {
            return in_array($user->role, ['superadmin', 'admin']);
        });
        
        // User management
        Gate::define('manage-users', function ($user) {
            return $user->role === 'superadmin';
        });
        
        // Data room upload (general)
        Gate::define('upload-documents', function ($user) {
            return in_array($user->role, [
                'superadmin',
                'admin',
                'data_room_administrator',
                'document_owner',
            ]);
        });
        
        // Reporting access
        Gate::define('view-reports', function ($user) {
            return in_array($user->role, [
                'superadmin',
                'admin',
                'auditor',
                'compliance_officer',
            ]);
        });
        
        // Export data
        Gate::define('export-data', function ($user) {
            return in_array($user->role, [
                'superadmin',
                'admin',
                'auditor',
            ]);
        });
        
        // Approve investors
        Gate::define('approve-investors', function ($user) {
            return in_array($user->role, [
                'superadmin',
                'admin',
                'compliance_officer',
            ]);
        });
        
        // Manage capital calls
        Gate::define('manage-capital-calls', function ($user) {
            return in_array($user->role, [
                'superadmin',
                'admin',
                'operations',
            ]);
        });
        
        // Manage distributions
        Gate::define('manage-distributions', function ($user) {
            return in_array($user->role, [
                'superadmin',
                'admin',
                'operations',
            ]);
        });
    }
}
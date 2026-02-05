<?php

namespace App\Policies;

use App\Models\Investor;
use App\Models\User;

class InvestorPolicy
{
    /**
     * Determine if user can view any investors
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'superadmin',
            'compliance_officer',
            'relationship_manager',
            'auditor',
            'operations',
        ]);
    }

    /**
     * Determine if user can view specific investor
     */
    public function view(User $user, Investor $investor): bool
    {
        // Admin and compliance can view all
        if (in_array($user->role, ['admin', 'superadmin', 'compliance_officer'])) {
            return true;
        }

        // Relationship manager can view assigned investors
        if ($user->role === 'relationship_manager') {
            return $investor->assigned_to_user_id === $user->id;
        }

        // Investors can view their own record
        if (str_starts_with($user->role, 'investor_')) {
            return $investor->id === $user->investor_id;
        }

        return false;
    }

    /**
     * Determine if user can create investors
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'superadmin',
            'relationship_manager',
        ]);
    }

    /**
     * Determine if user can update investor
     */
    public function update(User $user, Investor $investor): bool
    {
        // Admin and compliance can update all
        if (in_array($user->role, ['admin', 'superadmin', 'compliance_officer'])) {
            return true;
        }

        // Relationship manager can update assigned investors
        if ($user->role === 'relationship_manager') {
            return $investor->assigned_to_user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if user can delete investor
     */
    public function delete(User $user, Investor $investor): bool
    {
        // Only admin and superadmin can delete
        return in_array($user->role, ['admin', 'superadmin']);
    }

    /**
     * Determine if user can change investor stage
     */
    public function changeStage(User $user, Investor $investor): bool
    {
        return in_array($user->role, [
            'admin',
            'superadmin',
            'compliance_officer',
        ]);
    }

    /**
     * Determine if user can approve investor
     */
    public function approve(User $user, Investor $investor): bool
    {
        return in_array($user->role, [
            'admin',
            'superadmin',
            'compliance_officer',
        ]);
    }
}
<?php

namespace App\Policies;

use App\Models\CapitalCall;
use App\Models\User;

class CapitalCallPolicy
{
    /**
     * Determine if the user can view any capital calls
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view capital calls list
        return in_array($user->role, [
            'superadmin',
            'admin',
            'operations',
            'compliance_officer',
            'auditor',
            'relationship_manager',
        ]);
    }

    /**
     * Determine if the user can view the capital call
     */
    public function view(User $user, CapitalCall $capitalCall): bool
    {
        // Same as viewAny
        return $this->viewAny($user);
    }

    /**
     * Determine if the user can create capital calls
     */
    public function create(User $user): bool
    {
        // Only admin and operations can create capital calls
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }

    /**
     * Determine if the user can update the capital call
     */
    public function update(User $user, CapitalCall $capitalCall): bool
    {
        // Only admin and operations can update capital calls
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }

    /**
     * Determine if the user can delete the capital call
     */
    public function delete(User $user, CapitalCall $capitalCall): bool
    {
        // Only superadmin and admin can delete capital calls
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine if the user can issue a capital call
     */
    public function issue(User $user, CapitalCall $capitalCall): bool
    {
        // Only admin and operations can issue capital calls
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }
}
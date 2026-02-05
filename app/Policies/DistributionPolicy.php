<?php

namespace App\Policies;

use App\Models\Distribution;
use App\Models\User;

class DistributionPolicy
{
    /**
     * Determine if the user can view any distributions
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view distributions list
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
     * Determine if the user can view the distribution
     */
    public function view(User $user, Distribution $distribution): bool
    {
        // Same as viewAny
        return $this->viewAny($user);
    }

    /**
     * Determine if the user can create distributions
     */
    public function create(User $user): bool
    {
        // Only admin and operations can create distributions
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }

    /**
     * Determine if the user can update the distribution
     */
    public function update(User $user, Distribution $distribution): bool
    {
        // Only admin and operations can update distributions
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }

    /**
     * Determine if the user can delete the distribution
     */
    public function delete(User $user, Distribution $distribution): bool
    {
        // Only superadmin and admin can delete distributions
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine if the user can issue a distribution
     */
    public function issue(User $user, Distribution $distribution): bool
    {
        // Only admin and operations can issue distributions
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }
}
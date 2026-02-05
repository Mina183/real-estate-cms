<?php

namespace App\Policies;

use App\Models\PaymentTransaction;
use App\Models\User;

class PaymentTransactionPolicy
{
    /**
     * Determine if the user can view any payment transactions
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view payment transactions
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
     * Determine if the user can view the payment transaction
     */
    public function view(User $user, PaymentTransaction $paymentTransaction): bool
    {
        // Same as viewAny
        return $this->viewAny($user);
    }

    /**
     * Determine if the user can create payment transactions
     */
    public function create(User $user): bool
    {
        // Only admin and operations can create payment transactions
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }

    /**
     * Determine if the user can update the payment transaction
     */
    public function update(User $user, PaymentTransaction $paymentTransaction): bool
    {
        // Only admin and operations can update payment transactions
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }

    /**
     * Determine if the user can delete the payment transaction
     */
    public function delete(User $user, PaymentTransaction $paymentTransaction): bool
    {
        // Only superadmin and admin can delete payment transactions
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine if the user can mark payment as paid
     */
    public function markAsPaid(User $user, PaymentTransaction $paymentTransaction): bool
    {
        // Only admin and operations can mark payments as paid
        return in_array($user->role, ['superadmin', 'admin', 'operations']);
    }
}
<?php

namespace App\Policies;

use App\Models\EmailDraft;
use App\Models\User;

class EmailDraftPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'fund_manager', 'operations', 'relationship_manager']);
    }

    public function update(User $user, EmailDraft $draft): bool
    {
        // Admin/superadmin can edit any draft
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }
        // Creator can edit only their own drafts that are not yet sent
        return $draft->created_by_user_id === $user->id && $draft->status !== 'sent';
    }

    public function approve(User $user, EmailDraft $draft): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function send(User $user, EmailDraft $draft): bool
    {
        // Only creator can send, and only if approved
        return $draft->created_by_user_id === $user->id && $draft->status === 'approved';
    }
}
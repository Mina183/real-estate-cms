<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DataRoomDocument;
use App\Models\DataRoomFolder;

class DataRoomPolicy
{
    /**
     * Determine if user can view document
     */
    public function view(User $user, DataRoomDocument $document): bool
    {
        // Everyone can view documents they have access to
        // (actual folder-level access will be checked separately in Phase 5)
        return true;
    }

    /**
     * Determine if user can download document
     */
    public function download(User $user, DataRoomDocument $document): bool
    {
        // For now: all authenticated users can download
        // In Phase 5 we'll add folder-level security checks based on role
        return true;
    }

    /**
     * Determine if user can upload documents
     */
    public function upload(User $user): bool
    {
        return in_array($user->role, [
            'superadmin',
            'admin',
            'operations',
            'compliance_officer',
        ]);
    }

    /**
     * Determine if user can delete document
     */
    public function delete(User $user, DataRoomDocument $document): bool
    {
        return in_array($user->role, [
            'superadmin',
            'admin',
        ]);
    }
}
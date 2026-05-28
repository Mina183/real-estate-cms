<?php

namespace App\Policies;

use App\Helpers\DataRoomHelper;
use App\Models\User;
use App\Models\DataRoomDocument;
use App\Models\DataRoomFolder;

class DataRoomPolicy
{
    public function view(User $user, DataRoomDocument $document): bool
    {
        return DataRoomHelper::canAccessDocument($user, $document);
    }

    public function download(User $user, DataRoomDocument $document): bool
    {
        return DataRoomHelper::canDownloadDocument($user, $document);
    }

    /**
     * Determine if user can upload documents
     */
    public function upload(User $user): bool
    {
        return in_array($user->role, [
            'superadmin',
            'admin',
            'relationship_manager',
            'fund_manager',
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

    public function approve(User $user, DataRoomDocument $document): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'compliance_officer']);
    }
}
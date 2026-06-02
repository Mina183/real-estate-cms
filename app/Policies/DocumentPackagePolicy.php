<?php

namespace App\Policies;

use App\Models\DocumentPackage;
use App\Models\User;

class DocumentPackagePolicy
{
    private function canManage(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'relationship_manager', 'fund_manager', 'operations']);
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, DocumentPackage $documentPackage): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, DocumentPackage $documentPackage): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, DocumentPackage $documentPackage): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }
}

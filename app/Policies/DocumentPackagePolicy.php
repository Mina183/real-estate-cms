<?php

namespace App\Policies;

use App\Models\DocumentPackage;
use App\Models\User;

class DocumentPackagePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function view(User $user, DocumentPackage $documentPackage): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function update(User $user, DocumentPackage $documentPackage): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function delete(User $user, DocumentPackage $documentPackage): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $pendingUsers = User::where('is_approved', false)->get();

        return view('admin.approvals.index', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        $user->update([
            'is_approved' => true,
            'role' => $user->requested_role ?? User::ROLE_CHANNEL_PARTNER, // fallback to 'sub partner' if none
        ]);

        return redirect()->route('approve_users')->with('success', 'User approved and role assigned.');
    }
}

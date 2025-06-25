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
        $user->update(['is_approved' => true]);

        return redirect()->route('approve_users')->with('success', 'User approved.');
    }
}

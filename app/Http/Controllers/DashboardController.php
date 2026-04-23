<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PartnerDocument;
use App\Models\PartnerDocumentResponse;
use App\Models\User;
use App\Models\Meeting;

class DashboardController extends Controller
{
public function index()
{
    $user = Auth::user();

    if (!$user->is_approved) {
        return redirect()->route('approval.pending');
    }

    // Investment Fund CRM Dashboard
    $stats = [
        'totalInvestors'      => \App\Models\Investor::notViewers()->count(),
        'activeInvestors'     => \App\Models\Investor::notViewers()->where('lifecycle_status', 'active')->count(),
        'prospectInvestors'   => \App\Models\Investor::notViewers()->where('stage', 'prospect')->count(),
        'activeStageInvestors'=> \App\Models\Investor::notViewers()->where('stage', 'active')->count(),
    ];

    // Recent investors
    $recentInvestors = \App\Models\Investor::notViewers()
        ->with(['fund', 'assignedTo'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // User-specific data
    if ($user->role === 'relationship_manager') {
        $myInvestors = \App\Models\Investor::notViewers()
            ->where('assigned_to_user_id', $user->id)
            ->with('fund')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    } else {
        $myInvestors = collect();
    }

    return view('dashboard', [
        'user' => $user,
        'stats' => $stats,
        'recentInvestors' => $recentInvestors,
        'myInvestors' => $myInvestors,
    ]);
}
}
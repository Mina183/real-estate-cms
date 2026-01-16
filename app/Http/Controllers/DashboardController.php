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

    // Temporary simplified dashboard for Investment Fund CRM
    return view('dashboard', [
        'user' => $user,
        'documentRedDot' => false,
        'calendarRedDot' => false,
        'adminTriggeringDocs' => collect(),
        'partnerTriggeringDocs' => collect(),
        'upcomingAcceptedMeetings' => collect(),
        'pendingMeetingInvitations' => collect(),
        'adminUpcomingMeetings' => collect(),
        'pendingProposals' => collect(),
        'proposalsRedDot' => false,
    ]);
}
}
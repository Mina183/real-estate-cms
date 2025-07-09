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

    if (! $user->is_approved) {
        return redirect()->route('approval.pending');
    }

    // === Initialize Red Dot Flags and Data Collections ===
    $documentRedDot = false;
    $calendarRedDot = false;
    $adminTriggeringDocs = collect();
    $partnerTriggeringDocs = collect();

    // === CALENDAR RED DOT: Any pending invitation? ===
    $calendarRedDot = $user->meetings()
        ->wherePivotNull('is_accepted')
        ->where('start_time', '>', now())
        ->exists();

    // === PARTNER DOCUMENTS LOGIC ===
    if ($user->role === 'channel_partner') {
        $partnerId = $user->id;

        // Direct documents assigned to this partner
        $directDocs = PartnerDocument::where('partner_id', $partnerId)
            ->whereIn('status', ['waiting_partner_action', 'review_only'])
            ->get();

        $partnerTriggeringDocs = $partnerTriggeringDocs->merge($directDocs);

        // Shared documents requiring action
        $sharedDocs = PartnerDocument::whereNull('partner_id')->get();

        foreach ($sharedDocs as $doc) {
            $response = PartnerDocumentResponse::where('document_id', $doc->id)
                ->where('partner_id', $partnerId)
                ->first();

            if (! $response || in_array($response->status, ['waiting_partner_action', 'review_only', null])) {
                $partnerTriggeringDocs->push($doc);
            }
        }

        $documentRedDot = $partnerTriggeringDocs->isNotEmpty();
    }

    // === ADMIN DOCUMENTS LOGIC ===
    $adminUpcomingMeetings = collect();

    if (in_array($user->role, ['admin', 'superadmin'])) {
        $adminUpcomingMeetings = Meeting::where('start_time', '>', now())
            ->with(['attendees' => function ($query) {
                $query->select('users.id', 'name')->withPivot('is_accepted');
            }])
            ->orderBy('start_time')
            ->get();

        $partners = User::where('role', 'channel_partner')->get();
        $sharedDocs = PartnerDocument::whereNull('partner_id')->get();

        foreach ($sharedDocs as $doc) {
            foreach ($partners as $partner) {
                $response = PartnerDocumentResponse::where('document_id', $doc->id)
                    ->where('partner_id', $partner->id)
                    ->first();

                if (! $response || in_array($response->status, [
                    'waiting_partner_action',
                    'review_only',
                    'waiting_admin_approval',
                    null
                ])) {
                    if (! $adminTriggeringDocs->contains('id', $doc->id)) {
                        $adminTriggeringDocs->push($doc);
                    }
                }
            }
        }

        $documentRedDot = $adminTriggeringDocs->isNotEmpty();
    }

    // === MEETING LISTS FOR PARTNERS ===
    $upcomingAcceptedMeetings = collect();
    $pendingMeetingInvitations = collect();

    if ($user->role === 'channel_partner') {
        $upcomingAcceptedMeetings = $user->meetings()
            ->wherePivot('is_accepted', 1)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();

        $pendingMeetingInvitations = $user->meetings()
            ->wherePivotNull('is_accepted')
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
    }

    return view('dashboard', [
        'user' => $user,
        'documentRedDot' => $documentRedDot,
        'calendarRedDot' => $calendarRedDot,
        'adminTriggeringDocs' => $adminTriggeringDocs,
        'partnerTriggeringDocs' => $partnerTriggeringDocs,
        'upcomingAcceptedMeetings' => $upcomingAcceptedMeetings,
        'pendingMeetingInvitations' => $pendingMeetingInvitations,
        'adminUpcomingMeetings' => $adminUpcomingMeetings,
    ]);
}
}
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PartnerDocument;
use App\Models\PartnerDocumentResponse;
use App\Models\User;

class DashboardController extends Controller
{
public function index()
{
    $user = Auth::user();

    if (! $user->is_approved) {
        return redirect()->route('approval.pending');
    }

    $showRedDot = false;
    $adminTriggeringDocs = collect();
    $partnerTriggeringDocs = collect();

    // ==== Partner Red Dot Logic ====
    if ($user->role === 'channel_partner') {
        $partnerId = $user->id;

        // Direct documents that require partner's action
        $directDocs = PartnerDocument::where('partner_id', $partnerId)
            ->whereIn('status', ['waiting_partner_action', 'review_only'])
            ->get();

        foreach ($directDocs as $doc) {
            $partnerTriggeringDocs->push($doc);
        }

        // Shared documents that this partner hasn't responded to or only partially responded
        $sharedDocs = PartnerDocument::whereNull('partner_id')->get();

        foreach ($sharedDocs as $doc) {
            $response = PartnerDocumentResponse::where('document_id', $doc->id)
                ->where('partner_id', $partnerId)
                ->first();

            if (! $response || in_array($response->status, ['waiting_partner_action', 'review_only', null])) {
                $partnerTriggeringDocs->push($doc);
            }
        }

        $showRedDot = $partnerTriggeringDocs->isNotEmpty();
    }

    // ==== Admin Red Dot Logic (mirrors create() logic exactly) ====
    if (in_array($user->role, ['admin', 'superadmin'])) {
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
                    'waiting_admin_approval', // ✅ NEW: admin-triggered status
                    null
                ])) {
                    if (! $adminTriggeringDocs->contains('id', $doc->id)) {
                        $adminTriggeringDocs->push($doc);
                    }
                }
            }
        }

        $showRedDot = $showRedDot || $adminTriggeringDocs->isNotEmpty();
    }

    return view('dashboard', [
        'user' => $user,
        'showRedDot' => $showRedDot,
        'adminTriggeringDocs' => $adminTriggeringDocs,
        'partnerTriggeringDocs' => $partnerTriggeringDocs,
    ]);
}
}
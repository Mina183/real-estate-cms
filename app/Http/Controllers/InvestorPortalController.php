<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CapitalCall;
use App\Models\Distribution;

class InvestorPortalController extends Controller
{
    /**
     * Display investor dashboard
     */
    public function dashboard()
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        if (!$investor) {
            return redirect()->route('investor.login')
                ->with('error', 'Investor account not found');
        }

        $stats = [
            'commitment' => $investor->final_commitment_amount ?? $investor->target_commitment_amount ?? 0,
            'funded'     => $investor->funded_amount ?? 0,
            'currency'   => $investor->currency ?? 'USD',
            'stage'      => $investor->stage,
            'status'     => $investor->status,
        ];

        $stats['funded_percentage'] = $stats['commitment'] > 0
            ? round(($stats['funded'] / $stats['commitment']) * 100, 1)
            : 0;

        $capitalCallPayments = \App\Models\PaymentTransaction::where('investor_id', $investor->id)
            ->where('transactionable_type', 'App\\Models\\CapitalCall')
            ->with('transactionable')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $distributionPayments = \App\Models\PaymentTransaction::where('investor_id', $investor->id)
            ->where('transactionable_type', 'App\\Models\\Distribution')
            ->with('transactionable')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalCapitalCalled = \App\Models\PaymentTransaction::where('investor_id', $investor->id)
            ->where('transactionable_type', 'App\\Models\\CapitalCall')
            ->sum('amount');

        $totalDistributed = \App\Models\PaymentTransaction::where('investor_id', $investor->id)
            ->where('transactionable_type', 'App\\Models\\Distribution')
            ->where('status', 'paid')
            ->sum('amount');

        $pendingCapitalCalls = \App\Models\PaymentTransaction::where('investor_id', $investor->id)
            ->where('transactionable_type', 'App\\Models\\CapitalCall')
            ->whereIn('status', ['pending', 'issued'])
            ->sum('amount');

        return view('investor.dashboard', compact(
            'investor',
            'stats',
            'capitalCallPayments',
            'distributionPayments',
            'totalCapitalCalled',
            'totalDistributed',
            'pendingCapitalCalls'
        ));
    }

    /**
     * Display investor profile
     */
    public function profile()
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        return view('investor.profile', compact('investor', 'investorUser'));
    }

    /**
     * Show compliance gate (first-run acknowledgement)
     */
    public function complianceGate()
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        // If already completed gate, redirect to documents
        if ($this->hasCompletedGate($investor)) {
            return redirect()->route('investor.documents');
        }

        return view('investor.compliance-gate', compact('investor'));
    }

    /**
     * Process compliance gate submission
     */
    public function complianceGateSubmit(Request $request)
    {
        $request->validate([
            'confirmed_professional_client' => 'accepted',
            'agreed_confidentiality'        => 'accepted',
            'acknowledged_ppm_confidential' => 'accepted',
        ], [
            'confirmed_professional_client.accepted' => 'You must confirm you are a Professional Client.',
            'agreed_confidentiality.accepted'        => 'You must agree to the confidentiality terms.',
            'acknowledged_ppm_confidential.accepted' => 'You must acknowledge the PPM confidentiality notice.',
        ]);

        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        $now = now();
        $updates = [
            'confirmed_professional_client'    => true,
            'confirmed_professional_client_at' => $investor->confirmed_professional_client_at ?? $now,
            'agreed_confidentiality'           => true,
            'agreed_confidentiality_at'        => $investor->agreed_confidentiality_at ?? $now,
            'acknowledged_ppm_confidential'    => true,
            'acknowledged_ppm_confidential_at' => $investor->acknowledged_ppm_confidential_at ?? $now,
        ];

        // Optional risk warnings
        if ($request->boolean('acknowledged_risk_warnings')) {
            $updates['acknowledged_risk_warnings']    = true;
            $updates['acknowledged_risk_warnings_at'] = $investor->acknowledged_risk_warnings_at ?? $now;
        }

        $investor->update($updates);

        // Log to activity log
        app(\App\Services\DataRoomService::class)->logActivity(
            $investor,
            null,
            null,
            'compliance_gate_accepted',
            [
                'confirmed_professional_client' => true,
                'agreed_confidentiality'        => true,
                'acknowledged_ppm_confidential' => true,
                'acknowledged_risk_warnings'    => $request->boolean('acknowledged_risk_warnings'),
                'ip_address'                    => $request->ip(),
                'user_agent'                    => $request->userAgent(),
            ]
        );

        return redirect()->route('investor.documents')
            ->with('success', 'Thank you for confirming. You now have access to the documents.');
    }

    /**
     * Display investor documents
     */
    public function documents()
    {
        $investorUser = Auth::guard('investor')->user();
        $investor     = $investorUser->investor;

        if (!$this->hasCompletedGate($investor)) {
            return redirect()->route('investor.compliance-gate');
        }

        $accessLevel    = $investor->data_room_access_level ?? 'none';
        $allowedFolders = config("dataroom.investor_folder_access.{$accessLevel}", []);

        $folderQuery = \App\Models\DataRoomFolder::whereNull('parent_folder_id')
            ->whereNull('investor_id')
            ->where('is_active', true);

        if ($allowedFolders !== '*' && !empty($allowedFolders)) {
            $folderQuery->whereIn('folder_number', $allowedFolders);
        } elseif (empty($allowedFolders)) {
            $folderQuery->whereRaw('1 = 0'); // no access
        }

        $folders = $folderQuery
            ->with([
                'children.documents' => fn($q) => $q->where('status', 'approved')->whereNull('investor_id'),
                'documents'          => fn($q) => $q->where('status', 'approved')->whereNull('investor_id'),
            ])
            ->orderBy('order')
            ->get();

        // Investor personal folder — auto-created per investor
        $personalFolder = \App\Models\DataRoomFolder::where('investor_id', $investor->id)
            ->whereNull('parent_folder_id')
            ->with([
                'documents'       => fn($q) => $q->where('status', 'approved'),
                'children.documents' => fn($q) => $q->where('status', 'approved'),
            ])
            ->first();

        return view('investor.documents', compact('investor', 'folders', 'accessLevel', 'personalFolder'));
    }

    /**
     * Download a document
     */
    public function downloadDocument($documentId)
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        if (!$this->hasCompletedGate($investor)) {
            return redirect()->route('investor.compliance-gate');
        }

        $document = \App\Models\DataRoomDocument::with('folder')->findOrFail($documentId);
        $folder   = $document->folder;

        $accessLevel    = $investor->data_room_access_level ?? 'none';
        $allowedFolders = config("dataroom.investor_folder_access.{$accessLevel}", []);

        // Personal folder: folder belongs to this investor (auto-created system)
        if ($folder->investor_id !== null) {
            if ($folder->investor_id !== $investor->id) {
                abort(403, 'You do not have access to this document.');
            }
        } else {
            $allowed = $allowedFolders === '*' || in_array($folder->folder_number, (array) $allowedFolders);
            if (!$allowed) {
                abort(403, 'You do not have access to this document.');
            }
        }

        if ($document->status !== 'approved') {
            abort(403, 'This document is not available.');
        }

        if (!\Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        app(\App\Services\DataRoomService::class)->logActivity(
            $investor,
            $document->id,
            $folder->id,
            'download',
            ['downloaded_by' => $investorUser->id]
        );

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        $mimeType = $mimeTypes[$document->file_type] ?? 'application/octet-stream';

        return \Storage::disk('private')->download(
            $document->file_path,
            $document->document_name . '.' . $document->file_type,
            ['Content-Type' => $mimeType]
        );
    }

    /**
     * Check if investor has completed the compliance gate
     */
    private function hasCompletedGate(\App\Models\Investor $investor): bool
    {
        return $investor->confirmed_professional_client
            && $investor->agreed_confidentiality
            && $investor->acknowledged_ppm_confidential;
    }
}
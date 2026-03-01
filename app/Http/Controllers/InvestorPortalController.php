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
        $investor = $investorUser->investor;

        // Redirect to gate if not completed
        if (!$this->hasCompletedGate($investor)) {
            return redirect()->route('investor.compliance-gate');
        }

        $accessLevel = $investor->data_room_access_level ?? 'none';

        $allowedFolderLevels = match($accessLevel) {
            'prospect'   => ['public'],
            'qualified'  => ['public', 'restricted'],
            'subscribed' => ['public', 'restricted', 'confidential'],
            default      => [],
        };

        $folders = collect();

        if (!empty($allowedFolderLevels)) {
            $folders = \App\Models\DataRoomFolder::whereNull('parent_folder_id')
                ->whereIn('access_level', $allowedFolderLevels)
                ->where('is_active', true)
                ->with(['children' => function ($q) use ($allowedFolderLevels) {
                    $q->whereIn('access_level', $allowedFolderLevels)
                        ->where('is_active', true)
                        ->with(['documents' => function ($q) {
                            $q->where('status', 'approved')->whereNull('investor_id');
                        }]);
                }])
                ->with(['documents' => function ($q) {
                    $q->where('status', 'approved')->whereNull('investor_id');
                }])
                ->orderBy('folder_number')
                ->get();

            // Folder 12 — samo za subscribed, samo njihovi dokumenti
            if ($accessLevel === 'subscribed') {
                $folder12 = \App\Models\DataRoomFolder::where('folder_number', '12')
                    ->with(['documents' => function ($q) use ($investor) {
                        $q->where('status', 'approved')
                            ->where(function ($q) use ($investor) {
                                $q->where('investor_id', $investor->id)->orWhereNull('investor_id');
                            });
                    }, 'children' => function ($q) use ($investor) {
                        $q->with(['documents' => function ($q) use ($investor) {
                            $q->where('status', 'approved')
                                ->where(function ($q) use ($investor) {
                                    $q->where('investor_id', $investor->id)->orWhereNull('investor_id');
                                });
                        }]);
                    }])
                    ->first();

                if ($folder12) {
                    $folders->push($folder12);
                }
            }
        }

        return view('investor.documents', compact('investor', 'folders', 'accessLevel'));
    }

    /**
     * Download a document
     */
    public function downloadDocument($documentId)
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        // Must have completed compliance gate
        if (!$this->hasCompletedGate($investor)) {
            return redirect()->route('investor.compliance-gate');
        }

        $document = \App\Models\DataRoomDocument::with('folder')->findOrFail($documentId);
        $folder = $document->folder;

        // Build allowed levels for this investor
        $accessLevel = $investor->data_room_access_level ?? 'none';
        $allowedFolderLevels = match($accessLevel) {
            'prospect'   => ['public'],
            'qualified'  => ['public', 'restricted'],
            'subscribed' => ['public', 'restricted', 'confidential'],
            default      => [],
        };

        // Check folder access level
        $isFolder12 = str_starts_with($folder->folder_number, '12');

        if ($isFolder12) {
            // Folder 12 — only subscribed + only their docs or general docs
            if ($accessLevel !== 'subscribed') {
                abort(403, 'You do not have access to this document.');
            }
            if ($document->investor_id && $document->investor_id !== $investor->id) {
                abort(403, 'You do not have access to this document.');
            }
        } else {
            // All other folders — check folder access_level against investor's allowed levels
            if (!in_array($folder->access_level, $allowedFolderLevels)) {
                abort(403, 'You do not have access to this document.');
            }
        }

        // Document must be approved
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
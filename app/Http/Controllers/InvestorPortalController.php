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

        // Portfolio stats
        $stats = [
            'commitment' => $investor->final_commitment_amount ?? $investor->target_commitment_amount ?? 0,
            'funded' => $investor->funded_amount ?? 0,
            'currency' => $investor->currency ?? 'USD',
            'stage' => $investor->stage,
            'status' => $investor->status,
        ];

        // Calculate funded percentage
        $stats['funded_percentage'] = $stats['commitment'] > 0 
            ? round(($stats['funded'] / $stats['commitment']) * 100, 1)
            : 0;

        // TEMPORARY — Capital calls and distributions disabled until we add investor_id to tables
        $capitalCalls = collect(); // Empty collection
        $distributions = collect(); // Empty collection
        $totalCapitalCalled = 0;
        $totalDistributed = 0;
        $pendingCapitalCalls = 0;

        // TODO: Re-enable when capital_calls and distributions tables have investor_id column
        // $capitalCalls = CapitalCall::where('investor_id', $investor->id)
        //     ->orderBy('due_date', 'desc')
        //     ->limit(5)
        //     ->get();
        // $distributions = Distribution::where('investor_id', $investor->id)
        //     ->orderBy('distribution_date', 'desc')
        //     ->limit(5)
        //     ->get();
        // $totalCapitalCalled = CapitalCall::where('investor_id', $investor->id)->sum('amount');
        // $totalDistributed = Distribution::where('investor_id', $investor->id)
        //     ->where('status', 'processed')->sum('amount');
        // $pendingCapitalCalls = CapitalCall::where('investor_id', $investor->id)
        //     ->where('status', 'issued')->sum('amount');

        return view('investor.dashboard', compact(
            'investor',
            'stats',
            'capitalCalls',
            'distributions',
            'totalCapitalCalled',
            'totalDistributed',
            'pendingCapitalCalls'
        ));
    }

    /**
     * Display investor profile/account information
     */
    public function profile()
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        return view('investor.profile', compact('investor', 'investorUser'));
    }

    /**
     * Display investor documents (Data Room access)
     */
    public function documents()
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        // Get investor's access level
        $accessLevel = $investor->data_room_access_level ?? 'none';

        // Get accessible folders based on access level
        // For now, show Section 12 (investor-specific) if they have 'subscribed' access
        $folders = collect();
        
        if ($accessLevel === 'subscribed' || $accessLevel === 'qualified') {
            // Section 12: Investor-Specific Documents
            $folders = \App\Models\DataRoomFolder::where('folder_number', 'LIKE', '12%')
                ->with(['documents' => function($query) {
                    $query->where('status', 'approved');
                }])
                ->orderBy('folder_number')
                ->get();
        }

        return view('investor.documents', compact('investor', 'folders', 'accessLevel'));
    }

    /**
     * Download a document (investor-specific)
     */
    public function downloadDocument($documentId)
    {
        $investorUser = Auth::guard('investor')->user();
        $investor = $investorUser->investor;

        $document = \App\Models\DataRoomDocument::findOrFail($documentId);

        // Check if investor has access to this document's folder
        $folder = $document->folder;
        
        // Only allow Section 12 documents
        if (!str_starts_with($folder->folder_number, '12')) {
            abort(403, 'You do not have access to this document');
        }

        // Check if file exists
        if (!\Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        // Log document access (for audit trail)
        \Log::info('Investor document download', [
            'investor_id' => $investor->id,
            'document_id' => $document->id,
            'document_name' => $document->document_name
        ]);

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $mimeType = $mimeTypes[$document->file_type] ?? 'application/octet-stream';

        return \Storage::disk('private')->download(
            $document->file_path,
            $document->document_name,
            ['Content-Type' => $mimeType]
        );
    }
}
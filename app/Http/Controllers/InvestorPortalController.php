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

            // Get payment transactions for this investor (capital calls)
            $capitalCallPayments = \App\Models\PaymentTransaction::where('investor_id', $investor->id)
                ->where('transactionable_type', 'App\\Models\\CapitalCall')
                ->with('transactionable')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Get payment transactions for distributions
            $distributionPayments = \App\Models\PaymentTransaction::where('investor_id', $investor->id)
                ->where('transactionable_type', 'App\\Models\\Distribution')
                ->with('transactionable')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Calculate totals from payment transactions
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
                ->with(['children' => function($q) use ($allowedFolderLevels, $investor) {
                    $q->whereIn('access_level', $allowedFolderLevels)
                    ->where('is_active', true)
                    ->with(['documents' => function($q) use ($investor) {
                        $q->where('status', 'approved')
                            ->whereNull('investor_id'); // subfolder dokumenti nikad investor-specific
                    }]);
                }])
                ->with(['documents' => function($q) use ($investor) {
                    $q->where('status', 'approved')
                    ->whereNull('investor_id');
                }])
                ->orderBy('display_order')
                ->get();

            // Dodaj Folder 12 za subscribed - samo njihovi dokumenti
            if ($accessLevel === 'subscribed') {
                $folder12 = \App\Models\DataRoomFolder::where('folder_number', '12')
                    ->with(['documents' => function($q) use ($investor) {
                        $q->where('status', 'approved')
                        ->where(function($q) use ($investor) {
                            $q->where('investor_id', $investor->id)
                                ->orWhereNull('investor_id');
                        });
                    }, 'children' => function($q) use ($investor) {
                        $q->with(['documents' => function($q) use ($investor) {
                            $q->where('status', 'approved')
                            ->where(function($q) use ($investor) {
                                $q->where('investor_id', $investor->id)
                                    ->orWhereNull('investor_id');
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
        ];

        $mimeType = $mimeTypes[$document->file_type] ?? 'application/octet-stream';

        return \Storage::disk('private')->download(
            $document->file_path,
            $document->document_name,
            ['Content-Type' => $mimeType]
        );
    }
}
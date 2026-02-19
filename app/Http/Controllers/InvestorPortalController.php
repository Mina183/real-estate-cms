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

        // Get investor-accessible folders based on access level
        // This will be implemented when we add Data Room investor access
        
        return view('investor.documents', compact('investor'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Fund;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\InvestorStageService;

class InvestorController extends Controller
{
    /**
     * Apply Policy authorization to all resource methods
     * 
     * Automatically maps:
     * - index()   → viewAny()
     * - create()  → create()
     * - store()   → create()
     * - show()    → view()
     * - edit()    → update()
     * - update()  → update()
     * - destroy() → delete()
     */
    public function __construct()
    {
        $this->authorizeResource(Investor::class, 'investor');
    }

    /**
     * Display a listing of investors
     */
    public function index()
    {
        $investors = Investor::with(['fund', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('investors.index', compact('investors'));
    }

    /**
     * Show the form for creating a new investor
     */
    public function create()
    {
        $funds = Fund::where('status', 'active')->get();
        $users = User::whereIn('role', ['admin', 'relationship_manager'])->get();

        return view('investors.create', compact('funds', 'users'));
    }

    /**
     * Store a newly created investor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_type' => 'required|in:individual,corporate,family_office,spv,fund',
            'organization_name' => 'nullable|string|max:255',
            'legal_entity_name' => 'nullable|string|max:255',
            'jurisdiction' => 'required|string|max:100',
            'fund_id' => 'nullable|exists:funds,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'target_commitment_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'source_of_introduction' => 'nullable|in:direct,advisor,placement_agent,referral,event,other',
            'referral_source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Set defaults
        $validated['stage'] = 'prospect';
        $validated['status'] = 'pending';
        $validated['lifecycle_status'] = 'active';
        $validated['created_by_user_id'] = auth()->id();

        $investor = Investor::create($validated);

        return redirect()->route('investors.show', $investor)
            ->with('success', 'Investor created successfully!');
    }

    /**
     * Display the specified investor
     */
    public function show(Investor $investor)
    {
        $investor->load(['fund', 'contacts', 'commitments', 'assignedTo', 'createdBy']);

        return view('investors.show', compact('investor'));
    }

    /**
     * Show the form for editing the specified investor
     */
    public function edit(Investor $investor)
    {
        $funds = Fund::where('status', 'active')->get();
        $users = User::whereIn('role', ['admin', 'relationship_manager'])->get();

        return view('investors.edit', compact('investor', 'funds', 'users'));
    }

    /**
     * Update the specified investor
     */
    public function update(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'investor_type' => 'required|in:individual,corporate,family_office,spv,fund',
            'organization_name' => 'nullable|string|max:255',
            'legal_entity_name' => 'nullable|string|max:255',
            'jurisdiction' => 'required|string|max:100',
            'fund_id' => 'nullable|exists:funds,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'target_commitment_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'source_of_introduction' => 'nullable|in:direct,advisor,placement_agent,referral,event,other',
            'referral_source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',

            // KYC Status - DODAJ OVO!
            'kyc_status' => 'nullable|in:not_started,in_progress,submitted,under_review,complete,rejected',
            
            // Compliance checkboxes
            'is_professional_client' => 'nullable|boolean',
            'sanctions_check_passed' => 'nullable|boolean',
            'bank_account_verified' => 'nullable|boolean',
            'confidentiality_acknowledged' => 'nullable|boolean',
        ]);

        // Convert checkbox values (checkboxes send "1" or null)
        $validated['is_professional_client'] = $request->has('is_professional_client');
        $validated['sanctions_check_passed'] = $request->has('sanctions_check_passed');
        $validated['bank_account_verified'] = $request->has('bank_account_verified');
        $validated['confidentiality_acknowledged'] = $request->has('confidentiality_acknowledged');

        // Set timestamps if checkbox was just checked (and wasn't checked before)
        if ($validated['is_professional_client'] && !$investor->is_professional_client) {
            $validated['professional_client_verified_at'] = now();
        }

        if ($validated['sanctions_check_passed'] && !$investor->sanctions_check_passed) {
            $validated['sanctions_checked_at'] = now();
        }

        if ($validated['bank_account_verified'] && !$investor->bank_account_verified) {
            $validated['bank_verified_at'] = now();
        }

        if ($validated['confidentiality_acknowledged'] && !$investor->confidentiality_acknowledged) {
            $validated['confidentiality_acknowledged_at'] = now();
        }

        $investor->update($validated);

        return redirect()->route('investors.show', $investor)
            ->with('success', 'Investor updated successfully!');
    }

    /**
     * Remove the specified investor (soft delete)
     */
    public function destroy(Investor $investor)
    {
        $investor->delete();

        return redirect()->route('investors.index')
            ->with('success', 'Investor archived successfully!');
    }

    /**
     * Show form to change investor stage
     * Uses explicit authorization check for custom action
     */
    public function changeStageForm(Investor $investor)
    {
        $this->authorize('changeStage', $investor);

        $stages = [
            'prospect' => 'Prospect',
            'eligibility_review' => 'Eligibility Review',
            'ppm_issued' => 'PPM Issued',
            'kyc_in_progress' => 'KYC In Progress',
            'subscription_signed' => 'Subscription Signed',
            'approved' => 'Approved',
            'funded' => 'Funded',
            'active' => 'Active',
            'monitored' => 'Monitored',
        ];

        $stageService = app(\App\Services\InvestorStageService::class);
        $stageRequirements = [];
        
        foreach (array_keys($stages) as $stage) {
            $stageRequirements[$stage] = [
                'requirements' => $stageService->getMissingRequirements($investor, $stage),
                'automation' => $this->getAutomationText($stage)
            ];
        }

        return view('investors.change-stage', compact('investor', 'stages', 'stageRequirements'));
    }

    /**
     * Process stage change
     * Uses explicit authorization check for custom action
     */
    public function changeStage(Request $request, Investor $investor, InvestorStageService $stageService)
    {
        $this->authorize('changeStage', $investor);

        $validated = $request->validate([
            'new_stage' => 'required|in:prospect,eligibility_review,ppm_issued,kyc_in_progress,subscription_signed,approved,funded,active,monitored',
            'reason' => 'nullable|string|max:500',
        ]);

        // Check if move is allowed
        $missingRequirements = $stageService->getMissingRequirements($investor, $validated['new_stage']);

        if (!empty($missingRequirements)) {
            return back()
                ->withErrors(['requirements' => 'Cannot move to this stage. Missing requirements:'])
                ->with('missing_requirements', $missingRequirements);
        }

        // Perform stage transition
        $success = $stageService->moveToStage(
            $investor,
            $validated['new_stage'],
            $validated['reason'] ?? null,
            auth()->id()
        );

        if ($success) {
            return redirect()->route('investors.show', $investor)
                ->with('success', "Investor moved to stage: " . ucfirst(str_replace('_', ' ', $validated['new_stage'])));
        }

        return back()->withErrors(['error' => 'Failed to change stage. Please try again.']);
    }

    /**
     * Show investor activity log
     * Uses explicit authorization check for custom action
     */
    public function activityLog(Investor $investor)
    {
        $this->authorize('view', $investor);

        $activities = \App\Models\DataRoomActivityLog::where('investor_id', $investor->id)
            ->with('user')
            ->orderBy('activity_at', 'desc')
            ->paginate(50);

        $stageTransitions = $investor->stageTransitions()
            ->with('changedBy')
            ->orderBy('transitioned_at', 'desc')
            ->get();

        return view('investors.activity', compact('investor', 'activities', 'stageTransitions'));
    }

    /**
     * Get automation text for stage
     */
    protected function getAutomationText(string $stage): string
    {
        return match($stage) {
            'prospect' => 'No automatic actions',
            'eligibility_review' => 'Sanctions check timestamp recorded',
            'ppm_issued' => 'Data Room access granted (PROSPECT level), PPM issue date recorded',
            'kyc_in_progress' => 'Data Room upgraded to QUALIFIED level',
            'subscription_signed' => 'Subscription date recorded',
            'approved' => 'Approval date and approver recorded',
            'funded' => 'Funding date recorded',
            'active' => 'Data Room upgraded to SUBSCRIBED level, Investor ID generated, Reporting access granted',
            'monitored' => 'Ongoing monitoring enabled',
            default => 'No automatic actions',
        };
    }
}

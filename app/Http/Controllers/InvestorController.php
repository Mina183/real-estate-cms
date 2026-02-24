<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Fund;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\InvestorStageService;

class InvestorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Investor::class, 'investor');
    }

    public function index()
    {
        $investors = Investor::with(['fund', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('investors.index', compact('investors'));
    }

    public function create()
    {
        $funds = Fund::where('status', 'active')->get();
        $users = User::whereIn('role', ['admin', 'relationship_manager'])->get();

        return view('investors.create', compact('funds', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_type' => 'required|in:individual,corporate,family_office,spv,fund',
            'organization_name' => 'nullable|string|max:255',
            'legal_entity_name' => 'nullable|string|max:255',
            'jurisdiction' => 'required|string|max:100',
            'fund_id' => 'nullable|exists:funds,id',
            'assigned_to_user_id' => 'required|exists:users,id', // REQUIRED
            'target_commitment_amount' => 'nullable|numeric|min:1000000', // $1M minimum
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

    public function show(Investor $investor)
    {
        $investor->load(['fund', 'contacts', 'commitments', 'assignedTo', 'createdBy']);

        return view('investors.show', compact('investor'));
    }

    public function edit(Investor $investor)
    {
        $funds = Fund::where('status', 'active')->get();
        $users = User::whereIn('role', ['admin', 'relationship_manager'])->get();

        return view('investors.edit', compact('investor', 'funds', 'users'));
    }

    public function update(Request $request, Investor $investor)
{
    $validated = $request->validate([
        'investor_type' => 'required|in:individual,corporate,family_office,spv,fund',
        'organization_name' => 'nullable|string|max:255',
        'legal_entity_name' => 'nullable|string|max:255',
        'jurisdiction' => 'required|string|max:100',
        'fund_id' => 'nullable|exists:funds,id',
        'assigned_to_user_id' => 'required|exists:users,id',
        'target_commitment_amount' => 'nullable|numeric|min:1000000',
        'final_commitment_amount' => 'nullable|numeric',
        'funded_amount' => 'nullable|numeric',
        'currency' => 'nullable|string|max:3',
        'source_of_introduction' => 'nullable|in:direct,advisor,placement_agent,referral,event,other',
        'referral_source' => 'nullable|string|max:255',
        'notes' => 'nullable|string',

        // Compliance checkboxes
        'is_professional_client' => 'nullable|boolean',
        'sanctions_check_passed' => 'nullable|boolean',
        'bank_account_verified' => 'nullable|boolean',
        'agreed_confidentiality' => 'nullable|boolean',
        'acknowledged_ppm_confidential' => 'nullable|boolean',

        // PPM & Subscription (checkbox → date)
        'ppm_acknowledged' => 'nullable|boolean',
        'subscription_signed' => 'nullable|boolean',

        // KYC
        'kyc_status' => 'nullable|in:not_started,in_progress,submitted,under_review,complete,rejected,expired',

        // Stage 2
        'risk_profile' => 'nullable|in:low,medium,high',
        'investor_experience' => 'nullable|string',

        // Stage 4
        'kyc_risk_rating' => 'nullable|in:low,medium,high',
        'enhanced_due_diligence_required' => 'nullable|boolean',

        // Stage 5
        'side_letter_exists' => 'nullable|boolean',
        'side_letter_terms' => 'nullable|string',
        'legal_review_complete' => 'nullable|boolean',
        'share_class' => 'nullable|string|max:100',

        // Stage 6
        'board_approval_required' => 'nullable|boolean',
        'board_approval_date' => 'nullable|date',
        'admission_notice_issued_date' => 'nullable|date',

        // Stage 8
        'units_allotted' => 'nullable|numeric|min:0',
        'welcome_letter_sent_date' => 'nullable|date',
        'investor_register_updated' => 'nullable|boolean',

        // CRM
        'next_action' => 'nullable|string',
        'next_action_due_date' => 'nullable|date',

        // Stage 9
        'last_kyc_refresh_date' => 'nullable|date',
        'next_kyc_refresh_due' => 'nullable|date',
        'last_sanctions_rescreen_date' => 'nullable|date',
    ]);

    // Convert checkboxes to boolean
    $validated['is_professional_client'] = $request->has('is_professional_client');
    $validated['sanctions_check_passed'] = $request->has('sanctions_check_passed');
    $validated['bank_account_verified'] = $request->has('bank_account_verified');
    $validated['agreed_confidentiality'] = $request->has('agreed_confidentiality');
    $validated['acknowledged_ppm_confidential'] = $request->has('acknowledged_ppm_confidential');
    $validated['enhanced_due_diligence_required'] = $request->has('enhanced_due_diligence_required');
    $validated['side_letter_exists'] = $request->has('side_letter_exists');
    $validated['legal_review_complete'] = $request->has('legal_review_complete');
    $validated['board_approval_required'] = $request->has('board_approval_required');
    $validated['investor_register_updated'] = $request->has('investor_register_updated');

    // Timestamps za boolean polja
    if ($validated['is_professional_client'] && !$investor->is_professional_client) {
        $validated['confirmed_professional_client_at'] = now();
    }
    if ($validated['sanctions_check_passed'] && !$investor->sanctions_check_passed) {
        $validated['sanctions_checked_at'] = now();
    }
    if ($validated['bank_account_verified'] && !$investor->bank_account_verified) {
        $validated['bank_verified_date'] = now();
    }
    if ($validated['agreed_confidentiality'] && !$investor->agreed_confidentiality) {
        $validated['agreed_confidentiality_at'] = now();
    }
    if ($validated['acknowledged_ppm_confidential'] && !$investor->acknowledged_ppm_confidential) {
        $validated['acknowledged_ppm_confidential_at'] = now();
    }

    // PPM acknowledged - checkbox setuje datum
    if ($request->has('ppm_acknowledged') && !$investor->ppm_acknowledged_date) {
        $validated['ppm_acknowledged_date'] = now();
    } elseif (!$request->has('ppm_acknowledged')) {
        $validated['ppm_acknowledged_date'] = null;
    }

    // Subscription signed - checkbox setuje datum
    if ($request->has('subscription_signed') && !$investor->subscription_signed_date) {
        $validated['subscription_signed_date'] = now();
    } elseif (!$request->has('subscription_signed')) {
        $validated['subscription_signed_date'] = null;
    }

    // Ukloni checkbox polja koja ne postoje kao kolone u bazi
    unset($validated['ppm_acknowledged']);
    unset($validated['subscription_signed']);

    $investor->update($validated);

    return redirect()->route('investors.show', $investor)
        ->with('success', 'Investor updated successfully!');
}

    public function destroy(Investor $investor)
    {
        $investor->delete();

        return redirect()->route('investors.index')
            ->with('success', 'Investor archived successfully!');
    }

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
            'monitored' => 'Monitored', // ADDED Stage 9
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

    public function changeStage(Request $request, Investor $investor, InvestorStageService $stageService)
    {
        $this->authorize('changeStage', $investor);

        $validated = $request->validate([
            'new_stage' => 'required|in:prospect,eligibility_review,ppm_issued,kyc_in_progress,subscription_signed,approved,funded,active,monitored', // ADDED monitored
            'reason' => 'nullable|string|max:500',
        ]);

        $missingRequirements = $stageService->getMissingRequirements($investor, $validated['new_stage']);

        if (!empty($missingRequirements)) {
            return back()
                ->withErrors(['requirements' => 'Cannot move to this stage. Missing requirements:'])
                ->with('missing_requirements', $missingRequirements);
        }

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
            'monitored' => 'Ongoing KYC/AML monitoring enabled', // ADDED
            default => 'No automatic actions',
        };
    }
}
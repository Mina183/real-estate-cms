<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Fund;
use App\Models\User;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
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
        ]);

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
}

<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Investor;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
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
        $this->authorizeResource(Distribution::class, 'distribution');
    }

    /**
     * Display a listing of distributions
     */
    public function index()
    {
        $distributions = Distribution::with('fund')
            ->orderBy('distribution_date', 'desc')
            ->paginate(15);

        $stats = [
            'total_distributions' => Distribution::count(),
            'pending_distributions' => Distribution::pending()->count(),
            'completed_distributions' => Distribution::completed()->count(),
            'total_amount_distributed' => Distribution::sum('total_distributed'),
        ];

        return view('distributions.index', compact('distributions', 'stats'));
    }

    /**
     * Show the form for creating a new distribution
     */
    public function create()
    {
        // Get active investors eligible for distributions
        $investors = Investor::where('stage', 'active')
            ->where('funded_amount', '>', 0)
            ->get();

        return view('distributions.create', compact('investors'));
    }

    /**
     * Store a newly created distribution
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fund_id' => 'nullable|exists:funds,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:dividend,return_of_capital,profit_share,other',
            'total_amount' => 'required|numeric|min:0',
            'distribution_date' => 'required|date',
            'record_date' => 'required|date|before_or_equal:distribution_date',
            'status' => 'required|in:draft,approved,processing,completed',
            'notes' => 'nullable|string',
            'investor_ids' => 'nullable|array',
            'investor_ids.*' => 'exists:investors,id',
            'investor_amounts' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Generate unique distribution number
            $lastDist = Distribution::latest('id')->first();
            $distNumber = 'DIST-' . date('Y') . '-' . str_pad(($lastDist ? $lastDist->id + 1 : 1), 3, '0', STR_PAD_LEFT);

            // Create distribution
            $distribution = Distribution::create([
                'fund_id' => $validated['fund_id'] ?? null,
                'distribution_number' => $distNumber,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'total_amount' => $validated['total_amount'],
                'distribution_date' => $validated['distribution_date'],
                'record_date' => $validated['record_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create payment transactions for selected investors
            if (!empty($validated['investor_ids'])) {
                foreach ($validated['investor_ids'] as $index => $investorId) {
                    $investor = Investor::find($investorId);
                    $amount = $validated['investor_amounts'][$index] ?? 0;

                    if ($amount > 0 && $investor) {
                        PaymentTransaction::create([
                            'transactionable_id' => $distribution->id,
                            'transactionable_type' => Distribution::class,
                            'investor_id' => $investorId,
                            'transaction_type' => 'distribution',
                            'amount' => $amount,
                            'status' => 'pending',
                            'due_date' => $validated['distribution_date'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('distributions.show', $distribution)
                ->with('success', 'Distribution created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating distribution: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified distribution
     */
    public function show(Distribution $distribution)
    {
        $distribution->load(['payments.investor', 'fund']);

        $stats = [
            'total_payments' => $distribution->payments()->count(),
            'completed_payments' => $distribution->completedPayments()->count(),
            'pending_payments' => $distribution->pendingPayments()->count(),
            'distribution_percentage' => $distribution->distribution_percentage,
            'remaining_amount' => $distribution->remaining_amount,
        ];

        return view('distributions.show', compact('distribution', 'stats'));
    }

    /**
     * Show the form for editing the specified distribution
     */
    public function edit(Distribution $distribution)
    {
        $investors = Investor::where('stage', 'active')->get();
        $distribution->load('payments.investor');

        return view('distributions.edit', compact('distribution', 'investors'));
    }

    /**
     * Update the specified distribution
     */
    public function update(Request $request, Distribution $distribution)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:dividend,return_of_capital,profit_share,other',
            'total_amount' => 'required|numeric|min:0',
            'distribution_date' => 'required|date',
            'record_date' => 'required|date|before_or_equal:distribution_date',
            'status' => 'required|in:draft,approved,processing,completed',
            'notes' => 'nullable|string',
        ]);

        $distribution->update($validated);

        return redirect()
            ->route('distributions.show', $distribution)
            ->with('success', 'Distribution updated successfully!');
    }

    /**
     * Remove the specified distribution
     */
    public function destroy(Distribution $distribution)
    {
        // Check if there are any completed payments
        if ($distribution->completedPayments()->count() > 0) {
            return back()->with('error', 'Cannot delete distribution with completed payments.');
        }

        $distribution->delete();

        return redirect()
            ->route('distributions.index')
            ->with('success', 'Distribution deleted successfully!');
    }

    /**
     * Approve distribution (change status from draft to approved)
     * Uses explicit authorization check for custom action
     */
    public function approve(Distribution $distribution)
    {
        $this->authorize('issue', $distribution);

        if ($distribution->status !== 'draft') {
            return back()->with('error', 'Only draft distributions can be approved.');
        }

        $distribution->update(['status' => 'approved']);

        return back()->with('success', 'Distribution approved successfully!');
    }

    /**
     * Start processing distribution
     * Uses explicit authorization check for custom action
     */
    public function process(Distribution $distribution)
    {
        $this->authorize('issue', $distribution);

        if ($distribution->status !== 'approved') {
            return back()->with('error', 'Only approved distributions can be processed.');
        }

        $distribution->update(['status' => 'processing']);

        // TODO: Initiate payment processing

        return back()->with('success', 'Distribution processing started!');
    }
}
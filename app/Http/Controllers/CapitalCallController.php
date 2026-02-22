<?php

namespace App\Http\Controllers;

use App\Models\CapitalCall;
use App\Models\Investor;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapitalCallController extends Controller
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
        $this->authorizeResource(CapitalCall::class, 'capitalCall');
    }

    /**
     * Display a listing of capital calls
     */
    public function index()
    {
        $capitalCalls = CapitalCall::with('fund')
            ->orderBy('call_date', 'desc')
            ->paginate(15);

        $stats = [
            'total_calls' => CapitalCall::count(),
            'active_calls' => CapitalCall::active()->count(),
            'overdue_calls' => CapitalCall::overdue()->count(),
            'total_amount_called' => CapitalCall::sum('total_amount'),
            'total_amount_received' => CapitalCall::sum('total_received'),
        ];

        return view('capital-calls.index', compact('capitalCalls', 'stats'));
    }

    /**
     * Show the form for creating a new capital call
     */
    public function create()
    {
        // Get active investors for payment assignment
        $investors = Investor::where('stage', 'active')
            ->where('final_commitment_amount', '>', 0)
            ->get();

        return view('capital-calls.create', compact('investors'));
    }

    /**
     * Store a newly created capital call
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fund_id' => 'nullable|exists:funds,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'call_date' => 'required|date',
            'due_date' => 'required|date|after:call_date',
            'status' => 'required|in:draft,issued,partially_paid,fully_paid,overdue',
            'notes' => 'nullable|string',
            'investor_ids' => 'nullable|array',
            'investor_ids.*' => 'exists:investors,id',
            'investor_amounts' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Generate unique call number
            $lastCall = CapitalCall::latest('id')->first();
            $callNumber = 'CC-' . date('Y') . '-' . str_pad(($lastCall ? $lastCall->id + 1 : 1), 3, '0', STR_PAD_LEFT);

            // Create capital call
            $capitalCall = CapitalCall::create([
                'fund_id' => $validated['fund_id'] ?? null,
                'call_number' => $callNumber,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'total_amount' => $validated['total_amount'],
                'call_date' => $validated['call_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create payment transactions for selected investors
            if (!empty($validated['investor_ids'])) {
                foreach ($validated['investor_ids'] as $index => $investorId) {
                    $investor = Investor::find($investorId);
                    $amount = $validated['investor_amounts'][$index] ?? 0;

                    if ($amount > 0 && $investor) {
                        // Calculate percentage of commitment
                        $commitmentPercentage = $investor->final_commitment_amount > 0
                            ? ($amount / $investor->final_commitment_amount) * 100
                            : 0;

                        PaymentTransaction::create([
                            'transactionable_id' => $capitalCall->id,
                            'transactionable_type' => CapitalCall::class,
                            'investor_id' => $investorId,
                            'transaction_type' => 'capital_call',
                            'amount' => $amount,
                            'commitment_percentage' => $commitmentPercentage,
                            'status' => 'pending',
                            'due_date' => $validated['due_date'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('capital-calls.show', $capitalCall)
                ->with('success', 'Capital Call created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating capital call: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified capital call
     */
    public function show(CapitalCall $capitalCall)
    {
        $capitalCall->load(['payments.investor', 'fund']);

        $stats = [
            'total_payments' => $capitalCall->payments()->count(),
            'paid_payments' => $capitalCall->paidPayments()->count(),
            'pending_payments' => $capitalCall->pendingPayments()->count(),
            'payment_percentage' => $capitalCall->payment_percentage,
            'outstanding_amount' => $capitalCall->outstanding_amount,
        ];

        return view('capital-calls.show', compact('capitalCall', 'stats'));
    }

    /**
     * Show the form for editing the specified capital call
     */
    public function edit(CapitalCall $capitalCall)
    {
        $investors = Investor::where('stage', 'active')->get();
        $capitalCall->load('payments.investor');

        return view('capital-calls.edit', compact('capitalCall', 'investors'));
    }

    /**
     * Update the specified capital call
     */
    public function update(Request $request, CapitalCall $capitalCall)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'call_date' => 'required|date',
            'due_date' => 'required|date|after:call_date',
            'status' => 'required|in:draft,issued,partially_paid,fully_paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $capitalCall->update($validated);

        return redirect()
            ->route('capital-calls.show', $capitalCall)
            ->with('success', 'Capital Call updated successfully!');
    }

    /**
     * Remove the specified capital call
     */
    public function destroy(CapitalCall $capitalCall)
    {
        // Check if there are any paid transactions
        if ($capitalCall->paidPayments()->count() > 0) {
            return back()->with('error', 'Cannot delete capital call with paid transactions.');
        }

        $capitalCall->delete();

        return redirect()
            ->route('capital-calls.index')
            ->with('success', 'Capital Call deleted successfully!');
    }

    /**
     * Issue capital call (change status from draft to issued)
     * Uses explicit authorization check for custom action
     */
    public function issue(CapitalCall $capitalCall)
    {
        $this->authorize('issue', $capitalCall);

        if ($capitalCall->status !== 'draft') {
            return back()->with('error', 'Only draft capital calls can be issued.');
        }

        $capitalCall->update(['status' => 'issued']);

        // TODO: Send email notifications to investors

        return back()->with('success', 'Capital Call issued successfully!');
    }
}
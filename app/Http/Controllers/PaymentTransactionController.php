<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
    /**
     * Mark payment as paid
     */
    public function markAsPaid(Request $request, PaymentTransaction $payment)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:255',
            'paid_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'status' => 'paid',
            'paid_date' => $validated['paid_date'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? $payment->notes,
        ]);

        // Update parent total (using the method from PaymentTransaction model)
        $payment->updateParentTotal();

        return back()->with('success', 'Payment marked as paid successfully!');
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(Request $request, PaymentTransaction $payment)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $payment->update([
            'status' => 'failed',
            'notes' => $validated['notes'],
        ]);

        return back()->with('error', 'Payment marked as failed.');
    }

    /**
     * Reverse a payment
     */
    public function reverse(Request $request, PaymentTransaction $payment)
    {
        if ($payment->status !== 'paid') {
            return back()->with('error', 'Only paid payments can be reversed.');
        }

        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $payment->update([
            'status' => 'reversed',
            'notes' => $validated['notes'],
        ]);

        // Update parent total
        $payment->updateParentTotal();

        return back()->with('warning', 'Payment reversed successfully.');
    }

    /**
     * Update payment details
     */
    public function update(Request $request, PaymentTransaction $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return back()->with('success', 'Payment details updated successfully!');
    }

    /**
     * Delete payment transaction
     */
    public function destroy(PaymentTransaction $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'Cannot delete paid transactions.');
        }

        $payment->delete();

        return back()->with('success', 'Payment transaction deleted successfully!');
    }

    /**
     * Bulk mark as paid
     */
    public function bulkMarkAsPaid(Request $request)
    {
        $validated = $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:payment_transactions,id',
            'payment_method' => 'required|string',
            'paid_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payments = PaymentTransaction::whereIn('id', $validated['payment_ids'])
            ->where('status', 'pending')
            ->get();

        foreach ($payments as $payment) {
            $payment->markAsPaid(
                $validated['payment_method'],
                null,
                $validated['notes'] ?? null
            );
        }

        return back()->with('success', count($payments) . ' payments marked as paid!');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'investor_id',
        'transaction_type',
        'amount',
        'commitment_percentage',
        'status',
        'due_date',
        'paid_date',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commitment_percentage' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * Get the parent transactionable model (CapitalCall or Distribution)
     */
    public function transactionable()
    {
        return $this->morphTo();
    }

    /**
     * Get the investor that this transaction belongs to
     */
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date && $this->due_date < now();
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($paymentMethod = null, $referenceNumber = null, $notes = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'notes' => $notes ?? $this->notes,
        ]);

        // Update parent transactionable total
        $this->updateParentTotal();
    }

    /**
     * Update parent transactionable total amounts
     */
    protected function updateParentTotal()
    {
        $parent = $this->transactionable;
        
        if (!$parent) {
            return;
        }

        $totalPaid = $parent->payments()->where('status', 'paid')->sum('amount');

        if ($parent instanceof CapitalCall) {
            $parent->update(['total_received' => $totalPaid]);
            
            // Update capital call status
            if ($totalPaid >= $parent->total_amount) {
                $parent->update(['status' => 'fully_paid']);
            } elseif ($totalPaid > 0) {
                $parent->update(['status' => 'partially_paid']);
            }
        } elseif ($parent instanceof Distribution) {
            $parent->update(['total_distributed' => $totalPaid]);
            
            // Update distribution status
            if ($totalPaid >= $parent->total_amount) {
                $parent->update(['status' => 'completed']);
            } elseif ($totalPaid > 0) {
                $parent->update(['status' => 'processing']);
            }
        }
    }

    /**
     * Scope for overdue payments
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now());
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for capital call payments
     */
    public function scopeCapitalCalls($query)
    {
        return $query->where('transactionable_type', CapitalCall::class);
    }

    /**
     * Scope for distribution payments
     */
    public function scopeDistributions($query)
    {
        return $query->where('transactionable_type', Distribution::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'reversed' => 'gray',
            default => 'blue',
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute()
    {
        if (!$this->payment_method) {
            return 'N/A';
        }

        return match($this->payment_method) {
            'wire_transfer' => 'Wire Transfer',
            'ach' => 'ACH',
            'check' => 'Check',
            'cash' => 'Cash',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }
}


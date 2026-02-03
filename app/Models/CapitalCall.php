<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapitalCall extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fund_id',
        'call_number',
        'title',
        'description',
        'total_amount',
        'call_date',
        'due_date',
        'status',
        'total_received',
        'notes',
    ];

    protected $casts = [
        'call_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'total_received' => 'decimal:2',
    ];

    /**
     * Get the fund that this capital call belongs to
     */
    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    /**
     * Get all payment transactions for this capital call
     */
    public function payments()
    {
        return $this->morphMany(PaymentTransaction::class, 'transactionable');
    }

    /**
     * Get pending payments
     */
    public function pendingPayments()
    {
        return $this->payments()->where('status', 'pending');
    }

    /**
     * Get paid payments
     */
    public function paidPayments()
    {
        return $this->payments()->where('status', 'paid');
    }

    /**
     * Calculate outstanding amount
     */
    public function getOutstandingAmountAttribute()
    {
        return $this->total_amount - $this->total_received;
    }

    /**
     * Calculate payment percentage
     */
    public function getPaymentPercentageAttribute()
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return ($this->total_received / $this->total_amount) * 100;
    }

    /**
     * Check if overdue
     */
    public function isOverdue()
    {
        return $this->status !== 'fully_paid' && $this->due_date < now();
    }

    /**
     * Scope for overdue capital calls
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'fully_paid')
                     ->where('due_date', '<', now());
    }

    /**
     * Scope for active capital calls
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['issued', 'partially_paid']);
    }
}


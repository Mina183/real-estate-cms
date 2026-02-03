<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fund_id',
        'distribution_number',
        'title',
        'description',
        'type',
        'total_amount',
        'distribution_date',
        'record_date',
        'status',
        'total_distributed',
        'notes',
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'record_date' => 'date',
        'total_amount' => 'decimal:2',
        'total_distributed' => 'decimal:2',
    ];

    /**
     * Get the fund that this distribution belongs to
     */
    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    /**
     * Get all payment transactions for this distribution
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
     * Get completed payments
     */
    public function completedPayments()
    {
        return $this->payments()->where('status', 'paid');
    }

    /**
     * Calculate remaining amount to distribute
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->total_distributed;
    }

    /**
     * Calculate distribution percentage
     */
    public function getDistributionPercentageAttribute()
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return ($this->total_distributed / $this->total_amount) * 100;
    }

    /**
     * Scope for completed distributions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending distributions
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'approved', 'processing']);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'dividend' => 'Dividend',
            'return_of_capital' => 'Return of Capital',
            'profit_share' => 'Profit Share',
            'other' => 'Other',
            default => ucfirst($this->type),
        };
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'fund_id',
        'committed_amount',
        'currency',
        'commitment_date',
        'status',
        'funded_amount',
        'remaining_amount',
        'notes',
    ];

    protected $casts = [
        'committed_amount' => 'decimal:2',
        'funded_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'commitment_date' => 'date',
    ];

    // Relationships
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    // Calculate remaining amount automatically
    public function calculateRemaining()
    {
        $this->remaining_amount = $this->committed_amount - $this->funded_amount;
        return $this->remaining_amount;
    }
}

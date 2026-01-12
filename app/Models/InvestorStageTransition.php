<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvestorStageTransition extends Model
{
    use HasFactory;

    public $timestamps = false; // We use transitioned_at instead

    protected $fillable = [
        'investor_id',
        'from_stage',
        'to_stage',
        'from_status',
        'to_status',
        'changed_by_user_id',
        'reason',
        'metadata',
        'transitioned_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'transitioned_at' => 'datetime',
    ];

    // Relationships
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
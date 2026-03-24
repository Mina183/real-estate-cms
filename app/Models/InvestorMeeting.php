<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorMeeting extends Model
{
    protected $fillable = [
        'investor_id',
        'meeting_date',
        'attendees',
        'outcome',
        'created_by_user_id',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
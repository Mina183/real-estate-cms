<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fund extends Model
{
    use HasFactory;

    protected $fillable = [
        'fund_name',
        'fund_number',
        'vintage_year',
        'total_size',
        'currency',
        'status',
        'inception_date',
        'description',
        'metadata',
    ];

    protected $casts = [
        'total_size' => 'decimal:2',
        'inception_date' => 'date',
        'metadata' => 'array',
    ];

    // Relationships
    public function investors()
    {
        return $this->hasMany(Investor::class);
    }

    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }
}

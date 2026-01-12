<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        
        // Basic info
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        
        // Role & Permissions
        'role',
        'is_primary',
        'can_sign_documents',
        'receives_capital_calls',
        'receives_distributions',
        'receives_reports',
        
        // Personal info (KYC)
        'title',
        'nationality',
        'date_of_birth',
        'passport_number',
        'national_id',
        
        // Address
        'address_line1',
        'address_line2',
        'city',
        'state_province',
        'postal_code',
        'country',
        
        // Preferences
        'preferred_language',
        'preferred_contact_method',
        
        // Portal access
        'user_id',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'can_sign_documents' => 'boolean',
        'receives_capital_calls' => 'boolean',
        'receives_distributions' => 'boolean',
        'receives_reports' => 'boolean',
        'date_of_birth' => 'date',
    ];

    // Relationships
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

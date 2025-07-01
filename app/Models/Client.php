<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    
        protected $fillable = [
        'name', 'email', 'phone', 'passport_number', 'nationality', 'language', 'base_location',
        'best_contact_method', 'lead_source_id', 'is_investor', 'investor_type',
        'preferred_property_type', 'preferred_location', 'uae_visa_required',
        'property_detail_type', 'investment_type', 'investment_budget',
        'employment_source', 'funds_location', 'cp_remarks', 'funnel_stage',
        'channel_partner_id',
    ];

    public function communications()
    {
        return $this->hasMany(ClientCommunication::class);
    }

    public function documents()
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class); // if you have this table
    }

    public function channelPartner()
    {
        return $this->belongsTo(User::class, 'channel_partner_id');
    }
}

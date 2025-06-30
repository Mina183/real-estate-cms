<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'channel_partner_id']; // adjust fields as needed

    public function channelPartner()
    {
        return $this->belongsTo(User::class, 'channel_partner_id');
    }
}

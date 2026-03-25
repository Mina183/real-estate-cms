<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDraft extends Model
{
    protected $fillable = [
        'investor_id', 'template_key', 'on_behalf_of_id', 'signature_id',
        'subject', 'body', 'document_ids', 'status',
        'created_by_user_id', 'approved_by_user_id', 'approved_at',
    ];

    protected $casts = [
        'document_ids' => 'array',
        'approved_at' => 'datetime',
    ];

    public function investor() { return $this->belongsTo(Investor::class); }
    public function onBehalfOf() { return $this->belongsTo(EmailOnBehalf::class, 'on_behalf_of_id'); }
    public function signature() { return $this->belongsTo(EmailSignature::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by_user_id'); }
}
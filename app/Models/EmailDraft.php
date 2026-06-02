<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDraft extends Model
{
    protected $fillable = [
        'investor_id', 'template_key', 'on_behalf_of_id', 'signature_id',
        'subject', 'body', 'document_ids', 'cc_emails', 'status',
        'created_by_user_id', 'approved_by_user_id', 'approved_at',
        'is_bulk', 'bulk_recipient_type', 'bulk_recipient_stage',
        'bulk_assigned_to_user_id', 'bulk_recipient_ids', 'bulk_recipient_count',
    ];

    protected $casts = [
        'document_ids'       => 'array',
        'cc_emails'          => 'array',
        'bulk_recipient_ids' => 'array',
        'approved_at'        => 'datetime',
        'is_bulk'            => 'boolean',
    ];

    public function investor() { return $this->belongsTo(Investor::class); }
    public function onBehalfOf() { return $this->belongsTo(EmailOnBehalf::class, 'on_behalf_of_id'); }
    public function signature() { return $this->belongsTo(EmailSignature::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by_user_id'); }
    public function bulkAssignedTo() { return $this->belongsTo(User::class, 'bulk_assigned_to_user_id'); }
}

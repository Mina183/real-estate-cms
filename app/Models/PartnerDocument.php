<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerDocument extends Model
{
    protected $fillable = [
        'title',
        'filename',
        'file_path',
        'uploaded_by',
        'partner_id',
        'status',
        'reviewed_at',
        'seen_by_partner_at',
        'response_file_path',
        'response_uploaded_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'seen_by_partner_at' => 'datetime',
        'response_uploaded_at' => 'datetime',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function responses()
{
    return $this->hasMany(PartnerDocumentResponse::class, 'document_id');
}

    public function isWaitingPartnerAction()
    {
        return $this->status === 'waiting_partner_action';
    }

    public function isWaitingAdminApproval()
    {
        return $this->status === 'waiting_admin_approval';
    }

    public function isComplete()
    {
        return $this->status === 'complete';
    }

    public function isAcknowledgedNoAction()
    {
        return $this->status === 'acknowledged_no_action';
    }
}

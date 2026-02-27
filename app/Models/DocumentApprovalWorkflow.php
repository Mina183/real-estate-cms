<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentApprovalWorkflow extends Model
{
    protected $fillable = [
        'document_id',
        'workflow_status',
        'submitted_by_user_id',
        'submitted_at',
        'reviewer_user_id',
        'reviewed_at',
        'approver_user_id',
        'approved_at',
        'reviewer_comments',
        'rejection_reason',
        'required_metadata_checklist',
        'metadata_complete',
    ];

    protected $casts = [
        'submitted_at'               => 'datetime',
        'reviewed_at'                => 'datetime',
        'approved_at'                => 'datetime',
        'required_metadata_checklist' => 'array',
        'metadata_complete'          => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(DataRoomDocument::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
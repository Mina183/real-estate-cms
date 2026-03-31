<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAccessRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_access_link_id',
        'requester_name',
        'requester_email',
        'status',
        'approved_by_user_id',
        'approved_at',
        'expires_at',
        'ip_address',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(DocumentAccessLink::class, 'document_access_link_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'approved'
            && $this->expires_at !== null
            && $this->expires_at->isFuture();
    }
}

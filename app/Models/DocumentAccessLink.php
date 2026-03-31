<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentAccessLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_package_id',
        'investor_id',
        'token',
        'label',
        'created_by_user_id',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(DocumentPackage::class, 'document_package_id');
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function accessRequests(): HasMany
    {
        return $this->hasMany(DocumentAccessRequest::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('doc-access.show', $this->token);
    }

    public function getPendingRequestsCountAttribute(): int
    {
        return $this->accessRequests()->where('status', 'pending')->count();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DocumentPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by_user_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(DocumentPackageItem::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(DataRoomDocument::class, 'document_package_items', 'document_package_id', 'data_room_document_id');
    }

    public function accessLinks(): HasMany
    {
        return $this->hasMany(DocumentAccessLink::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentPackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_package_id',
        'data_room_document_id',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(DocumentPackage::class, 'document_package_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DataRoomDocument::class, 'data_room_document_id');
    }
}

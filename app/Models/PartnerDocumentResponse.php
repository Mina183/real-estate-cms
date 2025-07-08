<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerDocumentResponse extends Model
{
    protected $fillable = [
        'document_id',
        'partner_id',
        'response_file_path',
        'response_uploaded_at',
        'reviewed_at',
        'status', // optional if you want to track response status per partner
    ];

    protected $casts = [
        'response_uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(PartnerDocument::class, 'document_id');
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
}
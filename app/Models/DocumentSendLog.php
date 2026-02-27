<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSendLog extends Model
{
    protected $table = 'document_send_logs';
    public $timestamps = false;

    protected $fillable = [
        'investor_id',
        'document_id',
        'document_name',
        'template',
        'email_subject',
        'sent_by_user_id',
        'sent_to_email',
        'document_version',
        'acknowledgement_token',
        'acknowledged_at',
        'requires_acknowledgement',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'requires_acknowledgement' => 'boolean',
    ];

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }
}
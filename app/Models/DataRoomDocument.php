<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataRoomDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'folder_id',
        'investor_id',
        'document_name',
        'file_path',
        'file_type',
        'file_size',
        'version',
        'description',
        'status',
        'uploaded_by',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function folder()
    {
        return $this->belongsTo(DataRoomFolder::class, 'folder_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
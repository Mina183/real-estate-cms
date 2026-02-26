<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataRoomActivityLog extends Model
{
    use HasFactory;

    protected $table = 'data_room_activity_log';

    public $timestamps = false; // Using activity_at instead

    protected $fillable = [
        'user_id',
        'investor_id',
        'document_id',
        'folder_id',
        'resource_type',
        'resource_id',
        'activity_type',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'is_suspicious',
        'notes',
        'metadata',
        'activity_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_suspicious' => 'boolean',
        'activity_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function document()
    {
        return $this->belongsTo(DataRoomDocument::class, 'document_id');
    }

    public function folder()
    {
        return $this->belongsTo(DataRoomFolder::class, 'folder_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::updating(function () {
            throw new \Exception('Activity logs are append-only and cannot be modified.');
        });
        
        static::deleting(function () {
            throw new \Exception('Activity logs are append-only and cannot be deleted.');
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataRoomPermission extends Model
{
    protected $fillable = [
        'investor_id',
        'folder_id',
        'user_id',
        'can_view',
        'can_download',
        'can_print',
        'can_upload',
        'can_edit',
        'can_delete',
        'granted_by_user_id',
        'granted_at',
        'access_reason',
        'expires_at',
        'ip_whitelist',
        'device_restrictions',
        'requires_2fa',
        'requires_watermark',
        'is_active',
        'revoked_at',
        'revoked_by_user_id',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_download' => 'boolean',
        'can_print' => 'boolean',
        'can_upload' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'requires_2fa' => 'boolean',
        'requires_watermark' => 'boolean',
        'is_active' => 'boolean',
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'ip_whitelist' => 'array',
        'device_restrictions' => 'array',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function folder()
    {
        return $this->belongsTo(DataRoomFolder::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by_user_id');
    }
}
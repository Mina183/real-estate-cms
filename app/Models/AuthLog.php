<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'guard',
        'user_id',
        'email',
        'event',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = now();
        });

        static::updating(function () {
        throw new \Exception('Auth logs are append-only and cannot be modified.');
    });
    
        static::deleting(function () {
            throw new \Exception('Auth logs are append-only and cannot be deleted.');
        });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;

class InvestorUser extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'investor_users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'investor_id',
        'name',
        'email',
        'password',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_enabled_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'two_factor_enabled_at' => 'datetime',
    ];

    /**
     * Relationship: InvestorUser belongs to Investor
     */
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * Relationship: InvestorUser has many messages
     */
    public function messages()
    {
        return $this->hasMany(InvestorMessage::class, 'investor_id', 'investor_id');
    }

    /**
     * Scope: Only active investor users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if investor user account is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Update last login timestamp and IP
     */
    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function sendPasswordResetNotification($token)
    {
        $url = url(route('investor.password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));

        $this->notify(new ResetPassword($token));
    }
}

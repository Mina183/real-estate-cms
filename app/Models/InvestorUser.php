<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;

class InvestorUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'investor_users';

    protected $fillable = [
        'investor_id',
        'name',
        'email',
        'password',
        'portal_pin',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_enabled_at',
    ];

    protected $hidden = [
        'password',
        'portal_pin',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'last_login_at'        => 'datetime',
        'is_active'            => 'boolean',
        'two_factor_enabled'   => 'boolean',
        'two_factor_enabled_at'=> 'datetime',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    public function messages()
    {
        return $this->hasMany(InvestorMessage::class, 'investor_id', 'investor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = route('investor.password.reset', [
            'token' => $token,
            'email' => $this->email,
        ]);

        $this->notify(new \App\Notifications\InvestorResetPasswordNotification($token, $url));
    }
}

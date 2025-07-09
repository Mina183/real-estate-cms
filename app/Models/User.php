<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // ✅ Add your custom constants here
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CHANNEL_PARTNER = 'channel_partner';
    public const ROLE_GUEST = 'guest';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved',
        'role',
        'requested_role',
        'supervisor_id',
    ];


    // ✅ You can define role-check helpers like this:

    public function isSuperadmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isChannelPartner()
    {
        return $this->role === self::ROLE_CHANNEL_PARTNER;
    }

    public function isGuest()
    {
        return $this->role === self::ROLE_GUEST;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function meetings()
{
    return $this->belongsToMany(Meeting::class)->withPivot('is_accepted', 'accepted_at')->withTimestamps();
}
}

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'description', 'start_time', 'end_time', 'created_by'];

    public function attendees()
    {
        return $this->belongsToMany(User::class)->withPivot('is_accepted', 'accepted_at')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
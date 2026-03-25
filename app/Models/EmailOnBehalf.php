<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOnBehalf extends Model
{
    protected $fillable = ['name', 'title', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
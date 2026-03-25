<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailBodyTemplate extends Model
{
    protected $fillable = ['name', 'subject_suggestion', 'body', 'is_active', 'created_by_user_id', 'updated_by_user_id'];
    protected $casts = ['is_active' => 'boolean'];
}
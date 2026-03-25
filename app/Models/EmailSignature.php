<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSignature extends Model
{
    protected $fillable = ['name', 'signature_html', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'filename', 'path'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

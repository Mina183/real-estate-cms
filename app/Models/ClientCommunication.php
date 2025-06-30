<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCommunication extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'date', 'action', 'update', 'feedback', 'outcome'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

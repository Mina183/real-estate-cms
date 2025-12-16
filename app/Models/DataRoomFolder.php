<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataRoomFolder extends Model
{
    protected $fillable = [
        'folder_number',
        'folder_name',
        'parent_folder_id',
        'order',
        'description',
        'access_level',
        'is_active'
    ];

    public function parent()
    {
        return $this->belongsTo(DataRoomFolder::class, 'parent_folder_id');
    }

    public function children()
    {
        return $this->hasMany(DataRoomFolder::class, 'parent_folder_id')->orderBy('order');
    }

    public function documents()
    {
        return $this->hasMany(DataRoomDocument::class, 'folder_id');
    }
}
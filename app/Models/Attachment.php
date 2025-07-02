<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['uploader_id', 'file_name', 'file_path', 'mime_type'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function attachable()
    {
        return $this->morphTo();
    }
}
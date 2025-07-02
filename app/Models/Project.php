<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'start_date', 
        'end_date', 
        'status', 
        'creator_id',
        'organization_id', // Tambahkan ini
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Tambahkan relasi ini
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
    
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}

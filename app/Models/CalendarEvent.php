<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari penamaan standar Laravel (calendar_events)
    protected $table = 'calendar_events';

    /**
     * Properti yang bisa diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'title',
        'start',
        'end',
        'level',
    ];

    /**
     * Terapkan enkripsi & casting tipe data.
     */
    protected $casts = [
        'title' => 'encrypted', // Enkripsi judul event
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
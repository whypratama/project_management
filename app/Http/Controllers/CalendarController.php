<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    // Menampilkan halaman kalender utama
    public function view()
    {
        return view('calendar.index'); // Kita akan buat view ini nanti
    }

    // API: Mengambil semua event untuk user yang login
    public function index(Request $request)
    {
        $events = CalendarEvent::where('user_id', auth()->id())->get();

        // Format data sesuai yang dibutuhkan FullCalendar.js
        $formattedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title, // Otomatis terdekripsi karena ada di $casts
                'start' => $event->start->toIso8601String(),
                'end' => $event->end ? $event->end->toIso8601String() : null,
                'extendedProps' => [
                    'calendar' => $event->level // 'calendar' sesuai dengan properti di JS Anda
                ]
            ];
        });

        return response()->json($formattedEvents);
    }
// 
    // API: Menyimpan event baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'level' => 'required|string',
        ]);

        $event = CalendarEvent::create([
            'user_id' => auth()->id(),
            'title' => $request->title, // Otomatis terenkripsi saat disimpan
            'start' => $request->start,
            'end' => $request->end,
            'level' => $request->level,
        ]);

        return response()->json(['success' => true, 'event_id' => $event->id]);
    }

    // API: Memperbarui event yang sudah ada
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        // Pastikan user hanya bisa mengedit event miliknya sendiri
        if ($calendarEvent->user_id != auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'level' => 'required|string',
        ]);

        $calendarEvent->update($request->all());

        return response()->json(['success' => true]);
    }
}
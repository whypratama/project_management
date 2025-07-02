<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class MessageController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Menyimpan pesan baru ke dalam proyek.
     */
    public function store(Request $request, Project $project)
    {
        // Otorisasi: Jika user bisa melihat proyek, ia bisa mengirim pesan.
        $this->authorize('view', $project);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Buat pesan baru menggunakan relasi polimorfik
        $project->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}

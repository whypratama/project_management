<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
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
        $this->authorize('view', $project);
        $request->validate(['content' => 'required|string|max:1000']);
        $project->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->content,
        ]);
        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    public function storeForTask(Request $request, Task $task)
    {
        $this->authorize('view', $task);
        $request->validate(['content' => 'required|string|max:1000']);
        $task->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->content,
        ]);
        return back()->with('success', 'Pesan berhasil dikirim.');
    }

}

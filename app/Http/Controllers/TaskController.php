<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class TaskController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function store(Request $request, Project $project)
    {
        // Menggunakan izin 'addTask' yang lebih spesifik
        $this->authorize('addTask', $project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $task = new Task($request->all());
        $task->project_id = $project->id;
        $task->creator_id = auth()->id();
        $task->status = 'pending';
        $task->save();

        return back()->with('success', 'Tugas baru berhasil ditambahkan.');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $user = auth()->user();
        $rules = [];
        
        // Bangun aturan validasi berdasarkan hak akses spesifik.
        if ($user->can('updateDetails', $task)) {
            $rules += [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'due_date' => 'nullable|date',
            ];
        }
        if ($user->can('updateStatus', $task)) {
            $rules += ['status' => 'required|string'];
        }

        // Validasi hanya field yang relevan.
        $validatedData = $request->validate($rules);
        $task->update($validatedData);

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return back()->with('success', 'Tugas berhasil dihapus.');
    }
}
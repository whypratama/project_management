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
        // PERUBAHAN DI SINI: Menggunakan izin 'addTask' yang lebih spesifik
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

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        if ($request->has('status') && $task->creator_id !== auth()->id()) {
            unset($validatedData['status']);
            session()->flash('warning', 'Anda tidak memiliki izin untuk mengubah status tugas ini.');
        }

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

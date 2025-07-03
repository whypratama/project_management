<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class AttachmentController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Menyimpan file yang diunggah dan mengenkripsinya.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('addTask', $project);
        $request->validate(['file' => 'required|file|max:10240']);
        $file = $request->file('file');
        $encryptedContent = Crypt::encryptString($file->get());
        $path = 'attachments/' . $project->id . '/' . uniqid() . '.encrypted';
        Storage::put($path, $encryptedContent);
        $project->attachments()->create([
            'uploader_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
        ]);
        return back()->with('success', 'File berhasil diunggah.');
    }

    /**
     * Mengunduh file dengan mendekripsinya terlebih dahulu.
     */
    public function download(Attachment $attachment)
    {
        $this->authorize('view', $attachment->attachable);
        if (!Storage::exists($attachment->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }
        $encryptedContent = Storage::get($attachment->file_path);
        $decryptedContent = Crypt::decryptString($encryptedContent);
        return response()->streamDownload(function () use ($decryptedContent) {
            echo $decryptedContent;
        }, $attachment->file_name);
    }

    /**
     * Menghapus file dari storage dan database.
     */
    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);
        if (Storage::exists($attachment->file_path)) {
            Storage::delete($attachment->file_path);
        }
        $attachment->delete();
        return back()->with('success', 'File berhasil dihapus.');
    }

    public function storeForTask(Request $request, Task $task)
    {
        $this->authorize('upload', $task);
        $request->validate(['file' => 'required|file|max:10240']);
        $file = $request->file('file');
        $encryptedContent = Crypt::encryptString($file->get());
        $path = 'attachments/tasks/' . $task->id . '/' . uniqid() . '.encrypted';
        Storage::put($path, $encryptedContent);
        $task->attachments()->create([
            'uploader_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
        ]);
        return back()->with('success', 'File berhasil diunggah.');
    }
}

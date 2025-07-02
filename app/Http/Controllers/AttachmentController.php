<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
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
        $this->authorize('addTask', $project); // Gunakan izin yang sama dengan menambah tugas

        $request->validate([
            'file' => 'required|file|max:10240', // Maksimal 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // Enkripsi konten file
        $encryptedContent = Crypt::encryptString($file->get());

        // Simpan file terenkripsi ke storage
        $path = 'attachments/' . $project->id . '/' . uniqid() . '.encrypted';
        Storage::put($path, $encryptedContent);

        // Simpan metadata file ke database
        $project->attachments()->create([
            'uploader_id' => auth()->id(),
            'file_name' => $originalName,
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
        $this->authorize('view', $attachment->attachable); // Cek apakah user bisa melihat proyek induk

        if (!Storage::exists($attachment->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Ambil dan dekripsi konten file
        $encryptedContent = Storage::get($attachment->file_path);
        $decryptedContent = Crypt::decryptString($encryptedContent);

        // Kirim sebagai response download
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

        // Hapus file dari storage
        if (Storage::exists($attachment->file_path)) {
            Storage::delete($attachment->file_path);
        }

        // Hapus record dari database
        $attachment->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }
}

<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttachmentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return null;
    }

    /**
     * Tentukan apakah user bisa menghapus file.
     * Hanya pengunggah atau pembuat proyek yang bisa.
     */
    public function delete(User $user, Attachment $attachment): bool
    {
        if ($user->id === $attachment->uploader_id) {
            return true;
        }

        // Cek apakah user adalah pembuat proyek (attachable)
        if ($attachment->attachable_type === 'App\\Models\\Project' && $user->id === $attachment->attachable->creator_id) {
            return true;
        }

        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return null;
    }

    /**
     * Izin umum untuk membuka form edit.
     * User bisa membuka form jika ia bisa mengubah detail ATAU status.
     */
    public function update(User $user, Task $task): bool
    {
        return $this->updateDetails($user, $task) || $this->updateStatus($user, $task);
    }

    /**
     * Izin untuk mengubah detail tugas (nama, deskripsi, dll).
     * Hanya pembuat proyek atau pembuat tugas yang bisa.
     */
    public function updateDetails(User $user, Task $task): bool
    {
        return $user->id === $task->project->creator_id || $user->id === $task->creator_id;
    }

    /**
     * Izin untuk mengubah status tugas.
     * PERBAIKAN DI SINI: Hanya pembuat tugas (creator_id) yang bisa mengubah status.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $user->id === $task->creator_id;
    }

    /**
     * Izin untuk menghapus tugas.
     */
    public function delete(User $user, Task $task): bool
    {
        // Hanya pembuat yang bisa menghapus.
        return $this->updateDetails($user, $task);
    }
}

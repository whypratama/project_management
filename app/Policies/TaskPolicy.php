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
     * Tentukan apakah user bisa mengupdate tugas.
     */
    public function update(User $user, Task $task): bool
    {
        // Aturan 1: User adalah pembuat asli tugas tersebut.
        if ($user->id === $task->creator_id) {
            return true;
        }

        // Aturan 2: User adalah orang yang ditugaskan, TAPI dia tidak bisa mengubah statusnya sendiri.
        // Logika ini akan kita terapkan di Controller, bukan di sini.
        // Di sini kita hanya berikan izin edit secara umum.
        if ($user->id === $task->assigned_to) {
            return true;
        }
        
        // Aturan 3: User adalah pembuat proyek (atasan tertinggi dalam konteks ini).
        return $user->id === $task->project->creator_id;
    }

    /**
     * Tentukan apakah user bisa menghapus tugas.
     */
    public function delete(User $user, Task $task): bool
    {
        // Hanya pembuat proyek atau pembuat tugas yang bisa menghapus.
        return $user->id === $task->project->creator_id || $user->id === $task->creator_id;
    }
}

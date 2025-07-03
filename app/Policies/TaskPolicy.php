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
     * Tentukan apakah user bisa melihat detail tugas.
     */
    public function view(User $user, Task $task): bool
    {
        // Aturan 1: Izinkan jika user adalah pembuat tugas.
        if ($user->id === $task->creator_id) {
            return true;
        }

        // Aturan 2: Izinkan jika user adalah orang yang ditugaskan (pemilik tugas).
        if ($user->id === $task->assigned_to) {
            return true;
        }

        // Aturan 3: Izinkan jika user adalah atasan dari orang yang ditugaskan.
        if ($task->assignee && $user->organization) {
            // Cek apakah organisasi si penugas ada di dalam lingkup organisasi user saat ini.
            $managerOrgScopeIds = array_unique(array_merge([$user->organization->id], $user->organization->getAllChildIds()));
            if (in_array($task->assignee->organization_id, $managerOrgScopeIds)) {
                return true;
            }
        }

        return false;
    }


    public function upload(User $user, Task $task): bool
    {
        // Aturan 1: Izinkan jika user adalah pembuat tugas.
        if ($user->id === $task->creator_id) {
            return true;
        }

        // Aturan 2: Izinkan jika user adalah orang yang ditugaskan (pemilik tugas).
        if ($user->id === $task->assigned_to) {
            return true;
        }

        // Aturan 3: Izinkan jika user adalah atasan dari orang yang ditugaskan.
        if ($task->assignee && $user->organization) {
            // Cek apakah organisasi si penugas ada di dalam lingkup organisasi user saat ini.
            $managerOrgScopeIds = array_unique(array_merge([$user->organization->id], $user->organization->getAllChildIds()));
            if (in_array($task->assignee->organization_id, $managerOrgScopeIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Izin umum untuk membuka form edit.
     */
    public function update(User $user, Task $task): bool
    {
        return $this->updateDetails($user, $task) || $this->updateStatus($user, $task);
    }

    /**
     * Izin untuk mengubah detail tugas (nama, deskripsi, dll).
     */
    public function updateDetails(User $user, Task $task): bool
    {
        return $user->id === $task->project->creator_id || $user->id === $task->creator_id;
    }

    /**
     * Izin untuk mengubah status tugas.
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
        return $this->updateDetails($user, $task);
    }
}

<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->id === $project->creator_id) {
            return true;
        }
        if ($user->organization && $user->jobTitle?->name !== 'Staff') {
            $managerOrgScopeIds = array_unique(array_merge([$user->organization->id], $user->organization->getAllChildIds()));
            if (in_array($project->organization_id, $managerOrgScopeIds)) {
                return true;
            }
        }
        if (Task::where('project_id', $project->id)->where('assigned_to', $user->id)->exists()) {
            return true;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->jobTitle && in_array($user->jobTitle->name, ['Direksi', 'Pemimpin Divisi', 'Pemimpin Departemen']);
    }

    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->creator_id;
    }

    /**
     * Tentukan apakah user bisa MENAMBAH TUGAS (Mendelegasikan).
     * Aturan ini sekarang lebih ketat dan tidak berlaku untuk Staff.
     */
    public function addTask(User $user, Project $project): bool
    {
        // Izinkan jika user adalah pembuat proyek.
        if ($user->id === $project->creator_id) {
            return true;
        }

        // Izinkan jika user adalah Atasan yang bertanggung jawab atas proyek ini.
        if ($user->organization && $user->jobTitle?->name !== 'Staff') {
            $managerOrgScopeIds = array_unique(array_merge([$user->organization->id], $user->organization->getAllChildIds()));
            if (in_array($project->organization_id, $managerOrgScopeIds)) {
                return true;
            }
        }

        return false;
    }


        public function addFile(User $user, Project $project): bool
    {
        // Izinkan jika user adalah pembuat proyek.
        if ($user->id === $project->creator_id) {
            return true;
        }

        // Izinkan jika user adalah Atasan yang bertanggung jawab atas proyek ini.
        if ($user->organization && $user->jobTitle?->name !== 'Staff') {
            $managerOrgScopeIds = array_unique(array_merge([$user->organization->id], $user->organization->getAllChildIds()));
            if (in_array($project->organization_id, $managerOrgScopeIds)) {
                return true;
            }
        }

        if (Task::where('project_id', $project->id)->where('assigned_to', $user->id)->exists()) {
            return true;
        }

        return false;
    }


    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->creator_id;
    }
}

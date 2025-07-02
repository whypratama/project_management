<?php

namespace App\Policies;

use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobTitlePolicy
{
    /**
     * Berikan semua akses ke Super Admin.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return null; // Lanjutkan ke pengecekan di bawah jika bukan Super Admin
    }

    /**
     * Hanya user dengan role 'Admin' yang bisa melihat daftar.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Hanya user dengan role 'Admin' yang bisa melihat detail.
     */
    public function view(User $user, JobTitle $jobTitle): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Hanya user dengan role 'Admin' yang bisa membuat.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Hanya user dengan role 'Admin' yang bisa mengupdate.
     */
    public function update(User $user, JobTitle $jobTitle): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Hanya user dengan role 'Admin' yang bisa menghapus.
     */
    public function delete(User $user, JobTitle $jobTitle): bool
    {
        return $user->hasRole('Admin');
    }
}

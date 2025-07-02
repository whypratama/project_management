<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Super Admin bisa melakukan segalanya.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return null;
    }

    /**
     * Tentukan apakah user bisa melihat daftar pengguna.
     * Hanya Admin yang bisa.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Tentukan apakah user bisa melihat detail pengguna lain.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Tentukan apakah user bisa membuat pengguna baru.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Tentukan apakah user bisa mengedit pengguna lain.
     */
    public function update(User $user, User $model): bool
    {
        // Admin bisa edit siapa saja kecuali Super Admin lain (jika ada)
        return $user->hasRole('Admin') && !$model->hasRole('Super Admin');
    }

    /**
     * Tentukan apakah user bisa menghapus pengguna lain.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin bisa hapus siapa saja kecuali Super Admin
        // dan tidak bisa menghapus diri sendiri.
        return $user->hasRole('Admin') && !$model->hasRole('Super Admin') && $user->id !== $model->id;
    }
}

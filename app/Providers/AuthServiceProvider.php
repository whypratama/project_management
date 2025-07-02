<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Daftarkan setiap Model dengan Policy-nya di sini
        User::class => UserPolicy::class,
        Organization::class => OrganizationPolicy::class,
        JobTitle::class => JobTitlePolicy::class,
        Project::class => ProjectPolicy::class, // Akan kita buat nanti
        Task::class => TaskPolicy::class,       // Akan kita buat nanti
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate ini akan dieksekusi sebelum semua pengecekan policy lainnya.
        // Jika user memiliki role 'Super Admin', semua pengecekan hak akses
        // akan langsung diizinkan (return true) tanpa perlu melanjutkan ke method di policy.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define who can manage enrollments (teachers and admins)
        Gate::define('manage-enrollments', function ($user) {
            return in_array($user->role, ['admin', 'teacher']);
        });

        // Define who can manage courses (teachers and admins)
        Gate::define('manage-courses', function ($user) {
            return in_array($user->role, ['admin', 'teacher']);
        });

        // Define who can manage assignments (teachers and admins)
        Gate::define('manage-assignments', function ($user) {
            return in_array($user->role, ['admin', 'teacher']);
        });

        // Define who can manage submissions (teachers and admins)
        Gate::define('manage-submissions', function ($user) {
            return in_array($user->role, ['admin', 'teacher']);
        });

        // Define who can submit assignments (students only)
        Gate::define('submit-assignments', function ($user) {
            return $user->role === 'student';
        });

        // Define who can view grades (student can view their own, teachers and admins can view all)
        Gate::define('view-grades', function ($user, $studentId = null) {
            return $user->role === 'admin' ||
                   $user->role === 'teacher' ||
                   ($user->role === 'student' && $user->id === $studentId);
        });
    }
}

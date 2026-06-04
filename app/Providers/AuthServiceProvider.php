<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Curriculum;
use App\Models\Role;
use App\Policies\UserPolicy;
use App\Policies\CurriculumPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Policies\UserPolicy as BaseUserPolicy;
use Illuminate\Auth\Policies\PasswordResetPolicy;
use Illuminate\Auth\Policies\EmailVerificationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Curriculum::class => CurriculumPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define a gate for role-based authorization
        Gate::define('has-role', function (User $user, string $role) {
            return $user->hasRole($role);
        });
    }
}

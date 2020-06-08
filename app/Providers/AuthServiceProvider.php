<?php

namespace App\Providers;

use App\Library;
use App\Manga;
use App\Policies\LibraryPolicy;
use App\Policies\MangaPolicy;
use App\Policies\RolePolicy;
use App\Role;
use App\User;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Library::class => LibraryPolicy::class,
        Role::class => RolePolicy::class,
        Manga::class => MangaPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-series', function (User $user, Manga $series) {
            return $user->can('update', $series);
        });
    }
}

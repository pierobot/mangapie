<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create a Role.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('create', Role::class);
    }

    /**
     * Determine whether the user can update a user's roles.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function update(User $user, Role $role)
    {
        /*
         * Technically, this is checking if the user has permission to update the specific Role,
         * but for our use, this is good enough. :shrug:
         */
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('update', $role);
    }

    /**
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function forceDelete(User $user, Role $role)
    {
        /*
         * Same comment as above in method `update`.
         */
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('forceDelete', $role);
    }
}

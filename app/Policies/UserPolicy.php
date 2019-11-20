<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the user profile.
     *
     * @param  \App\User  $user
     * @param  \App\User  $otherUser
     * @return bool
     */
    public function view(User $user, User $otherUser)
    {
        return true;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', User::class);
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\User  $user
     * @param  \App\User $targetUser
     * @return bool
     */
    public function update(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id ||
            $user->hasPermission('update', User::class);
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\User $targetUser
     * @return bool
     */
    public function delete(User $user, User $targetUser)
    {
        // TODO: allow users to delete their own accounts?
        return $user->hasPermission('delete', User::class);
    }

    /**
     * Determine whether the user can restore the user.
     *
     * @param  \App\User  $user
     * @param  \App\User $targetUser
     * @return bool
     */
    public function restore(User $user, User $targetUser)
    {
        return $user->hasPermission('restore', User::class);
    }

    /**
     * Determine whether the user can permanently delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\User $targetUser
     * @return bool
     */
    public function forceDelete(User $user, User $targetUser)
    {
        return $user->hasPermission('forceDelete', User::class);
    }
}

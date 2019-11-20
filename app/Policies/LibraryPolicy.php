<?php

namespace App\Policies;

use App\User;
use App\Library;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the library.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return bool
     */
    public function view(User $user, Library $library)
    {
        return $user->hasPermission('view', $library);
    }

    /**
     * Determine whether the user can create libraries.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', Library::class);
    }

    /**
     * Determine whether the user can update the library.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return bool
     */
    public function update(User $user, Library $library)
    {
        return $user->hasPermission('update', Library::class);
    }

    /**
     * Determine whether the user can delete the library.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return bool
     */
    public function delete(User $user, Library $library)
    {
        return $user->hasPermission('delete', Library::class);
    }

    /**
     * Determine whether the user can restore the library.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return bool
     */
    public function restore(User $user, Library $library)
    {
        return $user->hasPermission('restore', Library::class);
    }

    /**
     * Determine whether the user can permanently delete the library.
     *
     * @param  \App\User  $user
     * @param  \App\Library  $library
     * @return bool
     */
    public function forceDelete(User $user, Library $library)
    {
        return $user->hasPermission('forceDelete', Library::class);
    }
}

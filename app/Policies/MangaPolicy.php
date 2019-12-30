<?php

namespace App\Policies;

use App\Manga;
use App\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class MangaPolicy
{
    use HandlesAuthorization;

    /**
     * Determines whether the user can view any manga.
     * This is always true as library access will be restricted in the view method.
     * If this is false, MangaController@show will fail.
     *
     * @return bool
     */
//    public function viewAny(User $user)
//    {
//        return true;
//    }

    /**
     * Determine whether the user can view the manga.
     *
     * @param  \App\User  $user
     * @param  \App\Manga  $manga
     * @return bool
     */
    public function view(User $user, Manga $manga)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('view', $manga->library) ||
            $user->hasPermission('view', $manga);
    }

    /**
     * Determine whether the user can create manga.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('create', Manga::class);
    }

    /**
     * Determine whether the user can update the manga.
     *
     * @param  \App\User  $user
     * @param  \App\Manga  $manga
     * @return bool
     */
    public function update(User $user, Manga $manga)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('update', Manga::class);
    }

    /**
     * Determine whether the user can delete the manga.
     *
     * @param  \App\User  $user
     * @param  \App\Manga  $manga
     * @return bool
     */
    public function delete(User $user, Manga $manga)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('delete', Manga::class);
    }

    /**
     * Determine whether the user can restore the manga.
     *
     * @param  \App\User  $user
     * @param  \App\Manga  $manga
     * @return bool
     */
    public function restore(User $user, Manga $manga)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('restore', Manga::class);
    }

    /**
     * Determine whether the user can permanently delete the manga.
     *
     * @param  \App\User  $user
     * @param  \App\Manga  $manga
     * @return bool
     */
    public function forceDelete(User $user, Manga $manga)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('forceDelete', Manga::class);
    }
}

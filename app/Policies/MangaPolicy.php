<?php

namespace App\Policies;

use App\Manga;
use App\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class MangaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the manga.
     *
     * @param  \App\User  $user
     * @param  \App\Manga  $manga
     * @return bool
     */
    public function view(User $user, Manga $manga)
    {
        return $user->can('view', $manga->library);
    }

    /**
     * Determine whether the user can create manga.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', Manga::class);
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
        return $user->hasPermission('update', Manga::class);
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
        return $user->hasPermission('delete', Manga::class);
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
        return $user->hasPermission('restore', Manga::class);
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
        return $user->hasPermission('forceDelete', Manga::class);
    }
}

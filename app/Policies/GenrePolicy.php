<?php

namespace App\Policies;

use App\Genre;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GenrePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Genre $genre
     * @return bool
     */
    public function view(User $user, Genre $genre)
    {
        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', Genre::class);
    }

    /**
     * @param User $user
     * @param Genre $genre
     * @return bool
     */
    public function delete(User $user, Genre $genre)
    {
        return $user->hasPermission('delete', Genre::class);
    }

    /**
     * @param User $user
     * @param Genre $genre
     * @return bool
     */
    public function forceDelete(User $user, Genre $genre)
    {
        return $user->hasPermission('forceDelete', Genre::class);
    }

    /**
     * @param User $user
     * @param Genre $genre
     * @return bool
     */
    public function restore(User $user, Genre $genre)
    {
        return $user->hasPermission('restore', Genre::class);
    }

    /**
     * @param User $user
     * @param Genre $genre
     * @return bool
     */
    public function update(User $user, Genre $genre)
    {
        return $user->hasPermission('update', Genre::class);
    }
}

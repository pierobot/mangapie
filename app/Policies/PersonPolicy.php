<?php

namespace App\Policies;

use App\Person;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Person $person
     * @return bool
     */
    public function view(User $user, Person $person)
    {
        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', Person::class);
    }

    /**
     * @param User $user
     * @param Person $person
     * @return bool
     */
    public function delete(User $user, Person $person)
    {
        return $user->hasPermission('delete', Person::class);
    }

    /**
     * @param User $user
     * @param Person $person
     * @return bool
     */
    public function forceDelete(User $user, Person $person)
    {
        return $user->hasPermission('forceDelete', Person::class);
    }

    /**
     * @param User $user
     * @param Person $person
     * @return bool
     */
    public function restore(User $user, Person $person)
    {
        return $user->hasPermission('restore', Person::class);
    }

    /**
     * @param User $user
     * @param Person $person
     * @return bool
     */
    public function update(User $user, Person $person)
    {
        return $user->hasPermission('update', Person::class);
    }
}

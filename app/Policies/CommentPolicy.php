<?php

namespace App\Policies;

use App\Comment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function view(User $user, Comment $comment)
    {
        // TODO: Account for user blocking

        return ! $user->hasRole('Banned');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('create', Comment::class);
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('delete', Comment::class);
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function forceDelete(User $user, Comment $comment)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('forceDelete', Comment::class);
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function restore(User $user, Comment $comment)
    {
        return ! $user->hasRole('Banned') &&
            $user->hasPermission('restore', Comment::class);
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function update(User $user, Comment $comment)
    {
        return ! $user->hasRole('Banned') &&
            ($user->hasPermission('update', Comment::class) ||
            $comment->user == $user);
    }
}

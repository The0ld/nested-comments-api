<?php

namespace App\Policies;

use App\Models\{User, Comment};

class CommentPolicy
{
    /**
     * Determine if the given comment can be updated by the user.
     */
    public function update(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine if the given comment can be deleted by the user.
     */
    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }
}


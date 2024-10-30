<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Comment;

class CommentRepository
{
    /**
     * Get paginated root comments with user and replies.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRootComments($orderBy, $direction)
    {
        $allowedColumns = ['created_at', 'updated_at', 'user_id'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'created_at';
        }

        if (!in_array($direction, $allowedDirections)) {
            $direction = 'asc';
        }

        return Comment::with(['user', 'replies'])
                      ->whereNull('parent_id')
                      ->orderBy($orderBy, $direction)
                      ->paginate(50);
    }

    /**
     * Create a new comment.
     *
     * @param  \App\Models\User  $user
     * @param  array  $data
     * @return \App\Models\Comment
     */
    public function createComment(User $user, array $data): Comment
    {
        return $user->comments()->create($data);
    }

    /**
     * Update an existing comment.
     *
     * @param  \App\Models\Comment  $comment
     * @param  array  $data
     * @return void
     */
    public function updateComment(Comment $comment, array $data)
    {
        $comment->update($data);
    }

    /**
     * Delete a comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function deleteComment(Comment $comment)
    {
        $comment->delete();
    }
}

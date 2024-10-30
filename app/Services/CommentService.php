<?php

namespace App\Services;

use App\Models\User;
use App\Models\Comment;
use App\Events\CommentPosted;
use App\Listeners\SendNewCommentNotification;
use App\Repositories\CommentRepository;
use Illuminate\Support\Facades\Gate;

class CommentService
{
    protected $commentRepository;

    /**
     * Inject the CommentRepository.
     *
     * @param \App\Repositories\CommentRepository  $commentRepository
     */
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Get paginated root comments with user and replies.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getRootComments($orderBy, $direction)
    {
        return $this->commentRepository->getRootComments($orderBy, $direction);
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
        $comment = $this->commentRepository->createComment($user, $data);

        event(new CommentPosted($comment));

        return $comment;
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
        Gate::authorize('update', $comment);

        $this->commentRepository->updateComment($comment, $data);
    }

    /**
     * Delete a comment.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function deleteComment(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $this->commentRepository->deleteComment($comment);
    }
}

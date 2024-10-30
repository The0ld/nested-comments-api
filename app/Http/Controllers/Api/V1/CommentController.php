<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\{CreateCommentRequest, EditCommentRequest, GetCommentsRequest};
use App\Http\Resources\CommentResource;
use App\Services\CommentService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    protected $commentService;

    /**
     * Inject the CommentService into the constructor.
     *
     * @param  \App\Services\CommentService  $commentService
     */
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the root comments.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(GetCommentsRequest $request): AnonymousResourceCollection
    {
        $orderBy = $request->input('orderBy');
        $direction = $request->input('direction');

        $comments = $this->commentService->getRootComments($orderBy, $direction);

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param  \App\Http\Requests\CreateCommentRequest  $request
     * @return \App\Http\Resources\CommentResource
     */
    public function store(CreateCommentRequest $request): CommentResource
    {
        $comment = $this->commentService->createComment($request->user(), $request->validated());

        return new CommentResource($comment);
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \App\Http\Requests\EditCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \App\Http\Resources\CommentResource
     */
    public function update(EditCommentRequest $request, Comment $comment): CommentResource
    {
        $this->commentService->updateComment($comment, $request->validated());

        return new CommentResource($comment->refresh()); // Refresh the comment to get updated data
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        $this->commentService->deleteComment($comment);

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}


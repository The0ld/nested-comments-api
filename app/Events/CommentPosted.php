<?php

namespace App\Events;

use App\Models\Comment;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CommentPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        Log::info('Event Comment Posted dispatched with comment: ' . $comment->id);
        $this->comment = $comment;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function broadcastOn()
    {
        return new Channel('comments');
    }

    public function broadcastAs()
    {
        return 'comment.posted';
    }
}

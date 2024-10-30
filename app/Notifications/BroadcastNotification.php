<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BroadcastNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @param string $messageContent
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage;
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'comment' => [
                'id' => $this->comment->id,
                'comment_text' => $this->comment->comment,
                'author' => [
                    'id' => $this->comment->user->id,
                    'name' => $this->comment->user->name,
                ],
                'created_at' => $this->comment->created_at->toDateTimeString(),
            ],
        ]);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('comments');
    }

    public function broadcastAs()
    {
        return 'comment.posted';
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}


<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;
    protected $channels;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Comment $comment
     * @param array $channels
     */
    public function __construct(Comment $comment, array $channels)
    {
        $this->comment = $comment;
        $this->channels = $channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Comment Received')
            ->line('A new comment has been posted.')
            ->line('Comment: ' . $this->comment->comment)
            ->line('Thank you for using our application!');
    }

    /**
     * Simulate the SMS sending by logs.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toSms($notifiable)
    {
        return [
            'message' => 'New comment received: ' . $this->comment->comment,
        ];
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

    /**
     * Define the channel for broadcasting.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('comments');
    }

    /**
     * Define the event name for broadcasting.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'comment.posted';
    }

    /**
     * Obtener el comentario.
     *
     * @return \App\Models\Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }
}


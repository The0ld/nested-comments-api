<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'sms', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Comment Received')
                    ->line('A new comment has been posted.')
                    ->line('Comment: ' . $this->comment->comment)
                    //->action('View Comment', url('/comments/' . $this->comment->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {
        return "New comment from {$this->comment->user->name}: {$this->comment->comment}";
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
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
     * Get the array representation of the notification.
     *
     * @param mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'comment_id' => $this->comment->id,
            'comment' => $this->comment->comment,
            'user_name' => $this->comment->user->name,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailNotification extends Notification implements ShouldQueue
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
        return ['mail'];
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
                            ->action('View Comment', url('/comments/' . $this->comment->id))
                            ->line('Thank you for using our application!');
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}


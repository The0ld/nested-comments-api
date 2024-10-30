<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SMSNotification extends Notification implements ShouldQueue
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
        return ['sms'];
    }

    /**
     * Simulate the SMS sending by logs.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function toSms($notifiable)
    {
        return [
            'message' => 'New comment received: ' . $this->comment->comment,
        ];
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}


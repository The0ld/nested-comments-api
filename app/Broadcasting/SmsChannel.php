<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        // Simulate sending SMS by logging the message
        Log::channel('sms')->info('SMS to ' . $notifiable->phone_number . ': ' . $message);
    }
}

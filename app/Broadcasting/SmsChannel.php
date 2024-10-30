<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        // Simulate sending SMS by logging the message
        Log::channel('sms')->info('SMS to ' . $notifiable->phone_number . ': ' . $message['message']);
    }
}


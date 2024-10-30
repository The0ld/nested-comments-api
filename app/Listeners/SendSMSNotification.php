<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Models\User;
use App\Notifications\SMSNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSMSNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CommentPosted $event)
    {
        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            $admin->notify(new SMSNotification($event->comment));
        }
    }
}


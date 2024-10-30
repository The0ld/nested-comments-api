<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Models\User;
use App\Notifications\EmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CommentPosted $event)
    {
        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            $admin->notify(new EmailNotification($event->comment));
        }
    }
}


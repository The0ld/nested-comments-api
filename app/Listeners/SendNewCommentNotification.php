<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewCommentNotification;
use App\Events\CommentPosted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewCommentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CommentPosted $event): void
    {
        $admins = User::where('is_admin', true)->cursor(); // Usar cursor para eficiencia

        foreach ($admins as $admin) {
            $admin->notify(new NewCommentNotification($event->comment));
        }
    }
}

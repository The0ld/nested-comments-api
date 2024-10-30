<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Models\User;
use App\Notifications\BroadcastNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBroadcastNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(CommentPosted $event): void
    {
        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            $admin->notify(new BroadcastNotification($event->comment));
        }
    }
}

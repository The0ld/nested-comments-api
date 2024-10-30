<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Models\User;
use App\Notifications\CommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;

class SendNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CommentPosted $event)
    {
        $admins = User::where('is_admin', true)->get();

        $availableChannels = config('notifications.channels');

        $channels = [];
        foreach ($availableChannels as $channel => $settings) {
            if (Arr::get($settings, 'enabled', false)) {
                $channels[] = $channel;
            }
        }

        foreach ($admins as $admin) {
            $admin->notify(new CommentNotification($event->comment, $channels));
        }
    }
}

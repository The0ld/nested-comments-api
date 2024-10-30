<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use App\Events\CommentPosted;
use App\Notifications\CommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Arr;

class CommentPostedNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('queue.default', 'sync');

        Notification::extend('sms', function ($app) {
            return new \App\Broadcasting\SmsChannel();
        });

        $this->app['config']->set('notifications.channels', [
            'mail' => [
                'enabled' => true,
            ],
            'sms' => [
                'enabled' => true,
            ],
            'broadcast' => [
                'enabled' => true,
            ],
        ]);
    }

    /**
     * Test that CommentNotification is sent to admin users through enabled channels when a comment is posted.
     */
    public function test_comment_notification_sent_to_admins_via_enabled_channels_when_comment_posted()
    {
        Notification::fake();

        $admin1 = User::factory()->create(['is_admin' => true, 'email' => 'admin1@example.com']);
        $admin2 = User::factory()->create(['is_admin' => true, 'email' => 'admin2@example.com']);
        $nonAdmin = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);

        $comment = Comment::factory()->create();

        event(new CommentPosted($comment));

        $availableChannels = config('notifications.channels');
        $enabledChannels = [];
        foreach ($availableChannels as $channel => $settings) {
            if (Arr::get($settings, 'enabled', false)) {
                $enabledChannels[] = $channel;
            }
        }

        Notification::assertSentTo(
            [$admin1, $admin2],
            CommentNotification::class,
            function ($notification, $channels) use ($comment, $enabledChannels) {
                return !array_diff($channels, $enabledChannels) && $notification->getComment()->id === $comment->id;
            }
        );

        Notification::assertNotSentTo($nonAdmin, CommentNotification::class);
    }

    /**
     * Test that no notifications are sent if no channels are enabled.
     */
    public function test_no_notifications_sent_when_no_channels_enabled()
    {
        $this->app['config']->set('notifications.channels', [
            'mail' => [
                'enabled' => false,
            ],
            'sms' => [
                'enabled' => false,
            ],
            'broadcast' => [
                'enabled' => false,
            ],
        ]);

        Notification::fake();

        $admin1 = User::factory()->create(['is_admin' => true, 'email' => 'admin1@example.com']);
        $admin2 = User::factory()->create(['is_admin' => true, 'email' => 'admin2@example.com']);
        $nonAdmin = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);

        $comment = Comment::factory()->create();

        event(new CommentPosted($comment));

        Notification::assertNotSentTo([$admin1, $admin2], CommentNotification::class);

        Notification::assertNotSentTo($nonAdmin, CommentNotification::class);
    }

    /**
     * Test that only enabled channels are used when sending notifications.
     */
    public function test_only_enabled_channels_are_used_when_sending_notifications()
    {
        $this->app['config']->set('notifications.channels', [
            'mail' => [
                'enabled' => true,
            ],
            'sms' => [
                'enabled' => false,
            ],
            'broadcast' => [
                'enabled' => true,
            ],
        ]);

        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true, 'email' => 'admin@example.com']);

        $comment = Comment::factory()->create();

        event(new CommentPosted($comment));

        $availableChannels = config('notifications.channels');
        $enabledChannels = [];
        foreach ($availableChannels as $channel => $settings) {
            if (Arr::get($settings, 'enabled', false)) {
                $enabledChannels[] = $channel;
            }
        }

        Notification::assertSentTo(
            [$admin],
            CommentNotification::class,
            function ($notification, $channels) use ($comment, $enabledChannels) {
                return !array_diff($channels, $enabledChannels) && $notification->getComment()->id === $comment->id;
            }
        );

        Notification::assertSentTo(
            [$admin],
            CommentNotification::class,
            function ($notification, $channels) {
                return !in_array('sms', $channels) && !in_array('slack', $channels);
            }
        );
    }
}


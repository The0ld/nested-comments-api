<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use App\Events\CommentPosted;
use App\Notifications\EmailNotification;
use App\Notifications\SmsNotification;
use App\Notifications\BroadcastNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CommentPostedNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar la cola para procesarse sincrónicamente durante las pruebas
        $this->app['config']->set('queue.default', 'sync');

        // Registrar el canal 'sms' personalizado para las pruebas
        Notification::extend('sms', function ($app) {
            return new \App\Broadcasting\SmsChannel();
        });
    }

    /**
     * Test that email notifications are sent to admin users when a comment is posted.
     */
    public function test_email_notification_sent_to_admins_when_comment_posted()
    {
        Notification::fake();

        $admin1 = User::factory()->create(['is_admin' => true, 'email' => 'admin1@example.com']);
        $admin2 = User::factory()->create(['is_admin' => true, 'email' => 'admin2@example.com']);
        $nonAdmin = User::factory()->create(['is_admin' => false, 'email' => 'user@example.com']);

        $comment = Comment::factory()->create();

        event(new CommentPosted($comment));

        Notification::assertSentTo(
            [$admin1, $admin2],
            EmailNotification::class,
            function ($notification, $channels) use ($comment) {
                return in_array('mail', $channels) && $notification->getComment()->id === $comment->id;
            }
        );

        Notification::assertNotSentTo($nonAdmin, EmailNotification::class);
    }

    /**
     * Test that broadcast notifications are sent to admin users when a comment is posted.
     */
    public function test_broadcast_notification_sent_to_admins_when_comment_posted()
    {
        Notification::fake();

        $admin1 = User::factory()->create(['is_admin' => true]);
        $admin2 = User::factory()->create(['is_admin' => true]);
        $nonAdmin = User::factory()->create(['is_admin' => false]);

        $comment = Comment::factory()->create();

        event(new CommentPosted($comment));

        Notification::assertSentTo(
            [$admin1, $admin2],
            BroadcastNotification::class,
            function ($notification, $channels) use ($comment) {
                return in_array('broadcast', $channels) && $notification->getComment()->id === $comment->id;
            }
        );

        Notification::assertNotSentTo($nonAdmin, BroadcastNotification::class);
    }
}


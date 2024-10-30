<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Broadcasting\SmsChannel;
use App\Events\CommentPosted;
use App\Listeners\SendNewCommentNotification;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        CommentPosted::class => [
            SendNewCommentNotification::class,
        ],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notification::extend('sms', function ($app) {
            return new SmsChannel();
        });
    }
}

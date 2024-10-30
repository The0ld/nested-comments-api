<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // You can  add event-listener mappings here if needed in the future
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}

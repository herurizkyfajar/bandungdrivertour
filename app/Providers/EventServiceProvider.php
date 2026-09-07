<?php

namespace App\Providers;

use App\Events\BookingCreated;
use App\Events\BookingUpdated;
use App\Events\BookingDeleted;
use App\Listeners\SyncBookingToSheet;
use App\Listeners\UpdateBookingInSheet;
use App\Listeners\RemoveBookingFromSheet;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            SyncBookingToSheet::class,
        ],
        BookingUpdated::class => [
            UpdateBookingInSheet::class,
        ],
        BookingDeleted::class => [
            RemoveBookingFromSheet::class,
        ],
    ];

    public function boot(): void
    {
    }
}

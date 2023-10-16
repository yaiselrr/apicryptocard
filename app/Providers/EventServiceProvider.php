<?php

namespace App\Providers;

use App\Events\AbsenceNotification;
use App\Events\KidNotification;
use App\Events\ProductRequestNotification;
use App\Events\RealTimeTokenUpdate;
use App\Events\TicketAnswareNotification;
use App\Events\TicketNotification;
use App\Listeners\SendPickUpRequestNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

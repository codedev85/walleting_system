<?php

namespace App\Providers;

use App\Events\CashOutEvent;
use App\Events\VerificationMail;
use App\Events\WalletFundingEvent;
use App\Listeners\CashOutListener;
use App\Listeners\VerificationMailListener;
use App\Listeners\WalletFundingListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        VerificationMail::class => [
            VerificationMailListener::class
        ],
        WalletFundingEvent::class => [
            WalletFundingListener::class
        ],
        CashOutEvent::class => [
            CashOutListener::class
        ]
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

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}

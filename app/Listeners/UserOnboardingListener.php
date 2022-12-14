<?php

namespace App\Listeners;

use App\Events\UserOnboardingEVent;
use App\Mail\UserUnboarding;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class UserOnboardingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserOnboardingEVent  $event
     * @return void
     */
    public function handle(UserOnboardingEVent $event)
    {
        Mail::to($event['email'])->send(
            new UserUnboarding($event['user'], $event['password'])
        );
    }
}

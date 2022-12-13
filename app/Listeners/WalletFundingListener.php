<?php

namespace App\Listeners;

use App\Events\WalletFundingEvent;
use App\Mail\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class WalletFundingListener
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
     * @param  \App\Events\WalletFundingEvent  $event
     * @return void
     */
    public function handle(WalletFundingEvent $event)
    {
        Mail::to($event->user->email)->send(
            new VerifyEmail($event->user, $event->amount)
        );
    }
}

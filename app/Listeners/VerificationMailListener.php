<?php

namespace App\Listeners;

use App\Events\VerificationMail;
use App\Mail\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class VerificationMailListener
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
     * @param  \App\Events\VerificationMail  $event
     * @return void
     */
    public function handle(VerificationMail $event)
    {
        Mail::to($event->user->email)->send(
            new VerifyEmail($event->user, $event->token)
        );
    }
}

<?php

namespace App\Listeners;

use App\Events\CashOutEvent;
use App\Mail\CashOutMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CashOutListener
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
     * @param  \App\Events\CashOutEvent  $event
     * @return void
     */
    public function handle(CashOutEvent $event)
    {
        Mail::to($event->user->email)->send(
            new CashOutMail($event->user, $event->amount)
        );
    }
}

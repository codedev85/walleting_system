<?php

namespace App\Jobs;

use App\Mail\ActivationMail;
use App\Mail\SuspensionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ActivationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $user;
    public $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user ,$type)
    {
        $this->user = $user;
        $this->type = $type;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(
            new ActivationMail($this->user, $this->type)
        );
    }
}

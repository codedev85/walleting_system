<?php

namespace App\Jobs;

use App\Mail\AdminNotifier;
use App\Mail\UserUnboarding;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AdminNotifierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $adminEmail;
    public $amount;
    public $user;
    public $bank;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($adminEmail , $amount , $user , $bank)
    {
        $this->adminEmail = $adminEmail;
        $this->amount    = $amount;
        $this->user      = $user;
        $this->bank      = $bank;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->adminEmail)->send(
            new AdminNotifier($this->user,$this->amount, $this->bank)
        );
    }
}

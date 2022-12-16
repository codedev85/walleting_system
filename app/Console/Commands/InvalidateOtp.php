<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class InvalidateOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invalidate:otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command runs every 15 minutes to check which otp is due to invalidate';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $otps = Otp::where('expires_at', null)->get();

        foreach($otps as $otp){
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', now());
            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $otp->expires_at);
            $diff_in_minutes = $to->diffInMinutes($from);
            if($diff_in_minutes > 15){
                $otp->update(['expires_at', now()]);
            }
//            print_r($diff_in_minutes);
        }

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\WithdrawalPin;
use Illuminate\Console\Command;

class InvalidateWithdrawalPin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invalidate:withdrawal-pin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that invalidates withdrawal pin after 15 minutes of not being used ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pins = WithdrawalPin::where('expires_at', null)->get();
        foreach($pins as $pin){
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', now());
            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $pin->expires_at);
            $diff_in_minutes = $to->diffInMinutes($from);
            if($diff_in_minutes > 15){
                $pin->update(['expires_at', now()]);
            }
        }
        return Command::SUCCESS;
    }
}

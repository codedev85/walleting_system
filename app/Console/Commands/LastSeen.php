<?php

namespace App\Console\Commands;

use App\Models\LastSeen as LastSeenActivity;
use Illuminate\Console\Command;


class LastSeen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logout:in-active-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command will logout suers that have not been active for 2 mins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lastseen = LastSeenActivity::get();
        foreach($lastseen as $activity){
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', now());
            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $activity->last_seen);
            $diff_in_minutes = $to->diffInMinutes($from);
            if($diff_in_minutes > 2){
                $activity->user->tokens()->delete();
                $activity->update(['last_seen', now()]);
            }
        }
        return Command::SUCCESS;
    }
}

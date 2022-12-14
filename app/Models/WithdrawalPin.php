<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalPin extends Model
{
    use HasFactory ,UUID;

    public function user(){
        return $this->belongsTo(User::class);
    }
}

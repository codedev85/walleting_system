<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, UUID;

    protected $guarded = ['id'];

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}

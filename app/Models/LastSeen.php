<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastSeen extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'last_seen';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}

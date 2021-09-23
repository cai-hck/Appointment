<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Chat extends Model
{
    //
    use Notifiable;

    protected $guarded = [];

    public function booking(){

        return $this->belongsTo(Booking::class);
        
    }

    
    public function user(){

        return $this->belongsTo(User::class);
        
    }
}

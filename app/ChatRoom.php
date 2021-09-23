<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    //
    protected  $guarded = [];

    public function Booking()
    {
        return $this->belongsTo('App\Booking');
    }
}

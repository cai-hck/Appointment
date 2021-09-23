<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $guarded = ['id'];
    
    protected function Verify()
    {
        return $this->hasOne('App\ClientVerify');
    }
    protected function Bookings()
    {
        return $this->hasMany('App\Booking');
    }

    protected function Meetingroom()
    {
        return $this->hasMany('App\MeetingRoom');
    }
}

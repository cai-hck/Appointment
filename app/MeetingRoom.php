<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    //
    protected $guarded = ['id'];

    protected function Consultant()
    {
        return $this->belongsTo('App\Consultant');
    }
    protected function User()
    {
        return $this->belongsTo('App\User');
    }
    protected function Booking()
    {
        return $this->belongsTo('App\Booking');
    }    
    protected function Client()
    {
        return $this->belongsTo('App\Client');
    }
    protected function Mission()
    {
        return $this->belongsTo('App\Mission');
    }
    protected function Section()
    {
        return $this->belongsTo('App\Consultant');
    }
}

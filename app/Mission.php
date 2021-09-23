<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    //
    protected $guarded = ['id'];
    
    protected function Consultant()
    {
       return  $this->hasOne('App\Consultant');
    }
    protected function Section()
    {
        return $this->hasOne('App\Section');
    }
    protected function Bookings()
    {
        return $this->hasMany('App\Bookings');
    }

    protected function Meetingroom()
    {
        return $this->hasMany('App\MeetingRoom');
    }

    protected function setting()
    {
        return $this->hasOne('App\MissionSetting');
    }

    protected function news()
    {
        return $this->hasMany('App\MissionNews');
    }
}

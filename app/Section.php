<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\SectionCreated;
class Section extends Model
{
    //
    use Notifiable;
    
    protected $guarded = ['id'];

    protected function Mission()
    {
        return $this->belongsTo('App\Mission');
    }

    protected function Bookings()
    {
        return $this->hasMany('App\Booking');
    }

    protected function Meetingroom()
    {
        return $this->hasMany('App\MeetingRoom');
    }

    protected function info()
    {
        return $this->hasOne('App\SectionInfo');
    }
}

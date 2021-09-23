<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\AppointmentBooked;
use Thomasjohnkane\Snooze\Traits\SnoozeNotifiable;


class Booking extends Model
{
    //
    use Notifiable;
    use SnoozeNotifiable;
    public function routeNotificationForMail($notification)
    {
       // Return email address only
       return $this->client->email;
    }
    protected $guarded = ['id'];

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
        return $this->belongsTo('App\Section');
    }    
    protected function files()
    {
        return $this->hasMany('App\BookingFile');
    }

    protected function get_today_bookings()
    {
        return self::where('schedule_date', date('Y-m-d'))->get()->count();
    }

    protected function Extrafile()
    {
        $this->belongsTo('App\AddLink');
    }

    protected function Meetingroom()
    {
        return $this->hasOne('App\MeetingRoom');
    }

    public function messages(){
        return $this->hasMany(Chat::class);       
    }
    
    public function Chatroom()
    {
        return $this->hasOne(ChatRoom::class);       
    }

}

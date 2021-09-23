<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    //
    protected $guarded = ['id'];

    protected function Mission() {
        return $this->belongsTo('App\Mission');
    }
    protected function User() {
        return $this->belongsTo('App\User');
    }
    protected function Secretary() {
        return $this->hasOne('App\Secretary');
    }

    protected function Transaction() {
        return $this->hasMany('App\Transaction');
    }

    static protected function get_mission_users($mission_id)
    {
        return self::select('user_infos.fname as fname',
                    'user_infos.lname as lname','users.email as email',
                    'user_infos.mobile as phone','user_infos.whatsapp as whatsapp',
                    'user_infos.notify_email as notify_email',
                    'user_infos.notify_phone as notify_phone',
                    'user_infos.notify_whatsapp as notify_whatsapp'
                    )
        ->leftjoin('users', 'users.id','=','consultants.user_id')
        ->leftjoin('user_infos','users.id','=','user_infos.user_id')
        ->where('consultants.mission_id', $mission_id)->get();
    }

    protected function Meetingroom()
    {
        return $this->hasMany('App\MeetingRoom');
    }
}

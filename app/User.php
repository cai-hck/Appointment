<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\AccountCreated;

class User extends Authenticatable
{
    use Notifiable;

    public function routeNotificationForMail($notification)
    {
       // Return email address only
       return $this->email;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    protected function Userinfo()
    {
        return $this->hasOne('App\UserInfo');
    }

    protected function Consultant()
    {
        return $this->hasOne('App\Consultant');
    }

    protected function Secretary()
    {
        return $this->hasOne('App\Secretary');
    }
    protected function Schedule()
    {
        return $this->hasMany('App\Schedule');
    }

    protected function Meetingroom()
    {
        return $this->hasMany('App\MeetingRoom');
    }

    public function messages(){
        return $this->hasMany(Chat::class);       
    }

    public function internalmessages() {
        return $this->hasMany(InternalChat::class);       
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ScheduleCreated;

class Schedule extends Model
{
    //
    use Notifiable;
    
    protected $guarded = ['id'];
    

    public function User()
    {
        return $this->belongsTo('App\User');
    }
}

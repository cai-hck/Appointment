<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ScheduleTimingCreated;
class ScheduleTiming extends Model
{
    //
    use Notifiable;
    
    protected $guarded = ['id'];
}

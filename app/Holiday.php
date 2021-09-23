<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\HolidayCreated;
class Holiday extends Model
{
    //
    use Notifiable;

    protected $guarded = ['id'];

    static protected function isHoliday($date)
    {
        $exist = self::where('holiday_date', $date)->get()->first();
        if ($exist) return true;
        return false;
    }
}

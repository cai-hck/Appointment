<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingFile extends Model
{
    //

    protected $guarded = ['id'];

    protected function Booking ()
    {
        return $this->belongsTo('App\Booking');
    }
}

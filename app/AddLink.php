<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddLink extends Model
{
    //
    protected $guarded = ['id'];

    protected function Booking()
    {   
        return $this->hasOne('App\Booking');
    }
}

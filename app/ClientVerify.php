<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientVerify extends Model
{
    //
    protected $guarded = ['id'];
 
    protected function Client()
    {
        return $this->belongsTo('App\Client');
    }
}

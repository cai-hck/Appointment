<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MissionNews extends Model
{
    //
    protected $guarded = ['id'];
    
    protected function mission() 
    {
        return $this->belongsTo('App\Mission');
    }
}

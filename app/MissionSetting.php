<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MissionSetting extends Model
{
    //
    protected $guarded = ['id'];

    public function mission()
    {
        return $this->belongsTo('App\Mission');
    }
}

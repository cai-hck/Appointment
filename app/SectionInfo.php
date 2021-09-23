<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SectionInfo extends Model
{
    //
    protected $guarded = ['id'];

    protected function Section()
    {
        return $this->belongsTo('App\Section');
    }
}

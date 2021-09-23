<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    //
    protected $guarded = ['id'];

    static public function get_value($name) {
        if ($name == '' || $name == NULL) return false;
        $row = self::where('name', $name);    
        if ($row->exists()) {          
            return $row->get()->first()->value;
        }
        else
            return false;
    }
}

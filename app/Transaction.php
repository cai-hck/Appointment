<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\TransactionCreated;
class Transaction extends Model
{
    //
    use Notifiable;

    protected $guraded = ['id'];
    protected $fillable = [
        'consultant_id','date','amount','status','about','accept_date'
    ];
    protected function Consultant()
    {
        return $this->belongsTo('App\Consultant');
    }

}

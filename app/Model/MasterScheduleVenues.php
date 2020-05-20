<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterScheduleVenues extends Model
{
    //
    protected $table = 'mst_schedule_venues';
    protected $fillable = [
        'open_date', 'until_open_date', 'open_time', 'until_open_time','status','user_id','venues_id'
    ];
}

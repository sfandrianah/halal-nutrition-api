<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterVenues extends Model
{
    //
    protected $table = 'mst_venues';
    protected $fillable = [
        'code', 'name', 'food_type', 'restaurant_status_id','status','user_id','address','longitude','latitude','phone_number'
    ];
}

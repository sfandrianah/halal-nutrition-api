<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterNutrition extends Model
{
    //
    protected $table = 'mst_nutrition';
    protected $fillable = [
        'code', 'name', 'value', 'unit_code','percentage','order'
    ];
}

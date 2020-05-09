<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    //
    protected $table = 'mst_item';
    protected $fillable = [
        'code', 'name', 'manufacture', 'ingredient','status','user_id'
    ];
}

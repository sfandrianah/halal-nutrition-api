<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterNews extends Model
{
    //
    protected $table = 'mst_news';
    protected $fillable = [
        'code', 'name', 'content','status','user_id'
    ];
}

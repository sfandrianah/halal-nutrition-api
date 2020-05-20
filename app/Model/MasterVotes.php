<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterVotes extends Model
{
    //
    protected $table = 'mst_votes';
    protected $fillable = [
        'value', 'reference_id', 'reference_table','status','user_id'
    ];
}

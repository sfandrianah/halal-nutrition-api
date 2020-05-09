<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterAttachment extends Model
{
    //
    protected $table = 'mst_attachment';
    protected $fillable = [
        'reference_id', 'reference_table', 'status', 'path', 'user_id', 'filename','type','mime'
    ];
}

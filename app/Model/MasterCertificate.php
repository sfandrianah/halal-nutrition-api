<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterCertificate extends Model
{
    //
    protected $table = 'mst_certificate';
    protected $fillable = [
        'code', 'name', 'organization_name', 'certificate_status_id', 'item_id', 'expired_date'
    ];
}

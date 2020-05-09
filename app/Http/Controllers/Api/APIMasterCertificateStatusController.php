<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\MasterCertificateStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class APIMasterCertificateStatusController extends APIController
{
    public function __construct()
    {
        $this->model = MasterCertificateStatus::class;
        parent::__construct();
    }


}

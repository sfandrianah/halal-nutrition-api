<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\MasterCertificateType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class APIMasterCertificateTypeController extends APIController
{
    public function __construct()
    {
        $this->model = MasterCertificateType::class;
        parent::__construct();
    }


}

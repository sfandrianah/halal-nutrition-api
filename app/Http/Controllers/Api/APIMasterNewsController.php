<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\MasterAttachment;
use App\Model\MasterCertificate;
use App\Model\MasterCertificateStatus;
use App\Model\MasterItem;
use App\Model\MasterNews;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class APIMasterNewsController extends APIController
{
    public function __construct()
    {
        $this->model = MasterNews::class;
        parent::__construct();
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $resultData = $this->model;
        if (isset($input['status'])) {
            $resultData = $resultData::where('status', $input['status']);

            $resultData = $resultData->paginate(5);
        } else {
            $resultData = $resultData::paginate(5);
        }
        $custom = collect();
        $data_2 = $custom->merge($resultData);
        $fixData = array();
        for ($no = 0; $no < count($data_2['data']); $no++) {
            $itemId = $data_2['data'][$no]['id'];
            $fromUserId =  $data_2['data'][$no]['user_id'];
            $dataUser = User::where('id', $fromUserId)->get();
            $data_3 = $data_2['data'][$no];
            $dataAttach = MasterAttachment::where('reference_id', $itemId)
                ->where('reference_table','mst_news')
                ->get();
            $fixDatas = array_merge($data_3, array(
                "user" => $dataUser,
                "image" => $dataAttach
            ));
            array_push($fixData, $fixDatas);
        }
        $data_2['data'] = $fixData;
        return response()->json($data_2, 200);
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        $userId = $user['id'];
        $input = $request->all();
        $foodCode = $input['code'];
        $foodName = $input['name'];
        $manufacture = $input['manufacture'];
        $ingredient = $input['ingredient'];
//        $foodCode = rand(100000000000, 9999999999999);
        $insertItem = MasterItem::create(
            array(
                "code" => $foodCode,
                "name" => $foodName,
                "status" => 0,
                "manufacture" => $manufacture,
                "ingredient" => $ingredient,
                "user_id" => $userId
            )
        );
        $result = true;
        $data = array();
        $message = "";
        if (isset($insertItem['id'])) {
            if (is_numeric($insertItem['id'])) {
                $itemId = $insertItem['id'];
                if (isset($input['certificate'])) {
                    $inputCertificate = $input['certificate'];
                    foreach ($inputCertificate as $ic) {
                        if ($result == true) {
                            $certificateCode = $ic['code'];

                            $organizationName = $ic['organization'];
                            $name = $ic['organization'];
                            if (isset($ic['name'])) {
                                $name = $ic['name'];
                            }
                            $certificateStatusId = $ic['certificateStatusId'];
							$certificateTypeId = $ic['certificateTypeId'];
//                            $expiredDate = $ic['expiredDate'];
                            $expiredDate = \DateTime::createFromFormat('d/m/Y', $ic['expiredDate']);
                            $fixExpiredDate = $expiredDate->format('Y-m-d');
                            $insertCertificate = MasterCertificate::create(array(
                                "code" => $certificateCode,
                                "name" => $name,
                                "organization_name" => $organizationName,
                                "certificate_status_id" => $certificateStatusId,
								"certificate_type_id" => $certificateTypeId,
                                "expired_date" => $fixExpiredDate,
                                "item_id" => $itemId,
                            ));
                            if (isset($insertCertificate['id'])) {
                                if (is_numeric($insertCertificate['id'])) {
                                    $data = array_merge($input, array("id" => $itemId));

                                } else {
                                    $result = false;
                                    $message = "Insert Certificate By Item Failed";
                                }
                            } else {
                                $result = false;
                                $message = "Insert Certificate By Item Failed";
                            }
                        }
                    }
                }
            }
        }
        if (empty($data)) {
            $result = false;
            $message = "Insert Item Failed";
        }
        $resultData = array(
            "result" => $result,
            "message" => $message,
            "data" => $data,
        );

        return response()->json($resultData, 200);
    }
}

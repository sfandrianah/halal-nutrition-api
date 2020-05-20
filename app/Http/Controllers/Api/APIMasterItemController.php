<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\MasterAttachment;
use App\Model\MasterCertificate;
use App\Model\MasterCertificateStatus;
use App\Model\MasterItem;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class APIMasterItemController extends APIController
{
    public function __construct()
    {
        $this->model = MasterItem::class;
        parent::__construct();
    }

    public function list(Request $request)
    {
		$mainDir = $GLOBALS['MAIN_DIRECTORY'];
		$user = Auth::user();
        $input = $request->all();
        $resultData = $this->model;
		//echo json_encode($user);
		if(isset($user)){
			//echo "mAU";
			$userId = $user['id'];
			$resultData = $resultData::where('user_id',$userId);
		}	
		$search = "";
		if(isset($input["search"])){
			$search = $input["search"];
		}
		
		$key = "";
		if(isset($input["key"])){
			$key = $input["key"];
		}
		
		$value = "";
		if(isset($input["value"])){
			$value = $input["value"];
		}
        if (isset($input['status'])) {
			
			if(isset($user)){
				if($key == "" || $value == ""){
					$resultData = $resultData->orWhere('name', 'like', '%' . $search . '%');
					$resultData = $resultData->where('status', $input['status']);
				} else {
					$resultData = $resultData->where($key, $value);
				}
			} else {
				if($key == "" || $value == ""){
					$resultData = $resultData::orWhere('name', 'like', '%' . $search . '%');
					$resultData = $resultData->where('status', $input['status']);
				} else {
					$resultData = $resultData::where($key, $value);
				}
			} 
			//echo "masuk";
			
            $resultData = $resultData->paginate(5);
        } else {
			
			if(isset($user)){
				if($key == "" || $value == ""){
					$resultData = $resultData->orWhere('name', 'like', '%' . $search . '%');
					$resultData = $resultData->paginate(5);
				} else {
					$resultData = $resultData->where($key, $value);
					$resultData = $resultData->paginate(5);
				}
			} else {
				if($key == "" || $value == ""){
					$resultData = $resultData::orWhere('name', 'like', '%' . $search . '%');
					$resultData = $resultData->paginate(5);
				} else {
					$resultData = $resultData::where($key, $value);
					$resultData = $resultData->paginate(5);
				}
			}
            
        }
        $custom = collect();
        $data_2 = $custom->merge($resultData);
        $fixData = array();
        for ($no = 0; $no < count($data_2['data']); $no++) {
            $itemId = $data_2['data'][$no]['id'];
            $dataCertificate = MasterCertificate::where('item_id', $itemId)->get();
            $data_3 = $data_2['data'][$no];
            $dataAttach = MasterAttachment::where('reference_id', $itemId)
                ->where('reference_table','mst_item')
                ->get();
			for($no_1=0;$no_1<count($dataAttach);$no_1++){
				$dataPathIMG = $dataAttach[$no_1]["path"];
				$dataFilename = $dataAttach[$no_1]["filename"];
				$fileUrl = $dataAttach[$no_1]["url"];
				//echo $mainDir."\\uploads\\".$dataPathIMG.$dataFilename;
				if(file_exists($mainDir."/uploads/".$dataPathIMG."/".$dataFilename)){
					$fileUrl = URL("/uploads/".$dataPathIMG."/".$dataFilename);
				}
				$dataAttach[$no_1]["url"] = $fileUrl; 
			}
            $fixDatas = array_merge($data_3, array(
                "certificate" => $dataCertificate,
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

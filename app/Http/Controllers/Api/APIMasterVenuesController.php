<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\MasterAttachment;
use App\Model\MasterCertificate;
use App\Model\MasterCertificateStatus;
use App\Model\MasterItem;
use App\Model\MasterScheduleVenues;
use App\Model\MasterVenues;
use App\Model\MasterVotes;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class APIMasterVenuesController extends APIController
{
    public function __construct()
    {
        $this->model = MasterVenues::class;
        parent::__construct();
    }

    public function list(Request $request)
    {
        $mainDir = $GLOBALS['MAIN_DIRECTORY'];
        $user = Auth::user();
        $input = $request->all();
        $resultData = $this->model;
        //echo json_encode($user);
        if (isset($user)) {
            //echo "mAU";
            $userId = $user['id'];
            $resultData = $resultData::where('user_id', $userId);
        }
        $search = "";
        if (isset($input["search"])) {
            $search = $input["search"];
        }

        $key = "";
        if (isset($input["key"])) {
            $key = $input["key"];
        }

        $value = "";
        if (isset($input["value"])) {
            $value = $input["value"];
        }
        if (isset($input['status'])) {

            if (isset($user)) {
                if ($key == "" || $value == "") {
                    $resultData = $resultData->orWhere('name', 'like', '%' . $search . '%');
                    $resultData = $resultData->where('status', $input['status']);
                } else {
                    $resultData = $resultData->where($key, $value);
                }
            } else {
                if ($key == "" || $value == "") {
                    $resultData = $resultData::orWhere('name', 'like', '%' . $search . '%');
                    $resultData = $resultData->where('status', $input['status']);
                } else {
                    $resultData = $resultData::where($key, $value);
                }
            }
            //echo "masuk";

            $resultData = $resultData->paginate(10);
        } else {

            if (isset($user)) {
                if ($key == "" || $value == "") {
                    $resultData = $resultData->orWhere('name', 'like', '%' . $search . '%');
                    $resultData = $resultData->paginate(10);
                } else {
                    $resultData = $resultData->where($key, $value);
                    $resultData = $resultData->paginate(10);
                }
            } else {
                if ($key == "" || $value == "") {
                    $resultData = $resultData::orWhere('name', 'like', '%' . $search . '%');
                    $resultData = $resultData->paginate(10);
                } else {
                    $resultData = $resultData::where($key, $value);
                    $resultData = $resultData->paginate(10);
                }
            }

        }
        $custom = collect();
        $data_2 = $custom->merge($resultData);
        $fixData = array();
        for ($no = 0; $no < count($data_2['data']); $no++) {
            $itemId = $data_2['data'][$no]['id'];
            $restaurantStatusId = $data_2['data'][$no]['restaurant_status_id'];
            $restaurantStatus = "Partially Halal";
            if ($restaurantStatusId == 2) {
                $restaurantStatus = "Completely Halal";
            }
//            $votesCount = MasterVotes::where('reference_id', $itemId)
//                ->where('reference_table', 'mst_venues')
//                ->count();
//            echo $votesCount;
            $votesSumVal = MasterVotes::where('reference_id', $itemId)
                ->where('reference_table', 'mst_venues')
                ->sum("value");
            $resultVotes = $votesSumVal;
            if ($votesSumVal > 0) {
                if ($votesSumVal > 0) {
                    $resultVotes = $votesSumVal / 5;
                }
//
//                echo $calcVotes;
            }
            $data_3 = $data_2['data'][$no];
            $dataAttach = MasterAttachment::where('reference_id', $itemId)
                ->where('reference_table', 'mst_venues')
                ->get();
            for ($no_1 = 0; $no_1 < count($dataAttach); $no_1++) {
                $dataPathIMG = $dataAttach[$no_1]["path"];
                $dataFilename = $dataAttach[$no_1]["filename"];
                $fileUrl = $dataAttach[$no_1]["url"];
                //echo $mainDir."\\uploads\\".$dataPathIMG.$dataFilename;
				if($dataPathIMG != null || $dataFilename != null){
                if (file_exists($mainDir . "/uploads/" . $dataPathIMG . "/" . $dataFilename)) {
                    $fileUrl = URL("/uploads/" . $dataPathIMG . "/" . $dataFilename);
                }
				}
                $dataAttach[$no_1]["url"] = $fileUrl;
            }
            $fixDatas = array_merge($data_3, array(
                "restaurant_status" => $restaurantStatus,
                "votes" => $resultVotes,
                "image" => $dataAttach
            ));
            array_push($fixData, $fixDatas);
        }
        $data_2['data'] = $fixData;
        return response()->json($data_2, 200);
    }

    public function scheduleNow(Request $request, $id)
    {
        $mainDir = $GLOBALS['MAIN_DIRECTORY'];
        $user = Auth::user();
        $input = $request->all();
        $model = $this->model;
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $currentTimeToInt = strtotime($currentTime);
//        $currentTimeToInt = strtotime("17:00:00");
        $currentDayTimeSecond = $currentTimeToInt - strtotime('today');
        $getDataScheduleNow = MasterScheduleVenues::where('venues_id', $id)
            ->where("open_date", $currentDate)
            ->where("until_open_date", $currentDate)
            ->where('status',1)
            ->get();
        $strStatusSchedule = "Close Now";
        $statusSchedule = 0;
        $strOpenSchedule = null;
        if (count($getDataScheduleNow) == 0) {
            $getDataSchedule = MasterScheduleVenues::where('venues_id', $id)
                ->where("open_date", "<=", $currentDate)
                ->where("until_open_date", ">=", $currentDate)
                ->where('status',1)
                ->get();
            if (count($getDataSchedule) > 0) {
                $getDataSchedule_1 = $getDataSchedule[0];
                $openTime = $getDataSchedule_1['open_time'];
                $openUntilTime = $getDataSchedule_1['until_open_time'];
                $openTimeToInt = strtotime($openTime);
                $openUntilTimeToInt = strtotime($openUntilTime);
                $openTimeToSecond = $openTimeToInt - strtotime('today');
                $openUntilTimeToSecond = $openUntilTimeToInt - strtotime('today');
//            if($)
//            echo $currentDayTimeSecond."-".$openTimeToSecond;
                if ($currentDayTimeSecond >= $openTimeToSecond && $currentDayTimeSecond <= $openUntilTimeToSecond) {
                    $strStatusSchedule = "Open Now";
                    $statusSchedule = 1;
                }
                $strOpenSchedule = $openTime . " to " . $openUntilTime;
            }
        } else {
            $getDataScheduleNow_1 = $getDataScheduleNow[0];
            $openTime = $getDataScheduleNow_1['open_time'];
            $openUntilTime = $getDataScheduleNow_1['until_open_time'];
            $openTimeToInt = strtotime($openTime);
            $openUntilTimeToInt = strtotime($openUntilTime);
            $openTimeToSecond = $openTimeToInt - strtotime('today');
            $openUntilTimeToSecond =  $openUntilTimeToInt - strtotime('today');
//            if($)
//            echo $currentDayTimeSecond."-".$openTimeToSecond;
            if($currentDayTimeSecond >= $openTimeToSecond && $currentDayTimeSecond <= $openUntilTimeToSecond){
                $strStatusSchedule = "Open Now";
                $statusSchedule = 1;
            }
            $strOpenSchedule = $openTime." to ".$openUntilTime;
        }

        $resultJsonData = array(
            "str_status_schedule" => $strStatusSchedule,
            "status_schedule" => $statusSchedule,
            "str_open_schedule" => $strOpenSchedule,
        );
        return response()->json($resultJsonData, 200);
    }
	public function getDirections(Request $request)
    {
		//$endpoint = "http://my.domain.com/test.php";
		$input = $request->all();
		$origin = $input["origin"];
		$destination = $input["destination"];
		$key = $input["key"];
		//$endpoint =  "https://maps.googleapis.com/maps/api/directions/json";
		$endpoint =  "https://maps.googleapis.com/maps/api/directions/json?origin=".$origin."&destination=".$destination."&key=".$key."&sensor=true";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		// SSL important
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$output = curl_exec($ch);
		curl_close($ch);

		/*
		$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', $endpoint, ['query' => [
			'origin' => $origin, 
			'destination' => $destination,
			'key' => $key,
			'sensor' => "true",
		]]);

		// url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

		$statusCode = $response->getStatusCode();
		$content = $response->getBody();
		*/
		return $output;
	}
}

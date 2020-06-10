<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\MasterAttachment;
use App\Model\MasterCertificateStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APIAttachmentController extends APIController
{
    public function __construct()
    {
        $this->model = MasterAttachment::class;
        parent::__construct();
    }

    public function upload(Request $request)
    {
        $input = $request->all();
        $user = Auth::user();
        $userId = $user['id'];
        $mainDir = $GLOBALS['MAIN_DIRECTORY'];
        $file = $request->file('files');

        //Display File Name
//        echo 'File Name: ' . $file->getClientOriginalName();
//        echo '<br>';

        //Display File Extension
//        echo 'File Extension: ' . $file->getClientOriginalExtension();
//        echo '<br>';

        //Display File Real Path
//        echo 'File Real Path: ' . $file->getRealPath();
//        echo '<br>';

        //Display File Size
//        echo 'File Size: ' . $file->getSize();
//        echo '<br>';

        //Display File Mime Type
		if ($file->isValid()) {
        $mimeType = $file->getMimeType();
//        echo 'File Mime Type: ' . $file->getMimeType();
        $years = date('Y');
        $month = date('m');
        $day = date('d');
        $type = "none";
        if (strpos($mimeType, 'image') !== false) {
            $type = "image";
        }
        $path = '/attachment/' . $type . '/' . $years . '/' . $month . '/' . $day;
        $destinationPath = $mainDir . '/uploads';
		$explodeFilename = explode("?",$file->getClientOriginalName());
		$fixFilename = $file->getClientOriginalName();
		if(isset($explodeFilename[1])){
			$fixFilename = 	$explodeFilename[0];
		}
        $data = array(
            "mime" => $mimeType,
            "type" => $type,
            "path" => $path,
            "user_id" => $userId,
            "filename" => $fixFilename,
            "status" => 1
        );
        if (isset($input['referenceId'])) {
            $referenceId = $input['referenceId'];
            $data = array_merge($data, array("reference_id" => $referenceId));
        }
		$attachmentId = 0;
		if (isset($input['attachmentId'])) {
            $attachmentId = $input['attachmentId'];
         //   $data = array_merge($data, array("reference_id" => $referenceId));
        }
        if (isset($input['referenceTable'])) {
            $referenceTable = $input['referenceTable'];
            $data = array_merge($data, array("reference_table" => $referenceTable));
        }
		if($attachmentId == 0){
			$insertAttachment = MasterAttachment::create($data);
			$result = true;
			$message = "";
			if (isset($insertAttachment['id'])) {
				if (is_numeric($insertAttachment['id'])) {
					$file->move($destinationPath . $path, $fixFilename);
					$message = "Upload Attachment Success";
				} else {
					$result = false;
					$message = "Upload Attachment Failed";
				}
			} else {
				$result = false;
				$message = "Upload Attachment Failed";
			}
		} else {
			$insertAttachment = MasterAttachment::where("id",$attachmentId)->update($data);
			$result = true;
			$message = "";
			if (isset($insertAttachment)) {
				$file->move($destinationPath . $path, $fixFilename);
				$message = "Upload Attachment Success";
			} else {
				$result = false;
				$message = "Upload Attachment Failed";
			}
		}
        
		} else {
			if (isset($input['referenceTable'])) {
				Log::emergency('REFERENCE TABLE: '.$input['referenceTable']);
			}
			if (isset($input['referenceId'])) {
				Log::emergency('REFERENCE ID: '.$input['referenceId']);
			}
			if (isset($_FILES['files'])) {
				Log::emergency('FILES: '.json_encode($_FILES['files']));
			}
			$result = false;
			$message = "File Not Found";
		}
        $resultData = array(
            "result" => $result,
            "message" => $message,
        );
        return response()->json($resultData, 200);
//        echo json_encode($data);
//        $file->move($destinationPath . $path, $file->getClientOriginalName());
    }

}

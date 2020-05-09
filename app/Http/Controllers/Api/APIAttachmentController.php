<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\MasterAttachment;
use App\Model\MasterCertificateStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        $data = array(
            "mime" => $mimeType,
            "type" => $type,
            "path" => $path,
            "user_id" => $userId,
            "filename" => $file->getClientOriginalName(),
            "status" => 1
        );
        if (isset($input['referenceId'])) {
            $referenceId = $input['referenceId'];
            $data = array_merge($data, array("reference_id" => $referenceId));
        }
        if (isset($input['referenceTable'])) {
            $referenceTable = $input['referenceTable'];
            $data = array_merge($data, array("reference_table" => $referenceTable));
        }
        $insertAttachment = MasterAttachment::create($data);
        $result = true;
        $message = "";
        if (isset($insertAttachment['id'])) {
            if (is_numeric($insertAttachment['id'])) {
                $file->move($destinationPath . $path, $file->getClientOriginalName());
                $message = "Upload Attachment Success";
            } else {
                $result = false;
                $message = "Upload Attachment Failed";
            }
        } else {
            $result = false;
            $message = "Upload Attachment Failed";
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

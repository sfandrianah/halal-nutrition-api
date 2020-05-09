<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller
{
    //
    public $successStatus = 200;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
			'address' => 'required',
            // 'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('AppName')->accessToken;
        return response()->json($success, $this->successStatus);
    }
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('AppName')-> accessToken;
            return response()->json($success, $this-> successStatus);
        } else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function getUser() {
        $user = Auth::user();
        return response()->json($user->only(['id','name','email','address']), $this->successStatus);
    }
	
	public function update(Request $request) {
        $user = Auth::user();
		$userId = $user['id'];
		//echo $userId;
		$input = $request->all();
		$validator = Validator::make($request->all(), [
            'name' => 'required',
			'address' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
		$name = $input['name'];
		$address = $input['address'];
		$userUpdate = User::where("id",$userId)->update(array("name"=>$name,"address"=>$address));
		if($userUpdate){
			return response()->json(["result"=>true], $this->successStatus);
		} else {
			return response()->json(["result"=>false], $this->successStatus);
		}
    }
	
	public function updatePassword(Request $request) {
        $user = Auth::user();
		$userId = $user['id'];
		//echo $userId;
		$input = $request->all();
		$validator = Validator::make($request->all(), [
            'oldpassword' => 'required',
			'newpassword' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
		$oldpassword = $input['oldpassword'];
		$newpassword = $input['newpassword'];
		
		if(password_verify($oldpassword,$user['password'])){			
			$userUpdate = User::where("id",$userId)->update(array("password"=>bcrypt($newpassword)));
			if($userUpdate){
				return response()->json(["result"=>true], $this->successStatus);
			} else {
				return response()->json(["result"=>false,"message"=>"Ubah Password Gagal"], $this->successStatus);
			}
		} else {
			return response()->json(['result' => false,"message"=>"Password Lama Tidak Sama"], 200);
		}
		
		
    }
}

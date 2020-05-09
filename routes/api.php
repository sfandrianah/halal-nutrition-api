<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('login', 'Api\AuthController@login');
Route::post('register', 'Api\AuthController@register');
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('user', 'Api\AuthController@getUser');
    Route::post('getUser', 'Api\AuthController@getUser');
	Route::post('user/update/password', 'Api\AuthController@updatePassword');
	Route::post('user/update', 'Api\AuthController@update');
    Route::post('item', 'Api\APIMasterItemController@insert');
    Route::get('item', 'Api\APIMasterItemController@list');
    Route::post('attachment/upload', 'Api\APIAttachmentController@upload');
});
Route::get('certificate-status', 'Api\APIMasterCertificateStatusController@list');

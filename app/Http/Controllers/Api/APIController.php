<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class APIController extends Controller
{

    protected $table = null;
    protected $model = null;

    public function __construct()
    {
        if ($this->model != null) {
            $model = $this->model;
            $modelClass = get_class_vars($model);
            if (isset($modelClass->table)) {
                $this->table = $modelClass->table;
            }
        }
    }

    public function list(Request $request)
    {
        $data = array();
        if ($this->model != null) {
            $model = $this->model;
            $data = $model::where('status', 1)->get();
        }

        return response()->json($data, 200);
    }

}

<?php

namespace App\Http\Controllers\Api\Transaction\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class LogoutController extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "user_id" => "required|numeric"
        ]);

        $attributeNames = [
            "user_id" => "ID User"
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function process(Request $request)
	{
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }
        
        std_update([
            "table_name" => "MA_USRACC",
            "where" => ["MA_USRACC_ID" => $request->user_id],
            "data" => [
                "MA_USRACC_JWT_TOKEN" => null
            ]
        ]);

        return response()->json([
            "status" => "OK",
            "data" => "User Successfully Logged Out"
        ],200);
	}
}
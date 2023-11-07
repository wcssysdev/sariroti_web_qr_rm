<?php

namespace App\Http\Controllers\MasterData\Users;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddController extends Controller
{
    public function index()
    {
        return view('master_data/users/add');
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MA_USRACC_FULL_NAME" => "required",
            "MA_USRACC_EMAIL" => "required",
            "MA_USRACC_PLANT_CODE" => "required",
            "MA_USRACC_ROLE" => "required|in:1,2,3,4,5,6",
            "MA_USRACC_IS_ACTIVE" => "required|numeric",
            "MA_USRACC_LOGIN_VIA_SSO" => "required|in:1,0",
            "MA_USRACC_PASSWORD" => "max:50"
        ]);

        $attributeNames = [
            "MA_USRACC_FULL_NAME" => "Name",
            "MA_USRACC_EMAIL" => "Email",
            "MA_USRACC_PLANT_CODE" => "Plant Code",
            "MA_USRACC_ROLE" => "Role",
            "MA_USRACC_IS_ACTIVE" => "Is Active?",
            "MA_USRACC_LOGIN_VIA_SSO" => "Login Via SSO?",
            "MA_USRACC_PASSWORD" => "Password"
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function save(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }
        
        if ($request->MA_USRACC_LOGIN_VIA_SSO == false) {
            $password = Hash::make($request->MA_USRACC_PASSWORD);
        }
        else{
            $password = NULL;
        }
        
        $insert_res = std_insert([
            "table_name" => "MA_USRACC",
            "data" => [
                "MA_USRACC_FULL_NAME" => $request->MA_USRACC_FULL_NAME,
                "MA_USRACC_EMAIL" => $request->MA_USRACC_EMAIL,
                "MA_USRACC_PLANT_CODE" => $request->MA_USRACC_PLANT_CODE,
                "MA_USRACC_ROLE" => $request->MA_USRACC_ROLE,
                "MA_USRACC_JWT_TOKEN" => NULL,
                "MA_USRACC_FCM_TOKEN" => NULL,
                "MA_USRACC_IS_ACTIVE" => $request->MA_USRACC_IS_ACTIVE,
                "MA_USRACC_LOGIN_VIA_SSO" => $request->MA_USRACC_LOGIN_VIA_SSO,
                "MA_USRACC_LAST_LOGIN_TIMESTAMP" => NULL,
                "MA_USRACC_CRTD_BY" => session("id"),
                "MA_USRACC_CRTD_BY_TIMESTAMP" => date("Y-m-d H:i:s"),
                "MA_USRACC_PASSWORD" => $password
            ]
        ]);

        if ($insert_res !== true) {
            return response()->json([
                'message' => "Error on saving data, please try again later"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}

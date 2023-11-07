<?php

namespace App\Http\Controllers\MasterData\Users;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EditController extends Controller
{
    public function index(Request $request)
    {
        if ($request->MA_USRACC_ID != NULL) {
            $user_data = std_get([
                "select" => ["MA_USRACC_ID", "MA_USRACC_FULL_NAME","MA_USRACC_EMAIL", "MA_USRACC_PLANT_CODE","MA_USRACC_ROLE", "MA_USRACC_JWT_TOKEN", "MA_USRACC_FCM_TOKEN","MA_USRACC_IS_ACTIVE","MA_USRACC_LAST_LOGIN_TIMESTAMP","MA_USRACC_LOGIN_VIA_SSO"],
                "table_name" => "MA_USRACC",
                "where" => [
                    [
                        "field_name" => "MA_USRACC_ID",
                        "operator" => "=",
                        "value" => $request->MA_USRACC_ID
                    ]
                ],
                "first_row" => true,
            ]);
            if ($user_data == NULL) {
                abort(404);
            }
            return view('master_data/users/edit', ['user_data' => $user_data]);
        }
        else{
            abort(404);
        }
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

    public function update(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $update_data = [
            "MA_USRACC_FULL_NAME" => $request->MA_USRACC_FULL_NAME,
            "MA_USRACC_EMAIL" => $request->MA_USRACC_EMAIL,
            "MA_USRACC_PLANT_CODE" => $request->MA_USRACC_PLANT_CODE,
            "MA_USRACC_ROLE" => $request->MA_USRACC_ROLE,
            "MA_USRACC_IS_ACTIVE" => $request->MA_USRACC_IS_ACTIVE,
            "MA_USRACC_LAST_LOGIN_TIMESTAMP" =>date("Y-m-d H:i:s"),
            "MA_USRACC_UPDT_BY" => session("id"),
            "MA_USRACC_UPDT_BY_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        if ($request->MA_USRACC_PASSWORD != null && $request->MA_USRACC_PASSWORD != "") {
            $password = Hash::make($request->MA_USRACC_PASSWORD);
            $update_data = array_merge($update_data, [
                "MA_USRACC_PASSWORD" => $password
            ]);
        }

        $update_res = std_update([
            "table_name" => "MA_USRACC",
            "where" => ["MA_USRACC_ID" => $request->MA_USRACC_ID],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Error on update data, please try again later"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}

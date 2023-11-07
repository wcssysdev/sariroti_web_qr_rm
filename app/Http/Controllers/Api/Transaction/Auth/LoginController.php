<?php

namespace App\Http\Controllers\Api\Transaction\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class LoginController extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "email" => "required|max:255|email",
            "password" => "required|max:50"
        ]);

        $attributeNames = [
            "email" => "Email",
            "password" => "Password"
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

        $user_data = std_get([
            "select" => ["*"],
            "table_name" => "MA_USRACC",
            "where" => [
                [
                    "field_name" => "MA_USRACC_EMAIL",
                    "operator" => "=",
                    "value" => $request->email,
                ]
            ],
            "first_row" => true
        ]);
        
        if ($user_data == NULL) {
            return response()->json([
                'message' => "Invalid Credentials"
            ],400);
        }
        if ($user_data["MA_USRACC_IS_ACTIVE"] == false) {
            return response()->json([
                'message' => "User is Inactive"
            ],400);
        }
        if ($user_data["MA_USRACC_ROLE"] != 3) {
            return response()->json([
                'message' => "You are not allowed to login via Mobile"
            ],400);
        }

        if ($user_data["MA_USRACC_LOGIN_VIA_SSO"] == true) {
            $login_res = ldap_login($request->email, $request->password);
            if (is_array($login_res)) {
                $words = explode(" ", $user_data["MA_USRACC_FULL_NAME"]);
                $acronym = "";
                foreach ($words as $w) {
                    $acronym .= $w[0];
                }

                $payload = [
                    "user_id" => $user_data["MA_USRACC_ID"],
                    "name" => $user_data["MA_USRACC_FULL_NAME"],
                    "user_role"	=> $user_data["MA_USRACC_ROLE"],
                    "user_initial_name" => $acronym,
                    "plant" => $user_data["MA_USRACC_PLANT_CODE"],
                    "app_name" => "mobile_barcode_scanner_sari_roti"
                ];

                $jwt = JWT::encode($payload, config('secret.php_jwt_key'));

                std_update([
                    "table_name" => "MA_USRACC",
                    "where" => ["MA_USRACC_ID" => $user_data["MA_USRACC_ID"]],
                    "data" => [
                        "MA_USRACC_JWT_TOKEN" => $jwt
                    ]
                ]);

                $payload["token"] = $jwt;

                return response()->json([
                    "status" => "OK",
                    "data" => $payload
                ],200);
            }
            elseif ($login_res == 500) {
                return response()->json([
                    "status" => "INTERNAL_SERVER_ERROR",
                    "message" => "LDAP Error"
                ],400);
            }
            else{
                return response()->json([
                    "status" => "BAD_REQUEST",
                    "message" => "Invalid Credentials"
                ],400);
            }
        }
        else{
            if (!Hash::check($request->password, $user_data["MA_USRACC_PASSWORD"])) {
                return response()->json([
                    'message' => "Invalid Credentials"
                ],400);
            }

            $words = explode(" ", $user_data["MA_USRACC_FULL_NAME"]);
            $acronym = "";
            foreach ($words as $w) {
                $acronym .= $w[0];
            }

            $payload = [
                "user_id" => $user_data["MA_USRACC_ID"],
                "name" => $user_data["MA_USRACC_FULL_NAME"],
                "user_role"	=> $user_data["MA_USRACC_ROLE"],
                "user_initial_name" => $acronym,
                "plant" => $user_data["MA_USRACC_PLANT_CODE"],
                "app_name" => "mobile_barcode_scanner_sari_roti"
            ];

            $jwt = JWT::encode($payload, config('secret.php_jwt_key'));

            std_update([
                "table_name" => "MA_USRACC",
                "where" => ["MA_USRACC_ID" => $user_data["MA_USRACC_ID"]],
                "data" => [
                    "MA_USRACC_JWT_TOKEN" => $jwt
                ]
            ]);

            $payload["token"] = $jwt;

            return response()->json([
                "status" => "OK",
                "data" => $payload
            ], 200);
        }
	}
}
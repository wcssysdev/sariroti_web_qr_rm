<?php

namespace App\Http\Controllers\MasterData\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $user_data = std_get([
            "select" => ["MA_USRACC_ID", "MA_USRACC_FULL_NAME","MA_USRACC_EMAIL", "MA_USRACC_PLANT_CODE","MA_USRACC_ROLE", "MA_USRACC_JWT_TOKEN", "MA_USRACC_FCM_TOKEN","MA_USRACC_IS_ACTIVE","MA_USRACC_LAST_LOGIN_TIMESTAMP","MA_USRACC_LOGIN_VIA_SSO"],
            "table_name" => "MA_USRACC",
            "order_by" => [
                [
                    "field" => "MA_USRACC_FULL_NAME",
                    "type" => "ASC",
                ]
            ],
            "multiple_rows" => true,
        ]);
        return view('master_data/users/view', ['user_data' => $user_data]);
    }

    // public function detail(Request $request)
    // {
    //     if ($request->user_code != NULL) {
    //         $user_data = std_get([
    //             "select" => ["user_code", "user_name","user_email", "user_phone_number","user_profile_picture", "user_biography", "user_role", "user_is_active","user_last_login", "user_last_login_ip_address", "user_created_by", "user_created_by_name", "user_changed_by", "user_changed_by_name", "user_created_time", "user_changed_time"],
    //             "table_name" => "m_users",
    //             "where" => [
    //                 [
    //                     "field_name" => "user_code",
    //                     "operator" => "=",
    //                     "value" => $request->user_code
    //                 ]
    //             ],
    //             "first_row" => true,
    //         ]);
    //         return view('master_data/users/detail', ['user_data' => $user_data]);
    //     }
    // }
}

<?php

namespace App\Http\Controllers\Authentication;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;

class ForgotPasswordController extends Controller
{
    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "email" => "required|max:255",
        ]);

        $attributeNames = [
            "email" => "Alamat Email",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function send_email(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $user_data = std_get([
            "select" => ["backend_user_name","backend_user_email","backend_user_is_active"],
            "table_name" => "m_backend_users",
            "where" => [
                [
                    "field_name" => "backend_user_email",
                    "operator" => "=",
                    "value" => $request->email,
                ]
            ],
            "first_row" => true
        ]);

        if (!isset($user_data["backend_user_email"])) {
            return response()->json([
                'message' => "Alamat email belum terdaftar"
            ],500);
        }

        if ($user_data["backend_user_is_active"] != 1) {
            return response()->json([
                'message' => "Alamat email sudah tidak aktif!"
            ],500);
        }

        $activation_code = md5(uniqid(rand(), true));
        $update_res = std_update([
            "table_name" => "m_backend_users",
            "where" => ["backend_user_email" => $request->email],
            "data" => ["backend_user_forgot_password_code" => $activation_code],
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam melakukan proses login, silahkan coba berberapa saat lagi"
            ],500);
        }

        // BEGIN::SEND EMAIL FOR FORGOT PASSWORD
            //LARAVEL send mail sample
            $to_name = $user_data["backend_user_name"];
            $to_email = $user_data["backend_user_email"];
            $data = array(
                "name"=> $user_data["backend_user_name"],
                // "body" => "A test mail"
                "forgot_password_code" => $activation_code
            );
            
            try {
                Mail::send("authentication.sendmail", $data, function($message) use ($to_name, $to_email) {
                    $message
                    ->to($to_email, $to_name)
                    ->subject("Ubah Kata Sandi Revolusi Mental");
                    $message->from("testmailjoes@gmail.com", "Ubah Kata Sandi - Revolusi Mental");
                });
            } catch (\Exception $e) {
                Log::critical("Error when send email via sendinblue");
            }
        // END::SEND EMAIL FOR FORGOT PASSWORD

        return response()->json([
            'message' => "OK"
        ],200);
    }

    // FORGOT PASSWORD WEB VIEW
    public function validate_input_save($request)
    {
        $validate = Validator::make($request->all(),[
            "password_new" => "bail|required",
            "password_new_confirmation" => "bail|required|same:password_new",
        ]);

        $attributeNames = [
            "password_new" => "Kata sandi baru",
            "password_new_confirmation" => "Konfirmasi kata sandi baru"
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function forgot_password_view(Request $request)
    {
        $user_data = std_get([
            "select" => ["backend_user_forgot_password_code"],
            "table_name" => "m_backend_users",
            "where" => [
                [
                    "field_name" => "backend_user_forgot_password_code",
                    "operator" => "=",
                    "value" => $request->forgot_password_code,
                ]
            ],
            "first_row" => true
        ]);
        if ($user_data != NULL) {
            return view("authentication.forgotPasswordView",[
                "forgot_password_code" => $request->forgot_password_code
            ]);
        }
        abort(404);
    }

    public function forgot_password_save(Request $request)
    {
        $validation_res = $this->validate_input_save($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $user_data = std_get([
            "select" => ["backend_user_forgot_password_code"],
            "table_name" => "m_backend_users",
            "where" => [
                [
                    "field_name" => "backend_user_forgot_password_code",
                    "operator" => "=",
                    "value" => $request->forgot_password_code,
                ]
            ],
            "first_row" => true
        ]);

        if ($user_data == null) {
            return response()->json([
                'message' => "Permintaan mengubah kata sandi sudah kadaluarsa, Lakukan proses lupa kata sandi kembali."
            ],500);
        }

        $update_res = std_update([
            "table_name" => "m_backend_users",
            "where" => ["backend_user_forgot_password_code" => $request->forgot_password_code],
            "data" => [
                "backend_user_forgot_password_code" => null,
                "backend_user_password" => Hash::make($request->password_new_confirmation, [
                    'rounds' => 12,
                ]),
                "backend_user_changed_time" => date("Y-m-d H:i:s"),
            ],
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "Terjadi kesalahan dalam melakukan proses login, silahkan coba berberapa saat lagi"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
<?php

namespace App\Http\Controllers\MasterData\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        return view('master_data.users.upload',[
            'user_data' => session('uploaded_user_data')
        ]);
    }

    public function upload(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'file' => 'required|file|mimes:xlsx'
        ])->setAttributeNames([
            'file' => 'File'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'message' => $validate->errors()->all()
            ],400);
        }

        $file = $request->file('file');
        $filename = time().'.'.$file->getClientOriginalExtension();
        $path = 'upload_temp/';

        $file->move($path, $filename);

        $fileInput = $path.$filename;
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(["User Data"]);
        $spreadsheet = $reader->load($fileInput);
        $worksheet = $spreadsheet->getActiveSheet();
        $columns = [
            'A'=>"MA_USRACC_FULL_NAME",
            'B'=>"MA_USRACC_EMAIL",
            'C'=>"MA_USRACC_PLANT_CODE",
            'D'=>"MA_USRACC_ROLE",
            'E'=>"MA_USRACC_IS_ACTIVE",
            'F'=>"MA_USRACC_LOGIN_VIA_SSO",
            'F'=>"MA_USRACC_PASSWORD"
        ];
        $data = [];
        $max = $worksheet->getHighestRow();
        for($i=2;$i<=$max;$i++)
        {
            $data_row = [];
            foreach($columns as $col=>$field)
            {
                $value = $worksheet->getCell($col.$i)->getValue();
                $data_row[$field] = $value;
            }
            $data[] = $data_row;
        }

        session(['uploaded_user_data' => $data]);

        File::delete($fileInput);

        return response()->json(200);
    }

    public function save(Request $request)
    {
        if(!session('uploaded_user_data'))
        {
            return response()->json([
                'message' => 'File is not uploaded'
            ],400);
        }

        foreach(session('uploaded_user_data') as $data)
        {
            if ($data["MA_USRACC_LOGIN_VIA_SSO"] == 0) {
                $password = Hash::make($data["MA_USRACC_PASSWORD"]);
            }
            else{
                $password = NULL;
            }

            std_insert([
                "table_name" => "MA_USRACC",
                "data" => [
                    "MA_USRACC_FULL_NAME" => $data['MA_USRACC_FULL_NAME'],
                    "MA_USRACC_EMAIL" => $data['MA_USRACC_EMAIL'],
                    "MA_USRACC_PLANT_CODE" => $data['MA_USRACC_PLANT_CODE'],
                    "MA_USRACC_ROLE" => $data['MA_USRACC_ROLE'],
                    "MA_USRACC_JWT_TOKEN" => NULL,
                    "MA_USRACC_FCM_TOKEN" => NULL,
                    "MA_USRACC_IS_ACTIVE" => $data['MA_USRACC_IS_ACTIVE'],
                    "MA_USRACC_LOGIN_VIA_SSO" => $data["MA_USRACC_LOGIN_VIA_SSO"],
                    "MA_USRACC_LAST_LOGIN_TIMESTAMP" =>date("Y-m-d H:i:s"),
                    "MA_USRACC_CRTD_BY" => session("id"),
                    "MA_USRACC_CRTD_BY_TIMESTAMP" => date("Y-m-d H:i:s"),
                    "MA_USRACC_PASSWORD" => $password
                ]
            ]);
        }

        Session::forget('uploaded_user_data');

        return response()->json(200);
    }

    public function clear()
    {
        Session::forget('uploaded_user_data');
        return redirect(route('master_data_users_upload_view'));
    }
}

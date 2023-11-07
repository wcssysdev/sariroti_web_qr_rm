<?php

namespace App\Http\Controllers\MasterData\Plant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $data = get_master_data("MA_PLANT");
        return view('master_data/plant/view', [
            "data" => $data
        ]);
    }

    public function master_data_request_sap(Request $request)
    {
        $response = export_request_master_data_csv("plant","PLANT");
        if ($response["code"] == 200) {
            return response()->json([
                "code" => 200,
                "message" => "successfully request master data "
            ],200);
        }else {
            return response()->json([
                "code" => 500,
                "message" => "successfully save master data "
            ],500);
        }
    }

    public function master_data_sync_sap(Request $request)
    {
        $response = sync_master_data("PLANT");
        if ($response["code"] == 200) {
            return response()->json([
                "code" => 200,
                "message" => "successfully sync master data "
            ],200);
        }else {
            if ($response["code"] == 404) {
                return response()->json([
                    "code" => 500,
                    "message" => "No new master data found "
                ],500);
            }
            return response()->json([
                "code" => 500,
                "message" => "There's error when replace the  "
            ],500);
        }
    }
}

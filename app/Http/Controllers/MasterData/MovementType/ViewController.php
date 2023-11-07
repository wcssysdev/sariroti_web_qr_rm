<?php

namespace App\Http\Controllers\MasterData\MovementType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $data = get_master_data("MA_MVT", NULL, NULL, NULL, NULL, NULL, [
            [
                "field" => "MA_MVT_ID",
                "type" => "ASC"
            ]
        ]);

        $last_code = NULL;
        $filtered_data = [];
        for ($i=0; $i < count($data); $i++) {
            if ($last_code == NULL) {
                $filtered_data = array_merge($filtered_data, [$data[$i]]);
                $last_code = $data[$i]["MA_MVT_CODE"];
            }
            else{
                if ($last_code != $data[$i]["MA_MVT_CODE"]) {
                    $filtered_data = array_merge($filtered_data, [$data[$i]]);
                    $last_code = $data[$i]["MA_MVT_CODE"];
                }
            }
        }

        return view('master_data/movement_type/view', [
            "data" => $filtered_data
        ]);
    }

    public function master_data_request_sap(Request $request)
    {
        $response = export_request_master_data_csv("movement_type","MVT");
        if ($response["code"] == 200) {
            return response()->json([
                "code" => 200,
                "message" => "successfully request master data "
            ],200);
        }else {
            return response()->json([
                "code" => 500,
                "message" => "There's error when request master data to sap "
            ],500);
        }
    }

    public function master_data_sync_sap(Request $request)
    {
        $response = sync_master_data("MVT");
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
                "message" => "There's error when replace the master data "
            ],500);
        }
    }
}

<?php

namespace App\Http\Controllers\MasterData\Material;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $data = std_get([
            "select" => ["MA_MATL_CODE","MA_MATL_DESC","MA_MATL_TYPE","MA_MATL_GROUP","MA_MATL_PLANT","MA_MATL_UOM"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => session("plant"),
                ]
            ],
            "order_by" => [
                [
                    "field" => "MA_MATL_CODE",
                    "type" => "ASC",
                ]
            ],
            "distinct" => true,
            "first_row" => false
        ]);
        
        return view('master_data/material/view', [
            "data" => $data
        ]);
    }

    public function master_data_request_sap(Request $request)
    {
        $response = export_request_master_data_csv("material","MAT");
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
        $response = sync_master_data("MAT");
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

<?php

namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoodMovementController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        if ($request->plant_code != NULL && $request->start_date && $request->end_date) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }

            $data = std_get([
                "select" => ["LG_MATERIAL_CODE","LG_MATERIAL_MVT_TYPE","LG_MATERIAL_UOM","LG_MATERIAL_QTY","TR_GR_DETAIL.*","LG_MATERIAL_CREATED_TIMESTAMP","TR_GR_HEADER.TR_GR_HEADER_SAP_DOC"],
                "table_name" => "LG_MATERIAL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_HEADER",
                        "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                    ]
                ],
                "where" => [
                    [
                        "field_name" => "LG_MATERIAL_PLANT_CODE",
                        "operator" => "=",
                        "value" => $request->plant_code
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => ">=",
                        "value" => convert_to_y_m_d($request->start_date)
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => "<=",
                        "value" => convert_to_y_m_d($request->end_date)
                    ]
                ],
                "order_by" => [
                    [
                        "field" => "LG_MATERIAL_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "TR_GR_DETAIL_EXP_DATE",
                        "type" => "ASC",
                    ],
                ]
            ]);
        }
        
        if (session("user_role") == 6) {
            $plant = std_get([
                "select" => ["MA_PLANT_CODE","MA_PLANT_NAME"],
                "table_name" => "MA_PLANT",
                "order_by" => [
                    [
                        "field" => "MA_PLANT_CODE",
                        "type" => "ASC",
                    ]
                ],
                "first_row" => false
            ]);
        }
        else{
            $plant = std_get([
                "select" => ["MA_PLANT_CODE","MA_PLANT_NAME"],
                "table_name" => "MA_PLANT",
                "where" => [
                    [
                        "field_name" => "MA_PLANT_CODE",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ],
                "order_by" => [
                    [
                        "field" => "MA_PLANT_CODE",
                        "type" => "ASC",
                    ]
                ],
                "first_row" => false
            ]);
        }

        return view('report/good_movement', [
            "data" => $data,
            "start" => $request->start_date,
            "end" => $request->end_date,
            "plant_selected" => $request->plant_code,
            "plant" => $plant
        ]);
    }
}

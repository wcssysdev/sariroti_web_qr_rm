<?php

namespace App\Http\Controllers\StockOpname;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index(Request $request)
    {
        $conditions = [
            [
                "field_name" => "TR_PID_HEADER_STATUS",
                "operator" => "!=",
                "value" => "D"
            ],
            [
                "field_name" => "TR_PID_HEADER_STATUS",
                "operator" => "!=",
                "value" => "E"
            ],
            [
                "field_name" => "TR_PID_HEADER_PLANT",
                "operator" => "=",
                "value" => session("plant")
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-")."01";
        }
        else{
            $request->start_date = convert_to_y_m_d($request->start_date);
        }
        if (isset($request->start_date) && $request->start_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PID_HEADER_SAP_CREATED_DATE",
                    "operator" => ">=",
                    "value" => $request->start_date
                ]
            ]);
        }

        if (!isset($request->end_date) || $request->end_date == "") {
            $request->end_date = date("Y-m-d");
        }
        else{
            $request->end_date = convert_to_y_m_d($request->end_date);
        }
        if (isset($request->end_date) && $request->end_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PID_HEADER_SAP_CREATED_DATE",
                    "operator" => "<=",
                    "value" => $request->end_date
                ]
            ]);
        }

        if (isset($request->plant_code) && $request->plant_code != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PID_HEADER_PLANT",
                    "operator" => "=",
                    "value" => $request->plant_code
                ]
            ]);
        }

        $plant_data = get_master_data("MA_PLANT");

        $data = std_get([
            "select" => "TR_PID_HEADER.*",
            "table_name" => "TR_PID_HEADER",
            "where" => $conditions
        ]);

        return view('transaction/stock_opname/view', [
            "data" => $data,
            "plant" => $plant_data,
            "start" => $request->start_date,
            "end" => $request->end_date,
            "plant_selected" => $request->plant_code
        ]);
    }
}

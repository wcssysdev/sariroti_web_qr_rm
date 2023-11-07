<?php

namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class StockController extends Controller
{
    public function get_opening_balance($plant_code, $start_date)
    {
        $data = std_get([
            "select" => ["LG_MATERIAL_CODE","TR_GR_DETAIL_MATERIAL_NAME","LG_MATERIAL_UOM",DB::raw('SUM("LG_MATERIAL_QTY") as ACTUAL_QTY')],
            "table_name" => "LG_MATERIAL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "LG_MATERIAL_PLANT_CODE",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => "<",
                    "value" => $start_date
                ]
            ],
            "order_by" => [
                [
                    "field" => "LG_MATERIAL_CODE",
                    "type" => "ASC",
                ],
            ],
            "group_by" => ["LG_MATERIAL_CODE","TR_GR_DETAIL_MATERIAL_NAME","LG_MATERIAL_UOM"]
        ]);
        return $data;
    }

    public function get_receipt_balance($plant_code, $start_date)
    {
        $data = std_get([
            "select" => ["LG_MATERIAL_CODE","TR_GR_DETAIL_MATERIAL_NAME","LG_MATERIAL_UOM",DB::raw('SUM("LG_MATERIAL_QTY") as Qty')],
            "table_name" => "LG_MATERIAL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "LG_MATERIAL_PLANT_CODE",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => "=",
                    "value" => $start_date
                ],
                [
                    "field_name" => "LG_MATERIAL_QTY",
                    "operator" => ">=",
                    "value" => 0
                ]
            ],
            "order_by" => [
                [
                    "field" => "LG_MATERIAL_CODE",
                    "type" => "ASC",
                ],
            ],
            "group_by" => ["LG_MATERIAL_CODE","TR_GR_DETAIL_MATERIAL_NAME","LG_MATERIAL_UOM"]
        ]);
        return $data;
    }

    public function get_issued_balance($plant_code, $start_date)
    {
        $data = std_get([
            "select" => ["LG_MATERIAL_CODE","TR_GR_DETAIL_MATERIAL_NAME","LG_MATERIAL_UOM",DB::raw('SUM("LG_MATERIAL_QTY") as Qty')],
            "table_name" => "LG_MATERIAL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "LG_MATERIAL_PLANT_CODE",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => "=",
                    "value" => $start_date
                ],
                [
                    "field_name" => "LG_MATERIAL_QTY",
                    "operator" => "<",
                    "value" => 0
                ]
            ],
            "order_by" => [
                [
                    "field" => "LG_MATERIAL_CODE",
                    "type" => "ASC",
                ],
            ],
            "group_by" => ["LG_MATERIAL_CODE","TR_GR_DETAIL_MATERIAL_NAME","LG_MATERIAL_UOM"]
        ]);
        return $data;
    }

    public function get_gr_detail($plant_code, $start_date)
    {
        $data = std_get([
            "select" => ["TR_GR_DETAIL_MATERIAL_CODE","TR_GR_DETAIL_SAP_BATCH","TR_GR_DETAIL_LEFT_QTY","TR_GR_DETAIL_BASE_UOM","TR_GR_DETAIL_EXP_DATE"],
            "table_name" => "TR_GR_DETAIL",
            // "join" => [
            //     [
            //         "join_type" => "inner",
            //         "table_name" => "MA_MATL",
            //         "on1" => "MA_MATL.MA_MATL_CODE",
            //         "operator" => "=",
            //         "on2" => "LG_MATERIAL.LG_MATERIAL_CODE",
            //     ]
            // ],
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_UNLOADING_PLANT",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "TR_GR_DETAIL_LEFT_QTY",
                    "operator" => ">",
                    "value" => 0
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "type" => "ASC",
                ],
                [
                    "field" => "TR_GR_DETAIL_EXP_DATE",
                    "type" => "ASC",
                ]
            ]
        ]);
        return $data;
    }

    public function index(Request $request)
    {
        $open_balance = [];
        $receipt_balance = [];
        $issued_balance = [];
        $gr_detail = [];

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

        if ($request->plant_code != NULL && $request->date != NULL) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }
            $open_balance = $this->get_opening_balance($request->plant_code, convert_to_y_m_d($request->date));
            $receipt_balance = $this->get_receipt_balance($request->plant_code, convert_to_y_m_d($request->date));
            $issued_balance = $this->get_issued_balance($request->plant_code, convert_to_y_m_d($request->date));
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($request->date));
            
            for ($i=0; $i < count($receipt_balance); $i++) { 
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                }
                else{
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $receipt_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"],
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i=0; $i < count($issued_balance); $i++) { 
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                }
                else{
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $issued_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"],
                        "LG_MATERIAL_UOM" => $issued_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "issued_qty" => $issued_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i=0; $i < count($open_balance); $i++) { 
                if (!isset($open_balance[$i]["actual_qty"])) {
                    $open_balance[$i]["actual_qty"] = "0";
                }
                if (!isset($open_balance[$i]["issued_qty"])) {
                    $open_balance[$i]["issued_qty"] = "0";
                }
                if (!isset($open_balance[$i]["receipt_qty"])) {
                    $open_balance[$i]["receipt_qty"] = "0";
                }
                $open_balance[$i]["closing_qty"] = $open_balance[$i]["actual_qty"] + $open_balance[$i]["receipt_qty"] - abs($open_balance[$i]["issued_qty"]);
                $open_balance[$i]["gr_detail"] = [];
                for ($j=0; $j < count($gr_detail); $j++) { 
                    if ($gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"] == $open_balance[$i]["LG_MATERIAL_CODE"]) {
                        $open_balance[$i]["gr_detail"][] = $gr_detail[$j];
                    }
                }
            }
        }
        else{
            $request->date = date("d-m-Y");
        }
        
        return view('report/stock', [
            "open_balance" => $open_balance,
            "start" => $request->start_date,
            "end" => $request->end_date,
            "plant" => $plant,
            "plant_selected" => $request->plant_code,
            "date" => $request->date
        ]);
    }
}

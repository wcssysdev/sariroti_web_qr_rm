<?php

namespace App\Http\Controllers\MasterData\Material;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        $gr_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_GR_DETAIL_EXP_DATE",
                    "type" => "ASC",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "TR_GR_DETAIL.TR_GR_DETAIL_LEFT_QTY",
                    "operator" => ">",
                    "value" => 0,
                ],
                [
                    "field_name" => "TR_GR_HEADER.TR_GR_HEADER_IS_CANCELLED",
                    "operator" => "=",
                    "value" => false,
                ],
                [
                    "field_name" => "TR_GR_DETAIL_UNLOADING_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ]
            ],
            "multiple_rows" => true
        ]);

        $gi_data = std_get([
            "select" => ["TR_GI_SAPHEADER.*","TR_GI_SAPDETAIL.*","TR_GR_DETAIL.TR_GR_DETAIL_EXP_DATE"],
            "table_name" => "TR_GI_SAPHEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GI_SAPDETAIL",
                    "on1" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_SAPHEADER_ID",
                    "operator" => "=",
                    "on2" => "TR_GI_SAPHEADER.TR_GI_SAPHEADER_ID",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_GI_SAPHEADER_CREATED_TIMESTAMP",
                    "type" => "DESC",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_GI_SAPDETAIL_MATERIAL_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "TR_GI_SAPHEADER_CREATED_PLANT_CODE",
                    "operator" => "=",
                    "value" => session("plant")
                ]
            ],
            "multiple_rows" => true
        ]);

        $tp_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_HEADER",
            "join" => [
                    [
                    "join_type" => "inner",
                    "table_name" => "TR_TP_DETAIL",
                    "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_TP_HEADER_ID",
                    "operator" => "=",
                    "on2" => "TR_TP_HEADER.TR_TP_HEADER_ID",
                    ]
                ],
            "order_by" => [
                [
                    "field" => "TR_TP_HEADER_CREATED_TIMESTAMP",
                    "type" => "DESC",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_MATERIAL_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ],
                [
                    "field_name" => "TR_TP_HEADER_PLANT_CODE",
                    "operator" => "=",
                    "value" => session("plant")
                ]
            ],
            "multiple_rows" => true
        ]);

        // $cancellation_data = std_get([
        //     "select" => ["*"],
        //     "table_name" => "TR_CANCELLATION_MVT",
        //     "join" => [
        //             [
        //             "join_type" => "inner",
        //             "table_name" => "TR_TP_DETAIL",
        //             "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_TP_HEADER_ID",
        //             "operator" => "=",
        //             "on2" => "TR_TP_HEADER.TR_TP_HEADER_ID",
        //             ]
        //         ],
        //     "order_by" => [
        //              [
        //                 "field" => "TR_TP_HEADER_CREATED_TIMESTAMP",
        //                 "type" => "DESC",
        //                 ]
        //             ],
        //     "where" => [
        //                  [
        //                     "field_name" => "TR_TP_DETAIL_MATERIAL_CODE",
        //                     "operator" => "=",
        //                     "value" => $request->code,
        //                     ]
        //                 ],
        //     "multiple_rows" => true
        //             ]);

        return view('master_data/material/detail', [
            "gr_data" => $gr_data,
            "gi_data" => $gi_data,
            "tp_data" => $tp_data,
        ]);
    }
}

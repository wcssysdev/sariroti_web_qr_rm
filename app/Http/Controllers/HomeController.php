<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (session("user_role") == 1 || session("user_role") == 2 || session("user_role") == 6) {
            $gr_data = std_get([
                "select" => ["TR_GR_HEADER.*"],
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
                "where" => [
                    [
                        "field_name" => "TR_GR_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "SUCCESS"
                    ],
                    [
                        "field_name" => "TR_GR_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "WARNING"
                    ],
                    [
                        "field_name" => "TR_GR_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "I"
                    ],
                    [
                        "field_name" => "TR_GR_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "U"
                    ],
                    [
                        "field_name" => "TR_GR_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "D"
                    ],
                    [
                        "field_name" => "TR_GR_DETAIL_UNLOADING_PLANT",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ]
            ]);
            $gr_data = array_map("unserialize", array_unique(array_map("serialize", $gr_data)));
    
            $gi_data = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPHEADER",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPHEADER_STATUS",
                        "operator" => "!=",
                        "value" => "SUCCESS"
                    ],
                    [
                        "field_name" => "TR_GI_SAPHEADER_STATUS",
                        "operator" => "!=",
                        "value" => "WARNING"
                    ],
                    [
                        "field_name" => "TR_GI_SAPHEADER_STATUS",
                        "operator" => "!=",
                        "value" => "I"
                    ],
                    [
                        "field_name" => "TR_GI_SAPHEADER_STATUS",
                        "operator" => "!=",
                        "value" => "U"
                    ],
                    [
                        "field_name" => "TR_GI_SAPHEADER_STATUS",
                        "operator" => "!=",
                        "value" => "D"
                    ],
                    [
                        "field_name" => "TR_GI_SAPHEADER_CREATED_PLANT_CODE",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ]
            ]);
    
            $tp_data = std_get([
                "select" => ["*"],
                "table_name" => "TR_TP_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_TP_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "SUCCESS"
                    ],
                    [
                        "field_name" => "TR_TP_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "WARNING"
                    ],
                    [
                        "field_name" => "TR_TP_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "I"
                    ],
                    [
                        "field_name" => "TR_TP_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "U"
                    ],
                    [
                        "field_name" => "TR_TP_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "D"
                    ],
                    [
                        "field_name" => "TR_TP_HEADER_PLANT_CODE",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ]
            ]);
    
            $cancellation_data = std_get([
                "select" => ["*"],
                "table_name" => "TR_CANCELATION_MVT",
                "where" => [
                    [
                        "field_name" => "TR_CANCELLATION_MVT_STATUS",
                        "operator" => "!=",
                        "value" => "SUCCESS"
                    ],
                    [
                        "field_name" => "TR_CANCELLATION_MVT_STATUS",
                        "operator" => "!=",
                        "value" => "WARNING"
                    ],
                    [
                        "field_name" => "TR_CANCELLATION_MVT_STATUS",
                        "operator" => "!=",
                        "value" => "I"
                    ],
                    [
                        "field_name" => "TR_CANCELLATION_MVT_STATUS",
                        "operator" => "!=",
                        "value" => "U"
                    ],
                    [
                        "field_name" => "TR_CANCELLATION_MVT_STATUS",
                        "operator" => "!=",
                        "value" => "D"
                    ],
                    [
                        "field_name" => "TR_CANCELLATION_PLANT_CODE",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ]
            ]);

            $pid_data = std_get([
                "select" => ["*"],
                "table_name" => "TR_PID_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_PID_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "SUCCESS"
                    ],
                    [
                        "field_name" => "TR_PID_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "WARNING"
                    ],
                    [
                        "field_name" => "TR_PID_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "I"
                    ],
                    [
                        "field_name" => "TR_PID_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "U"
                    ],
                    [
                        "field_name" => "TR_PID_HEADER_STATUS",
                        "operator" => "!=",
                        "value" => "D"
                    ],
                    [
                        "field_name" => "TR_PID_HEADER_PLANT",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ]
            ]);
    
            return view('home/dashboard', [
                "gr_data" => $gr_data,
                "gi_data" => $gi_data,
                "tp_data" => $tp_data,
                "cancellation_data" => $cancellation_data,
                "pid_data" => $pid_data
            ]);
        }
        else{
            return view('home/dashboard_other');
        }
    }
}
<?php

namespace App\Http\Controllers\PurchaseOrder\GoodIssue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ViewController extends Controller
{
    public function index(Request $request)
    {
        $conditions = [
            [
                "field_name" => "TR_PO_HEADER_STATUS",
                "operator" => "!=",
                "value" => "D"
            ],
            [
                "field_name" => "TR_PO_HEADER_STATUS",
                "operator" => "!=",
                "value" => "E"
            ],
            [
                "field_name" => "TR_PO_HEADER_SUP_PLANT",
                "operator" => "=",
                "value" => session("plant")
            ],
            [
                "field_name" => "TR_PO_HEADER_IS_DELETED",
                "operator" => "=",
                "value" => false
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
                    "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
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
                    "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                    "operator" => "<=",
                    "value" => $request->end_date
                ]
            ]);
        }

        if (isset($request->plant_code) && $request->plant_code != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PO_HEADER_SUP_PLANT",
                    "operator" => "=",
                    "value" => $request->plant_code
                ]
            ]);
        }

        if (isset($request->vendor_code) && $request->vendor_code != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PO_HEADER_VENDOR",
                    "operator" => "=",
                    "value" => $request->vendor_code
                ]
            ]);
        }

        $plant_data = get_master_data("MA_PLANT");
        $vendor_data = get_master_data("MA_VENDOR");

        $po_gi_data_non_zret = std_get([
            "select" => ["TR_PO_HEADER.*","MA_VENDOR.MA_VENDOR_NAME","MA_PLANT.MA_PLANT_NAME"],
            "table_name" => "TR_PO_HEADER",
            "join" => [
                [
                    "join_type" => "LEFT",
                    "table_name" => "MA_VENDOR",
                    "on1" => "MA_VENDOR.MA_VENDOR_CODE",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_VENDOR",
                ],
                [
                    "join_type" => "LEFT",
                    "table_name" => "MA_PLANT",
                    "on1" => "MA_PLANT.MA_PLANT_CODE",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_SUP_PLANT",
                ]
            ],
            "where" => $conditions
        ]);

        $conditions = [
            [
                "field_name" => "TR_PO_HEADER_TYPE",
                "operator" => "=",
                "value" => "ZRET"
            ],
            [
                "field_name" => "TR_PO_HEADER_STATUS",
                "operator" => "!=",
                "value" => "D"
            ],
            [
                "field_name" => "TR_PO_HEADER_STATUS",
                "operator" => "!=",
                "value" => "E"
            ],
            [
                "field_name" => "TR_PO_DETAIL_PLANT_RCV",
                "operator" => "=",
                "value" => session("plant")
            ],
            [
                "field_name" => "TR_PO_HEADER_IS_DELETED",
                "operator" => "=",
                "value" => false
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-")."01";
        }
        if (isset($request->start_date) && $request->start_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                    "operator" => ">=",
                    "value" => $request->start_date
                ]
            ]);
        }

        if (!isset($request->end_date) || $request->end_date == "") {
            $request->end_date = date("Y-m-d");
        }
        if (isset($request->end_date) && $request->end_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                    "operator" => "<=",
                    "value" => $request->end_date
                ]
            ]);
        }

        if (isset($request->plant_code) && $request->plant_code != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PO_HEADER_SUP_PLANT",
                    "operator" => "=",
                    "value" => $request->plant_code
                ]
            ]);
        }
        if (isset($request->vendor_code) && $request->vendor_code != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PO_HEADER_VENDOR",
                    "operator" => "=",
                    "value" => $request->vendor_code
                ]
            ]);
        }

        $po_gi_data_zret = std_get([
            "select" => ["TR_PO_HEADER.*","MA_VENDOR.MA_VENDOR_NAME","MA_PLANT.MA_PLANT_NAME"],
            "table_name" => "TR_PO_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_PO_DETAIL",
                    "on1" => "TR_PO_HEADER.TR_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "on2" => "TR_PO_DETAIL.TR_PO_DETAIL_PO_HEADER_NUMBER",
                ],
                [
                    "join_type" => "LEFT",
                    "table_name" => "MA_VENDOR",
                    "on1" => "MA_VENDOR.MA_VENDOR_CODE",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_VENDOR",
                ],
                [
                    "join_type" => "LEFT",
                    "table_name" => "MA_PLANT",
                    "on1" => "MA_PLANT.MA_PLANT_CODE",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_SUP_PLANT",
                ]
            ],
            "where" => $conditions,
            "distinct" => true
        ]);

        return view('transaction/purchase_order/good_issue/view', [
            "data" => array_merge($po_gi_data_zret, $po_gi_data_non_zret),
            "plant" => $plant_data,
            "vendor" => $vendor_data,
            "start" => $request->start_date,
            "end" => $request->end_date,
            "plant_selected" => $request->plant_code,
            "vendor_selected" => $request->vendor_code
        ]);
    }
}

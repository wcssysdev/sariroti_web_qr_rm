<?php

namespace App\Http\Controllers\PurchaseOrder\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index(Request $request)
    {
        $conditions = [
            // [
            //     "field_name" => "TR_PO_HEADER_SUP_PLANT",
            //     "operator" => "=",
            //     "value" => session("plant")
            // ]
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

        $po_data = std_get([
            "select" => "TR_PO_HEADER.*",
            "table_name" => "TR_PO_HEADER",
            "where" => $conditions
        ]);

        return view('transaction/purchase_order/master/view', [
            "start" => $request->start_date,
            "end" => $request->end_date,
            "po_data" => $po_data
        ]);
    }

    public function master_data_request_sap(Request $request)
    {
        $response = export_request_po_data_csv("PO","PO");
        if ($response["code"] == 200) {
            return response()->json([
                "code" => 200,
                "message" => "successfully request PO Data "
            ],200);
        }else {
            return response()->json([
                "code" => 500,
                "message" => "There's error when request PO Data to sap "
            ],500);
        }
    }

    public function master_data_sync_sap(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, url('/')."/purchase_order/receive");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch); 
        curl_close($ch);
        return response()->json([
            "code" => 200,
            "message" => "successfully sync PO Data "
        ],200);
    }

    public function detail(Request $request)
    {
        $header_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ],
            "first_row" => true
        ]);

        $detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ],
            "first_row" => false
        ]);

        return view('transaction/purchase_order/master/detail', [
            "header_data" => $header_data,
            "detail_data" => $detail_data
        ]);
    }
}

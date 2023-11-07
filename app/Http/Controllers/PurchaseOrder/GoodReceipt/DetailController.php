<?php

namespace App\Http\Controllers\PurchaseOrder\GoodReceipt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        if (isset($selects)) {
            $select_fields = $selects;
        }
        else {
            $select_fields = ["*"];
        }

        $conditions = [];
        $detail_conditions = [];
        if (isset($request->gr_po_number)) {
            $conditions[0]["field_name"] = "TR_PO_HEADER_NUMBER";
            $conditions[0]["operator"] = "=";
            $conditions[0]["value"] = $request->gr_po_number;
            $conditions[1]["field_name"] = "TR_PO_HEADER_IS_DELETED";
            $conditions[1]["operator"] = "=";
            $conditions[1]["value"] = false;
            $detail_conditions[0]["field_name"] = "TR_PO_DETAIL_PO_HEADER_NUMBER";
            $detail_conditions[0]["operator"] = "=";
            $detail_conditions[0]["value"] = $request->gr_po_number;
        }

        $data = std_get([
            "select" => $select_fields,
            "table_name" => "TR_PO_HEADER",
            "where" => $conditions,
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $detail_data = std_get([
            "select" => $select_fields,
            "table_name" => "TR_PO_DETAIL",
            "where" => $detail_conditions,
            "order_by" => [
                [
                    "field" => "TR_PO_DETAIL_MATERIAL_LINE_NUM",
                    "type" => "ASC",
                ]
            ],
        ]);

        $gr_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "where" => [
                [
                    "field_name" => "TR_GR_HEADER_PO_NUMBER",
                    "operator" => "=",
                    "value" => $request->gr_po_number
                ]
            ]
        ]);

        return view('transaction/purchase_order/good_receipt/detail', [
            "header_data" => $data,
            "detail_data" => $detail_data,
            "gr_data" => $gr_data
        ]);
    }

    public function detail(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "where" => [
                [
                    "field_name" => "TR_GR_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->gr_header_id
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->gr_header_id
                ]
            ],
        ]);

        return view('transaction/purchase_order/good_receipt/detail_detail', [
            "header_data" => $data,
            "detail_data" => $detail_data
        ]);
    }

    public function print(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_PO_HEADER",
                    "on1" => "TR_GR_HEADER.TR_GR_HEADER_PO_NUMBER",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_NUMBER",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_GR_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->gr_header_id
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $user_data = std_get([
            "select" => ["MA_USRACC_FULL_NAME"],
            "table_name" => "MA_USRACC",
            "where" => [
                [
                    "field_name" => "MA_USRACC_ID",
                    "operator" => "=",
                    "value" => $data["TR_GR_HEADER_CREATED_BY"]
                ]
            ],
            "first_row" => true
        ]);

        if ($user_data == NULL) {
            $user_data["MA_USRACC_FULL_NAME"] = "";
        }

        $detail_data = std_get([
            "select" => ["TR_GR_DETAIL.*","TR_PO_DETAIL.TR_PO_DETAIL_QTY_ORDER","TR_PO_DETAIL.TR_PO_DETAIL_QTY_DELIV"],
            "table_name" => "TR_GR_DETAIL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_PO_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->gr_header_id
                ]
            ],
        ]);
    
        std_update([
            "table_name" => "TR_GR_HEADER",
            "where" => ["TR_GR_HEADER_ID" => $request->gr_header_id],
            "data" => [
                "TR_GR_HEADER_PRINT_COUNT" => $data["TR_GR_HEADER_PRINT_COUNT"]+1
            ]
        ]);

        $data = std_get([
            "select" => ["TR_GR_HEADER.*","TR_PO_HEADER.*","MA_VENDOR.MA_VENDOR_NAME"],
            "table_name" => "TR_GR_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_PO_HEADER",
                    "on1" => "TR_GR_HEADER.TR_GR_HEADER_PO_NUMBER",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_NUMBER",
                ],
                [
                    "join_type" => "LEFT",
                    "table_name" => "MA_VENDOR",
                    "on1" => "MA_VENDOR.MA_VENDOR_CODE",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_VENDOR",
                ],
            ],
            "where" => [
                [
                    "field_name" => "TR_GR_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->gr_header_id
                ]
            ],
            "first_row" => true
        ]);

        return view('transaction/purchase_order/good_receipt/print', [
            "header_data" => $data,
            "detail_data" => $detail_data,
            "user_data" => $user_data
        ]);
    }

    public function print_qr(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->gr_detail_id
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }
        
        return view('transaction/purchase_order/good_receipt/print_qr', [
            "data" => $data
        ]);
    }

    public function delete(Request $request)
    {
        $gr_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "where" => [
                [
                    "field_name" => "TR_GR_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->gr_header_id
                ]
            ],
            "first_row" => true
        ]);

        if ($gr_data["TR_GR_HEADER_STATUS"] == "ERROR") {
            std_delete([
                "table_name" => "TR_GR_HEADER",
                "where" => [
                    "TR_GR_HEADER_ID" => $request->gr_header_id
                ]
            ]);
            std_delete([
                "table_name" => "TR_GR_DETAIL",
                "where" => [
                    "TR_GR_DETAIL_HEADER_ID" => $request->gr_header_id
                ]
            ]);
            return response()->json([
                'message' => "Data GR dengan ID: ".$request->gr_header_id." berhasil dihapus"
            ],200);
        }
        else{
            return response()->json([
                'message' => "Dokumen yang boleh di batalkan hanyalah yang memiliki status ERROR, transaksi dibatalkan"
            ],400);
        }
    }
}

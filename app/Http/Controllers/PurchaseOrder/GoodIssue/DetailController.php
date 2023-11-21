<?php

namespace App\Http\Controllers\PurchaseOrder\GoodIssue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;


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
        if (isset($request->gi_po_number)) {
            $conditions[0]["field_name"] = "TR_PO_HEADER_NUMBER";
            $conditions[0]["operator"] = "=";
            $conditions[0]["value"] = $request->gi_po_number;
            $conditions[1]["field_name"] = "TR_PO_HEADER_IS_DELETED";
            $conditions[1]["operator"] = "=";
            $conditions[1]["value"] = false;
            $detail_conditions[0]["field_name"] = "TR_PO_DETAIL_PO_HEADER_NUMBER";
            $detail_conditions[0]["operator"] = "=";
            $detail_conditions[0]["value"] = $request->gi_po_number;
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
        ]);

        $gi_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_PO_NUMBER",
                    "operator" => "=",
                    "value" => $request->gi_po_number
                ],
                [
                    "field_name" => "TR_GI_SAPHEADER_IS_CANCELLED",
                    "operator" => "=",
                    "value" => false
                ]
            ]
        ]);


        return view('transaction/purchase_order/good_issue/detail', [
            "header_data" => $data,
            "detail_data" => $detail_data,
            "gi_data" => $gi_data
        ]);
    }

    public function detail(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->gi_header_id
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPDETAIL",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->gi_header_id
                ]
            ],
        ]);

        return view('transaction/purchase_order/good_issue/detail_detail', [
            "header_data" => $data,
            "detail_data" => $detail_data
        ]);
    }

    public function print_qr(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPDETAIL",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPDETAIL_ID",
                    "operator" => "=",
                    "value" => $request->gi_detail_id
                ],
            ],
            "join" => [
                [
                    "join_type" => "left",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }
        
        return view('transaction/purchase_order/good_issue/print_qr', [
            "data" => $data
        ]);
    }

    public function print(Request $request)
    {
        $data = std_get([
            "select" => ["TR_GI_SAPHEADER.*","TR_GI_SAPHEADER.*","MA_PLANT.MA_PLANT_NAME"],
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->gi_header_id
                ]
            ],
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "MA_PLANT",
                    "on1" => "MA_PLANT.MA_PLANT_CODE",
                    "operator" => "=",
                    "on2" => "TR_GI_SAPHEADER.TR_GI_SAPHEADER_CREATED_PLANT_CODE",
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $detail_data = std_get([
            "select" => ["TR_GI_SAPDETAIL.*", "MA_SLOC.MA_SLOC_DESC"],
            "table_name" => "TR_GI_SAPDETAIL",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->gi_header_id
                ],
                [
                    "field_name" => "MA_SLOC.MA_SLOC_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ],
            ],
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "MA_SLOC",
                    "on1" => "MA_SLOC.MA_SLOC_CODE",
                    "operator" => "=",
                    "on2" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_SLOC",
                ]
            ],
        ]);

        return view('transaction/purchase_order/good_issue/print', [
            "header_data" => $data,
            "detail_data" => $detail_data
        ]);
    }

    public function delete(Request $request)
    {
        $gi_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->gi_header_id
                ]
            ],
            "first_row" => true
        ]);

        if ($gi_data["TR_GI_SAPHEADER_STATUS"] == "ERROR") {
            $gi_detail_data = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPDETAIL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID"
                    ]
                ],
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                        "operator" => "=",
                        "value" => $request->gi_header_id
                    ]
                ],
                "first_row" => false
            ]);

            if ($gi_detail_data != NULL) {
                foreach ($gi_detail_data as $row) {
                    std_update([
                        "table_name" => "TR_GR_DETAIL",
                        "where" => ["TR_GR_DETAIL_ID" => $row["TR_GI_SAPDETAIL_GR_DETAIL_ID"]],
                        "data" => [
                            "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" + '.$row["TR_GI_SAPDETAIL_MOBILE_QTY"])
                        ]
                    ]);

                    insert_material_log([
                        "material_code" => $row["TR_GI_SAPDETAIL_MATERIAL_CODE"],
                        "plant_code" => session("plant"),
                        "posting_date" => $gi_data["TR_GI_SAPHEADER_PSTG_DATE"],
                        "movement_type" => $gi_data["TR_GI_SAPHEADER_MVT_CODE"],
                        "gr_detail_id" => $row["TR_GI_SAPDETAIL_GR_DETAIL_ID"],
                        "base_qty" => $row["TR_GI_SAPDETAIL_MOBILE_QTY"],
                        "base_uom" => $row["TR_GI_SAPDETAIL_BASE_UOM"],
                        "created_by" => session("id")
                    ]);
                }
                std_delete([
                    "table_name" => "TR_GI_SAPHEADER",
                    "where" => [
                        "TR_GI_SAPHEADER_ID" => $request->gi_header_id
                    ]
                ]);
                std_delete([
                    "table_name" => "TR_GI_SAPDETAIL",
                    "where" => [
                        "TR_GI_SAPDETAIL_SAPHEADER_ID" => $request->gi_header_id
                    ]
                ]);

                return response()->json([
                    'message' => "Data GI dengan ID: ".$request->gi_header_id." berhasil dihapus"
                ],200);
            }
            else{
                return response()->json([
                    'message' => "Data GI tidak ditemukan, transaksi dibatalkan"
                ],400);
            }
        }
        else{
            return response()->json([
                'message' => "Dokumen yang boleh di batalkan hanyalah yang memiliki status ERROR, transaksi dibatalkan"
            ],400);
        }
    }
}

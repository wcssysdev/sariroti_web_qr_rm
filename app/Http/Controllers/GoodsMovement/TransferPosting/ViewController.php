<?php

namespace App\Http\Controllers\GoodsMovement\TransferPosting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ViewController extends Controller
{
    public function index(Request $request)
    {
        $conditions = [
            [
                "field_name" => "TR_TP_HEADER_STATUS",
                "operator" => "!=",
                "value" => "D"
            ],
            [
                "field_name" => "TR_TP_HEADER_STATUS",
                "operator" => "!=",
                "value" => "E"
            ],
            [
                "field_name" => "TR_TP_HEADER_PLANT_CODE",
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
                    "field_name" => "TR_TP_HEADER_CREATED_TIMESTAMP",
                    "operator" => ">=",
                    "value" => $request->start_date." 00:00:00"
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
                    "field_name" => "TR_TP_HEADER_CREATED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => $request->end_date ." 23:59:59"
                ]
            ]);
        }

        if (isset($request->plant_code) && $request->plant_code != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_TP_HEADER_PLANT_CODE",
                    "operator" => "=",
                    "value" => $request->plant_code
                ]
            ]);
        }

        $plant_data = get_master_data("MA_PLANT");

        $data = std_get([
            "select" => "TR_TP_HEADER.*",
            "table_name" => "TR_TP_HEADER",
            "where" => $conditions
        ]);

        return view('transaction/goods_movement/transfer_posting/view', [
            "data" => $data,
            "plant" => $plant_data,
            "start" => $request->start_date,
            "end" => $request->end_date,
            "plant_selected" => $request->plant_code
        ]);
    }

    public function print(Request $request)
    {
        $data = std_get([
            "select" => "*",
            "table_name" => "TR_TP_HEADER",
            "where" => [
                [
                    "field_name" => "TR_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->tp_header_id
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $detail_data = std_get([
            "select" => ["TR_TP_DETAIL.*","TR_GR_DETAIL_SLOC","sloc_a.MA_SLOC_DESC as sloc_from", "sloc_b.MA_SLOC_DESC as sloc_to"],
            "table_name" => "TR_TP_DETAIL",
            "join" => [
                [
                    "join_type" => "left",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "MA_SLOC as sloc_a",
                    "on1" => "sloc_a.MA_SLOC_CODE",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_SLOC",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "MA_SLOC as sloc_b",
                    "on1" => "sloc_b.MA_SLOC_CODE",
                    "operator" => "=",
                    "on2" => "TR_TP_DETAIL.TR_TP_DETAIL_SLOC",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->tp_header_id
                ],
                [
                    "field_name" => "sloc_a.MA_SLOC_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ],
                [
                    "field_name" => "sloc_b.MA_SLOC_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ],
            ]
        ]);

        return view('transaction/goods_movement/transfer_posting/print', [
            "header_data" => $data,
            "detail_data" => $detail_data
        ]);
    }

    public function delete(Request $request)
    {
        $tp_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_HEADER",
            "where" => [
                [
                    "field_name" => "TR_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->tp_header_id
                ]
            ],
            "first_row" => true
        ]);

        $tp_detail = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->tp_header_id
                ]
            ],
            "first_row" => false
        ]);

        if ($tp_detail == NULL) {
            return response()->json([
                'message' => "TP Detail tidak ditemukan"
            ],400);
        }

        if ($tp_header["TR_TP_HEADER_STATUS"] == "ERROR") {
            if ($tp_header["TR_TP_HEADER_MVT_CODE"] == "311" || $tp_header["TR_TP_HEADER_MVT_CODE"] == "411" || $tp_header["TR_TP_HEADER_MVT_CODE"] == "551") {
                foreach ($tp_detail as $row) {
                    std_update([
                        "table_name" => "TR_GR_DETAIL",
                        "where" => ["TR_GR_DETAIL_ID" => $row["TR_TP_DETAIL_GR_DETAIL_ID"]],
                        "data" => [
                            "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" + '.$row["TR_TP_DETAIL_BASE_QTY"])
                        ]
                    ]);

                    insert_material_log([
                        "material_code" => $row["TR_TP_DETAIL_MATERIAL_CODE"],
                        "plant_code" => $tp_header["TR_TP_HEADER_PLANT_CODE"],
                        "posting_date" => $tp_header["TR_TP_HEADER_PSTG_DATE"],
                        "movement_type" => $tp_header["TR_TP_HEADER_MVT_CODE"],
                        "gr_detail_id" => $row["TR_TP_DETAIL_GR_DETAIL_ID"],
                        "base_qty" => $row["TR_TP_DETAIL_BASE_QTY"],
                        "base_uom" => $row["TR_TP_DETAIL_BASE_UOM"],
                        "created_by" => "0"
                    ]);

                    if ($row["TR_TP_DETAIL_SLOC"] == "1419" || $row["TR_TP_DETAIL_SLOC"] == "1900") {
                        $gr_detail = std_get([
                            "select" => ["*"],
                            "table_name" => "TR_GR_DETAIL",
                            "join" => [
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
                                    "field_name" => "TR_GR_DETAIL_GR_REFERENCE",
                                    "operator" => "=",
                                    "value" => $row["TR_TP_DETAIL_GR_DETAIL_ID"]
                                ]
                            ],
                            "first_row" => true
                        ]);

                        insert_material_log([
                            "material_code" => $gr_detail["TR_GR_DETAIL_MATERIAL_CODE"],
                            "plant_code" => $gr_detail["TR_GR_DETAIL_UNLOADING_PLANT"],
                            "posting_date" => $gr_detail["TR_GR_HEADER_PSTG_DATE"],
                            "movement_type" => $gr_detail["TR_GR_HEADER_MVT_CODE"],
                            "gr_detail_id" => $gr_detail["TR_GR_DETAIL_ID"],
                            "base_qty" => -$gr_detail["TR_GR_DETAIL_BASE_QTY"],
                            "base_uom" => $gr_detail["TR_GR_DETAIL_BASE_UOM"],
                            "created_by" => "0"
                        ]);

                        std_delete([
                            "table_name" => "TR_GR_DETAIL",
                            "where" => [
                                "TR_GR_DETAIL_ID" => $gr_detail["TR_GR_DETAIL_ID"]
                            ]
                        ]);
                    }

                }
            }
            elseif ($tp_header["TR_TP_HEADER_MVT_CODE"] == "Y21") {
                foreach ($tp_detail as $row) {
                    $gr_detail = std_get([
                        "select" => ["*"],
                        "table_name" => "TR_GR_DETAIL",
                        "join" => [
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
                                "field_name" => "TR_GR_DETAIL_GR_REFERENCE",
                                "operator" => "=",
                                "value" => $row["TR_TP_DETAIL_GR_DETAIL_ID"]
                            ]
                        ],
                        "first_row" => true
                    ]);

                    insert_material_log([
                        "material_code" => $gr_detail["TR_GR_DETAIL_MATERIAL_CODE"],
                        "plant_code" => $gr_detail["TR_GR_DETAIL_UNLOADING_PLANT"],
                        "posting_date" => $gr_detail["TR_GR_HEADER_PSTG_DATE"],
                        "movement_type" => $gr_detail["TR_GR_HEADER_MVT_CODE"],
                        "gr_detail_id" => $gr_detail["TR_GR_DETAIL_ID"],
                        "base_qty" => -$gr_detail["TR_GR_DETAIL_BASE_QTY"],
                        "base_uom" => $gr_detail["TR_GR_DETAIL_BASE_UOM"],
                        "created_by" => "0"
                    ]);

                    std_delete([
                        "table_name" => "TR_GR_DETAIL",
                        "where" => [
                            "TR_GR_DETAIL_ID" => $gr_detail["TR_GR_DETAIL_ID"]
                        ]
                    ]);
                }
            }
            std_delete([
                "table_name" => "TR_TP_HEADER",
                "where" => [
                    "TR_TP_HEADER_ID" => $request->tp_header_id
                ]
            ]);
            std_delete([
                "table_name" => "TR_TP_DETAIL",
                "where" => [
                    "TR_TP_DETAIL_TP_HEADER_ID" => $request->tp_header_id
                ]
            ]);
            return response()->json([
                'message' => "Data TP dengan ID: ".$request->tp_header_id." berhasil dihapus"
            ],200);
        }
        else{
            return response()->json([
                'message' => "Dokumen yang boleh di batalkan hanyalah yang memiliki status ERROR, transaksi dibatalkan"
            ],400);
        }
    }
}

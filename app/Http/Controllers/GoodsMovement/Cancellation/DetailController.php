<?php

namespace App\Http\Controllers\GoodsMovement\Cancellation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index(Request $request)
    {
//dd($request);
        if ($request->doc_type == "GR") {
            $header = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_GR_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ],
                    [
                        "field_name" => "TR_GR_HEADER_PSTG_DATE",
                        "operator" => "like",
                        "value" => "{$request->doc_number_year}%"
                    ],

                ],
                "first_row" => true
            ]);

            if (!$header) {
                return redirect()->route('transaction_goods_movement_cancellation_add')
                    ->with([
                        'error' => "GR Dokumen $request->doc_number tahun $request->doc_number_year tidak ada, coba lagi dengan mat doc yang benar"
                    ]);
            }

            $detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_DETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GR_DETAIL_HEADER_ID",
                        "operator" => "=",
                        "value" => $header["TR_GR_HEADER_ID"]
                    ]
                ]
            ]);

            if (count($detail) > 0 and $detail[0]['TR_GR_DETAIL_UNLOADING_PLANT'] != session('plant')) {
                return redirect()->route('transaction_goods_movement_cancellation_add')
                    ->with([
                        'error' => "Cross Plant => Dokumen $request->doc_number punya plant {$detail[0]['TR_GR_DETAIL_UNLOADING_PLANT']} "
                    ]);
            }
            return view('transaction/goods_movement/cancellation/detail', [
                "cancellation_type" => "GR",
                "doc_number" => $request->doc_number,
                "header_data" => $header,
                "detail_data" => $detail
            ]);
        } else if ($request->doc_type == "GI") {
            $header = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPHEADER",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPHEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ],
                    [
                        "field_name" => "TR_GI_SAPHEADER_PSTG_DATE",
                        "operator" => "like",
                        "value" => "{$request->doc_number_year}%"
                    ],
                ],
                "first_row" => true,
//                "dump" => true
            ]);

//                        dd(['stop',$header]);
            if (!$header) {
                return redirect()->route('transaction_goods_movement_cancellation_add')
                    ->with([
                        'error' => "Dokumen GI $request->doc_number tahun $request->doc_number_year tidak ada, coba lagi dengan mat doc yang benar"
                    ]);
            }

            if ($header["TR_GI_SAPHEADER_CREATED_PLANT_CODE"] != session('plant')) {
                return redirect()->route('transaction_goods_movement_cancellation_add')
                    ->with([
                        'error' => "Cross Plant => Dokumen $request->doc_number punya plant {$header["TR_GI_SAPHEADER_CREATED_PLANT_CODE"]} "
                    ]);
            }
            $detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPDETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                        "operator" => "=",
                        "value" => $header["TR_GI_SAPHEADER_ID"]
                    ]
                ]
            ]);


            return view('transaction/goods_movement/cancellation/detail', [
                "cancellation_type" => "GI",
                "doc_number" => $request->doc_number,
                "header_data" => $header,
                "detail_data" => $detail
            ]);
        } else if ($request->doc_type == "TP") {
            $header = std_get([
                "select" => ["*"],
                "table_name" => "TR_TP_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_TP_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ],
                    [
                        "field_name" => "TR_TP_HEADER_PSTG_DATE",
                        "operator" => "like",
                        "value" => "{$request->doc_number_year}%"
                    ],
                ],
                "first_row" => true
            ]);

            if (!$header) {
                return redirect()->route('transaction_goods_movement_cancellation_add')
                    ->with([
                        'error' => "Dokumen TP $request->doc_number tahun $request->doc_number_year tidak ada, coba lagi dengan mat doc yang benar"
                    ]);
            }
            if ($header["TR_TP_HEADER_PLANT_CODE"] != session('plant')) {
                return redirect()->route('transaction_goods_movement_cancellation_add')
                    ->with([
                        'error' => "Cross Plant => Dokumen $request->doc_number punya plant {$header["TR_TP_HEADER_PLANT_CODE"]} "
                    ]);
            }
            $detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_TP_DETAIL",
                "join" => [
                    [
                        "join_type" => "left",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    ]
                ],
                "where" => [
                    [
                        "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                        "operator" => "=",
                        "value" => $header["TR_TP_HEADER_ID"]
                    ]
                ]
            ]);

            return view('transaction/goods_movement/cancellation/detail', [
                "cancellation_type" => "TP",
                "doc_number" => $request->doc_number,
                "header_data" => $header,
                "detail_data" => $detail
            ]);
        }
    }

    public function view_cancellation_detail(Request $request)
    {
        $cancellation_type = "";
        $cancellation_header_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_CANCELATION_MVT",
            "where" => [
                [
                    "field_name" => "TR_CANCELLATION_MVT_ID",
                    "operator" => "=",
                    "value" => $request->cancellation_id
                ]
            ],
            "first_row" => true
        ]);
//dd($cancellation_header_data);
        $header = [];
        $detail_data = [];
        if ($cancellation_header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "102") {
            $cancellation_type = "GR";
            $header = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_GR_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $cancellation_header_data["TR_CANCELLATION_MVT_TR_DOC"]
                    ]
                ],
                "first_row" => true
            ]);

            $detail_data = std_get([
                "select" => ["TR_GR_DETAIL.*"],
                "table_name" => "TR_CANCELATION_MVT_DETAIL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_PO_DETAIL",
                        "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
                    ]
                ],
                "where" => [
                    [
                        "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                        "operator" => "=",
                        "value" => $cancellation_header_data["TR_CANCELLATION_MVT_ID"]
                    ]
                ]
            ]);
        }
        //GI
        else if ($cancellation_header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "162" || $cancellation_header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "352") {
            $cancellation_type = "GI";
            $header = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPHEADER",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPHEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $cancellation_header_data["TR_CANCELLATION_MVT_TR_DOC"]
                    ]
                ],
                "first_row" => true
            ]);

            $detail_data = std_get([
                "select" => ["TR_GI_SAPDETAIL.*"],
                "table_name" => "TR_CANCELATION_MVT_DETAIL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GI_SAPDETAIL",
                        "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                        "operator" => "=",
                        "on2" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_SAPHEADER_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_PO_DETAIL",
                        "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
                    ]
                ],
                "where" => [
                    [
                        "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                        "operator" => "=",
                        "value" => $cancellation_header_data["TR_CANCELLATION_MVT_ID"]
                    ]
                ]
            ]);
        }
        //TP
        else if ($cancellation_header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "312" || $cancellation_header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "Y22" || $cancellation_header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "552") {
            $cancellation_type = "TP";

            $header = std_get([
                "select" => ["*"],
                "table_name" => "TR_TP_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_TP_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $cancellation_header_data["TR_CANCELLATION_MVT_TR_DOC"]
                    ]
                ],
                "first_row" => true
            ]);

            if ($cancellation_header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "Y22") {
                $detail_data = std_get([
                    "select" => ["TR_TP_DETAIL.*", "TR_GR_DETAIL.TR_GR_DETAIL_SLOC"],
                    "table_name" => "TR_CANCELATION_MVT_DETAIL",
                    "join" => [
                        [
                            "join_type" => "inner",
                            "table_name" => "TR_TP_DETAIL",
                            "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                            "operator" => "=",
                            "on2" => "TR_TP_DETAIL.TR_TP_DETAIL_ID",
                        ],
                        [
                            "join_type" => "inner",
                            "table_name" => "TR_GR_DETAIL",
                            "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_Y21_GR_REF",
                            "operator" => "=",
                            "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_Y21_TP_REF",
                        ]
                    ],
                    "where" => [
                        [
                            "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                            "operator" => "=",
                            "value" => $cancellation_header_data["TR_CANCELLATION_MVT_ID"]
                        ]
                    ]
                ]);
            } else {
                $detail_data = std_get([
                    "select" => ["TR_TP_DETAIL.*", "TR_GR_DETAIL.TR_GR_DETAIL_SLOC"],
                    "table_name" => "TR_CANCELATION_MVT_DETAIL",
                    "join" => [
                        [
                            "join_type" => "inner",
                            "table_name" => "TR_TP_DETAIL",
                            "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                            "operator" => "=",
                            "on2" => "TR_TP_DETAIL.TR_TP_DETAIL_ID",
                        ],
                        [
                            "join_type" => "inner",
                            "table_name" => "TR_GR_DETAIL",
                            "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
                            "operator" => "=",
                            "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                        ],
                        [
                            "join_type" => "inner",
                            "table_name" => "TR_PO_DETAIL",
                            "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                            "operator" => "=",
                            "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
                        ]
                    ],
                    "where" => [
                        [
                            "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                            "operator" => "=",
                            "value" => $cancellation_header_data["TR_CANCELLATION_MVT_ID"]
                        ]
                    ]
                ]);

                if (!$detail_data) {
                    $detail_data = std_get([
                        "select" => ["TR_TP_DETAIL.*", "TR_GR_DETAIL.TR_GR_DETAIL_SLOC"],
                        "table_name" => "TR_CANCELATION_MVT_DETAIL",
                        "join" => [
                            [
                                "join_type" => "inner",
                                "table_name" => "TR_TP_DETAIL",
                                "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                                "operator" => "=",
                                "on2" => "TR_TP_DETAIL.TR_TP_DETAIL_ID",
                            ],
                            [
                                "join_type" => "inner",
                                "table_name" => "TR_GR_DETAIL",
                                "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
                                "operator" => "=",
                                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                            ]
                        ],
                        "where" => [
                            [
                                "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                                "operator" => "=",
                                "value" => $cancellation_header_data["TR_CANCELLATION_MVT_ID"]
                            ]
                        ]
                    ]);
                }
            }
        }
        return view('transaction/goods_movement/cancellation/view_detail', [
            "cancellation_type" => $cancellation_type,
            "doc_number" => $request->doc_number,
            "cancellation_header_data" => $cancellation_header_data,
            "header_data" => $header,
            "detail_data" => $detail_data
        ]);
    }
}

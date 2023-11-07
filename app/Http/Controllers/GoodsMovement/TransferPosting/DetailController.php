<?php

namespace App\Http\Controllers\GoodsMovement\TransferPosting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index(Request $request)
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
            "select" => ["TR_TP_DETAIL.*","TR_GR_DETAIL_SLOC","TR_GR_DETAIL_EXP_DATE"],
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
                    "value" => $request->tp_header_id
                ]
            ],
        ]);

        return view('transaction/goods_movement/transfer_posting/detail', [
            'data' => $data,
            'detail_data' => $detail_data
        ]);
    }

    public function print_qr(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->tp_detail_id
                ]
            ],
            "join" => [
                [
                    "join_type" => "left",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }
        
        return view('transaction/goods_movement/transfer_posting/print_qr', [
            "data" => $data
        ]);
    }
}

<?php

namespace App\Http\Controllers\StockOpname;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        $data = std_get([
            "select" => "*",
            "table_name" => "TR_PID_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PID_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->pid_id
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PID_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $data["TR_PID_HEADER_SAP_NO"]
                ]
            ],
        ]);

        return view('transaction/stock_opname/detail', [
            'header_data' => $data,
            'detail_data' => $detail_data
        ]);
    }

    public function material_detail(Request $request)
    {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PID_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->pid_detail_id
                ]
            ],
            "first_row" => true
        ]);

        if ($data == null) {
            abort(404);
        }

        $detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_DETAIL_MATERIAL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_PID_DETAIL_MATERIAL.TR_PID_DETAIL_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_PID_DETAIL_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->pid_detail_id
                ]
            ],
        ]);

        return view('transaction/stock_opname/detail_material', [
            'header_data' => $data,
            'detail_data' => $detail_data
        ]);
    }

    public function approval(Request $request)
    {
        $pid_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PID_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_PID_HEADER_ID,
                ]
            ],
            "first_row" => true
        ]);
        if ($pid_header == NULL) {
            return redirect()->route('transaction_stock_opname_view',['pid_id'=>  $request->TR_PID_HEADER_ID]);
        }
        if ($request->approval_status == "Approved") {

            $pid_detail_material = std_get([
                "select" => ["*"],
                "table_name" => "TR_PID_DETAIL_MATERIAL",
                "where" => [
                    [
                        "field_name" => "TR_PID_DETAIL_HEADER_ID",
                        "operator" => "=",
                        "value" => $request->TR_PID_HEADER_ID
                    ]
                ],
                "first_row" => false
            ]);

            foreach ($pid_detail_material as $row) {
                std_update([
                    "table_name" => "TR_GR_DETAIL",
                    "where" => ["TR_GR_DETAIL_ID" => $row["TR_PID_DETAIL_GR_DETAIL_ID"]],
                    "data" => [
                        "TR_GR_DETAIL_LEFT_QTY" => $row["TR_PID_DETAIL_UPDATED_QTY"]
                    ]
                ]);
            }

            std_update([
                "table_name" => "TR_PID_HEADER",
                "where" => ["TR_PID_HEADER_ID" => $request->TR_PID_HEADER_ID],
                "data" => [
                    "TR_PID_MOBILE_ALLOW_TO_INPUT" => false,
                    "TR_PID_HEADER_APPROVAL_COUNTER" => $pid_header["TR_PID_HEADER_APPROVAL_COUNTER"]+1,
                    "TR_PID_HEADER_APPROVAL_STATUS" => "APPROVED",
                    "TR_PID_HEADER_APPROVAL_NOTES" => $request->notes,
                    "TR_PID_HEADER_APPROVAL_BY" => session("id"),
                    "TR_PID_HEADER_APPROVAL_TIMESTAMP" => date("Y-m-d H:i:s")
                ]
            ]);

            generate_stock_opname_csv($request->TR_PID_HEADER_ID, session("plant"));
        }
        else{
            std_update([
                "table_name" => "TR_PID_HEADER",
                "where" => ["TR_PID_HEADER_ID" => $request->TR_PID_HEADER_ID],
                "data" => [
                    "TR_PID_MOBILE_ALLOW_TO_INPUT" => true,
                    "TR_PID_HEADER_APPROVAL_COUNTER" => $pid_header["TR_PID_HEADER_APPROVAL_COUNTER"]+1,
                    "TR_PID_HEADER_APPROVAL_STATUS" => "REJECTED",
                    "TR_PID_HEADER_APPROVAL_NOTES" => $request->notes,
                    "TR_PID_HEADER_APPROVAL_BY" => session("id"),
                    "TR_PID_HEADER_APPROVAL_TIMESTAMP" => date("Y-m-d H:i:s")
                ]
            ]);
        }
        return redirect()->route('transaction_stock_opname_view');
    }

    public function edit_material_detail(Request $request)
    {
        $header = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PID_HEADER_SAP_NO",
                    "operator" => "=",
                    "value" => $request->pid_header_id
                ]
            ],
            "first_row" => true
        ]);

        if ($header == NULL) {
            abort(404);
        }

        $detail = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PID_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->pid_header_id
                ]
            ],
            "first_row" => false
        ]);

        if ($detail == NULL) {
            abort(404);
        }

        $gr_material_data = [];
        foreach ($detail as $row) {
            $conditions = [
                [
                    "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "operator" => "=",
                    "value" => $row["TR_PID_DETAIL_MATERIAL_CODE"]
                ],
                [
                    "field_name" => "TR_GR_DETAIL_UNLOADING_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ],
                [
                    "field_name" => "TR_GR_DETAIL_LEFT_QTY",
                    "operator" => ">",
                    "value" => 0
                ],
                [
                    "field_name" => "TR_GR_HEADER_SAP_DOC",
                    "operator" => "!=",
                    "value" => null
                ],
                [
                    "field_name" => "TR_GR_HEADER_IS_ADJUSTMENT",
                    "operator" => "=",
                    "value" => false
                ],
                [
                    "field_name" => "TR_GR_DETAIL_IS_CANCELLED",
                    "operator" => "=",
                    "value" => false
                ]
            ];
            if ($row["TR_PID_DETAIL_MATERIAL_SAP_BATCH"] != NULL && $row["TR_PID_DETAIL_MATERIAL_SAP_BATCH"] != "") {
                $conditions = array_merge($conditions, [
                    [
                        "field_name" => "TR_GR_DETAIL_SAP_BATCH",
                        "operator" => "=",
                        "value" => $row["TR_PID_DETAIL_MATERIAL_SAP_BATCH"]
                    ]
                ]);
            }
            $gr_data = std_get([
                "select" => ["TR_GR_DETAIL.*"],
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
                "where" => $conditions,
                "first_row" => false
            ]);
            for ($j=0; $j < count($gr_data); $j++) { 
                $gr_data[$j]["TR_PID_HEADER_ID"] = $header["TR_PID_HEADER_ID"];
                $gr_data[$j]["TR_PID_DETAIL_ID"] = $row["TR_PID_DETAIL_ID"];
            }
            $gr_material_data = array_merge($gr_material_data, $gr_data);
        }
        return view("transaction.stock_opname.edit_material", [
            'detail_data' => $gr_material_data,
            "header_data" => $header
        ]);
    }

    public function save_validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "TR_PID_HEADER_ID" => "required|exists:TR_PID_HEADER,TR_PID_HEADER_ID",
            "TR_PID_POSTING_DATE" => "required|max:10"
        ]);

        $attributeNames = [
            "TR_PID_HEADER_ID" => "TR_PID_HEADER_ID",
            "TR_PID_POSTING_DATE" => "Posting Date"
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function submit(Request $request)
    {
        $validation_res = $this->save_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $pid_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PID_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_PID_HEADER_ID,
                ]
            ],
            "first_row" => true
        ]);
        if ($pid_header == NULL) {
            return response()->json([
                'message' => "Stock Opname Header Not Found"
            ],500);
        }
        if ($pid_header["TR_PID_MOBILE_ALLOW_TO_INPUT"] == false) {
            return response()->json([
                'message' => "Stock Opname Already Input"
            ],500);
        }

        $timestamp = date("Y-m-d H:i:s");
        if ($request->detail == NULL) {
            return response()->json([
                'message' => "Detail Data Not Exist / Empty"
            ],500);
        }

        $detail_data = $request->detail;
        for ($i=0; $i < count($detail_data); $i++) { 
            $gr_detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_DETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GR_DETAIL_ID",
                        "operator" => "=",
                        "value" => $detail_data[$i]["TR_GR_DETAIL_ID"],
                    ]
                ],
                "first_row" => true
            ]);
            if ($gr_detail == NULL) {
                return response()->json([
                    'message' => "Stock Opname Data Not Found, Transaction Cancelled"
                ],500);
            }
            $detail_data[$i] = array_merge($detail_data[$i], $gr_detail);
        }

        $pid_insert = [];
        for ($i=0; $i < count($detail_data); $i++) {
            $pid_insert = array_merge($pid_insert, [
                [
                    "TR_PID_DETAIL_PHOTO" => NULL,
                    "TR_PID_DETAIL_DETAIL_ID" => $detail_data[$i]["TR_PID_DETAIL_ID"],
                    "TR_PID_DETAIL_HEADER_ID" => $detail_data[$i]["TR_PID_HEADER_ID"],
                    "TR_PID_DETAIL_GR_DETAIL_ID" => $detail_data[$i]["TR_GR_DETAIL_ID"],
                    "TR_PID_DETAIL_LEFT_QTY" => $detail_data[$i]["TR_GR_DETAIL_LEFT_QTY"],
                    "TR_PID_DETAIL_UPDATED_QTY" => $detail_data[$i]["qty"],
                    "TR_PID_DETAIL_DIFF" => $detail_data[$i]["TR_GR_DETAIL_LEFT_QTY"] - $detail_data[$i]["qty"]
                ]
            ]);
        }
        if ($pid_insert != []) {
            std_delete([
                "table_name" => "TR_PID_DETAIL_MATERIAL",
                "where" => [
                    "TR_PID_DETAIL_HEADER_ID" => $request->TR_PID_HEADER_ID
                ]
            ]);

            std_insert([
                "table_name" => "TR_PID_DETAIL_MATERIAL",
                "data" => $pid_insert
            ]);
        }
        else{
            return response()->json([
                'message' => "Error on Insert PID Material"
            ],500);
        }

        if ($pid_insert != NULL) {
            $sum_pid_insert = array();
            foreach($pid_insert as $data) {
                if(!array_key_exists($data['TR_PID_DETAIL_DETAIL_ID'], $sum_pid_insert))
                    $sum_pid_insert[$data['TR_PID_DETAIL_DETAIL_ID']] = 0;
                $sum_pid_insert[$data['TR_PID_DETAIL_DETAIL_ID']] += $data['TR_PID_DETAIL_UPDATED_QTY'];
            }

            foreach ($sum_pid_insert as $key => $value) {
                std_update([
                    "table_name" => "TR_PID_DETAIL",
                    "where" => ["TR_PID_DETAIL_ID" => $key],
                    "data" => [
                        "TR_PID_DETAIL_MATERIAL_MOBILE_QTY" => $value
                    ]
                ]);
            }
        }

        std_update([
            "table_name" => "TR_PID_HEADER",
            "where" => ["TR_PID_HEADER_ID" => $detail_data[0]["TR_PID_HEADER_ID"]],
            "data" => [
                "TR_PID_HEADER_PHOTO" => NULL,
                "TR_PID_MOBILE_ALLOW_TO_INPUT" => false,
                "TR_PID_HEADER_APPROVAL_STATUS" => "PENDING",
                "TR_PID_HEADER_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TR_PID_HEADER_UPDATED_BY" => session("id"),
                "TR_PID_POSTING_DATE" => convert_to_y_m_d($request->TR_PID_POSTING_DATE),
                "TR_PID_COUNT_DATE" => date("Y-m-d")
            ]
        ]);

        return response()->json([
            "status" => "OK",
            "data" => "Stock Opname Successfully Submitted"
        ],200);
    }
}

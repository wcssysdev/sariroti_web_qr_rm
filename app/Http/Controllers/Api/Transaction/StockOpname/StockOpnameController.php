<?php

namespace App\Http\Controllers\Api\Transaction\StockOpname;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function view(Request $request)
    {
        $conditions = [
            [
                "field_name" => "TR_PID_HEADER_STATUS",
                "operator" => "!=",
                "value" => "D"
            ],
            [
                "field_name" => "TR_PID_HEADER_STATUS",
                "operator" => "!=",
                "value" => "E"
            ],
            [
                "field_name" => "TR_PID_HEADER_PLANT",
                "operator" => "=",
                "value" => $request->user_data->plant
            ],
            [
                "field_name" => "TR_PID_HEADER_IS_DELETED",
                "operator" => "=",
                "value" => false
            ],
            [
                "field_name" => "TR_PID_MOBILE_ALLOW_TO_INPUT",
                "operator" => "=",
                "value" => true
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-")."01";
        }
        if (isset($request->start_date) && $request->start_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PID_HEADER_SAP_CREATED_DATE",
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
                    "field_name" => "TR_PID_HEADER_SAP_CREATED_DATE",
                    "operator" => "<=",
                    "value" => $request->end_date
                ]
            ]);
        }

        $data = std_get([
            "select" => "TR_PID_HEADER.*",
            "table_name" => "TR_PID_HEADER",
            "where" => $conditions
        ]);

        return response()->json([
            "status" => "OK",
            "data" => $data
        ],200);
    }

    public function view_detail(Request $request)
    {
        $header = std_get([
            "select" => "TR_PID_HEADER.*",
            "table_name" => "TR_PID_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PID_HEADER_SAP_NO",
                    "operator" => "=",
                    "value" => $request->pid_header_number
                ]
            ],
            "first_row" => true
        ]);

        $detail = std_get([
            "select" => ["*"],
            "table_name" => "TR_PID_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PID_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->pid_header_number
                ]
            ],
            "first_row" => false
        ]);

        return response()->json([
            "status" => "OK",
            "data" => [
                "header_data" => $header,
                "detail_data" => $detail
            ]
        ],200);
    }

    public function view_material_detail(Request $request)
    {
        $detail = std_get([
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

        if ($detail == NULL) {
            return response()->json([
                "status" => "OK",
                "data" => []
            ],200);
        }

        $conditions = [
            [
                "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                "operator" => "=",
                "value" => $detail["TR_PID_DETAIL_MATERIAL_CODE"]
            ],
            [
                "field_name" => "TR_GR_DETAIL_UNLOADING_PLANT",
                "operator" => "=",
                "value" => $request->user_data->plant
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
        if ($detail["TR_PID_DETAIL_MATERIAL_SAP_BATCH"] != NULL && $detail["TR_PID_DETAIL_MATERIAL_SAP_BATCH"] != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_GR_DETAIL_SAP_BATCH",
                    "operator" => "=",
                    "value" => $detail["TR_PID_DETAIL_MATERIAL_SAP_BATCH"]
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

        return response()->json([
            "status" => "OK",
            "data" => $gr_data
        ],200);
    }

    public function save_validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "detail.*.TR_PID_HEADER_ID" => "required|max:255",
            "detail.*.TR_GR_DETAIL_ID" => "required|max:255",
            "detail.*.TR_PID_DETAIL_ID" => "required|max:255",
            "detail.*.qty" => "required|max:255",
            "TR_PID_POSTING_DATE" => "required|max:255"
        ]);

        $attributeNames = [
            "detail.*.TR_PID_HEADER_ID" => "PID Header ID",
            "detail.*.TR_GR_DETAIL_ID" => "GR Detail ID",
            "detail.*.TR_PID_DETAIL_ID" => "PID Detail ID",
            "detail.*.qty" => "Qty",
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

        $pid_header_id = NULL;
        $pid_insert = [];
        for ($i=0; $i < count($detail_data); $i++) { 
            $filename = null;
            if (isset($detail_data["materialPhoto"]) && $detail_data["materialPhoto"] != NULL && $detail_data["materialPhoto"] != "") {
                $upload_dir = public_path()."/storage/PID_images/";
                $img = $row["materialPhoto"];
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $image_data = base64_decode($img);
                $unique_id = uniqid();
                $file = $upload_dir.$unique_id.'.jpeg';
                $success = file_put_contents($file, $image_data);
                $filename = $unique_id.'.jpeg';
            }

            $pid_insert = array_merge($pid_insert, [
                [
                    "TR_PID_DETAIL_PHOTO" => $filename,
                    "TR_PID_DETAIL_DETAIL_ID" => $detail_data[$i]["TR_PID_DETAIL_ID"],
                    "TR_PID_DETAIL_HEADER_ID" => $detail_data[$i]["TR_PID_HEADER_ID"],
                    "TR_PID_DETAIL_GR_DETAIL_ID" => $detail_data[$i]["TR_GR_DETAIL_ID"],
                    "TR_PID_DETAIL_LEFT_QTY" => $detail_data[$i]["TR_GR_DETAIL_LEFT_QTY"],
                    "TR_PID_DETAIL_UPDATED_QTY" => $detail_data[$i]["qty"]
                ]
            ]);
            $pid_header_id = $detail_data[$i]["TR_PID_HEADER_ID"];
        }

        if ($pid_insert != []) {
            std_delete([
                "table_name" => "TR_PID_DETAIL_MATERIAL",
                "where" => [
                    "TR_PID_DETAIL_HEADER_ID" => $pid_header_id
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

        // for ($i=0; $i < count($detail_data); $i++) { 
        //     if ($detail_data[$i]["qty"] != $detail_data[$i]["TR_GR_DETAIL_LEFT_QTY"]) {
        //         std_update([
        //             "table_name" => "TR_GR_DETAIL",
        //             "where" => ["TR_GR_DETAIL_ID" => $detail_data[$i]["TR_GR_DETAIL_ID"]],
        //             "data" => [
        //                 "TR_GR_DETAIL_LEFT_QTY" => $detail_data[$i]["qty"]
        //             ]
        //         ]);
        //     }
        // }

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

        $filename_header = null;

        if (isset($request->PID_PHOTO) && $request->PID_PHOTO != NULL && $request->PID_PHOTO != "") {
            $upload_dir = public_path()."/storage/PID_images/";
            $img = $request->PID_PHOTO;
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $image_data = base64_decode($img);
            $unique_id = uniqid();
            $file = $upload_dir.$unique_id.'.jpeg';
            $success = file_put_contents($file, $image_data);
            $filename_header = $unique_id.'.jpeg';
        }

        std_update([
            "table_name" => "TR_PID_HEADER",
            "where" => ["TR_PID_HEADER_ID" => $detail_data[0]["TR_PID_HEADER_ID"]],
            "data" => [
                "TR_PID_HEADER_PHOTO" => $filename_header,
                "TR_PID_MOBILE_ALLOW_TO_INPUT" => false,
                "TR_PID_HEADER_APPROVAL_STATUS" => "PENDING",
                "TR_PID_HEADER_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TR_PID_HEADER_UPDATED_BY" => $request->user_data->user_id,
                "TR_PID_POSTING_DATE" => $request->TR_PID_POSTING_DATE,
                "TR_PID_COUNT_DATE" => date("Y-m-d")
            ]
        ]);

        return response()->json([
            "status" => "OK",
            "data" => "Stock Opname Successfully Created"
        ],200);
    }

    public function history_header(Request $request)
    {
        $plant_code = $request->user_data->plant;
        $conditions = [
            [
                "field_name" => "TR_PID_HEADER_UPDATED_BY",
                "operator" => "=",
                "value" => $request->user_data->user_id
            ],
            [
                "field_name" => "TR_PID_MOBILE_ALLOW_TO_INPUT",
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
                    "field_name" => "TR_PID_HEADER_CREATED_TIMESTAMP",
                    "operator" => ">=",
                    "value" => $request->start_date." 00:00:00"
                ]
            ]);
        }

        if (!isset($request->end_date) || $request->end_date == "") {
            $request->end_date = date("Y-m-d");
        }
        if (isset($request->end_date) && $request->end_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_PID_HEADER_CREATED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => $request->end_date." 23:59:59"
                ]
            ]);
        }

        $gr_data = std_get([
            "select" => "TR_PID_HEADER.*",
            "table_name" => "TR_PID_HEADER",
            "where" => $conditions,
            "distinct" => true
        ]);

        return response()->json([
            "status" => "OK",
            "data" => $gr_data
        ],200);
    }

    public function history_detail(Request $request)
    {
        $header_data = std_get([
            "select" => "TR_PID_HEADER.*",
            "table_name" => "TR_PID_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PID_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_PID_HEADER_ID
                ]
            ],
            "first_row" => true
        ]);

        if (isset($header_data["TR_PID_HEADER_PHOTO"]) && $header_data["TR_PID_HEADER_PHOTO"] != NULL && $header_data["TR_PID_HEADER_PHOTO"] != "") {
            $header_data["TR_PID_HEADER_PHOTO"] = asset('storage/PID_images/')."/".$header_data["TR_PID_HEADER_PHOTO"];
        }

        $detail_data = std_get([
            "select" => "TR_PID_DETAIL.*",
            "table_name" => "TR_PID_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PID_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $header_data["TR_PID_HEADER_SAP_NO"]
                ]
            ]
        ]);

        return response()->json([
            "status" => "OK",
            "data" => [
                "header" => $header_data,
                "detail" => $detail_data,
            ]
        ],200);
    }

    public function history_detail_material(Request $request)
    {
        $detail_data = std_get([
            "select" => "TR_PID_DETAIL.*",
            "table_name" => "TR_PID_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PID_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->TR_PID_DETAIL_ID
                ]
            ],
            "first_row" => true
        ]);

        $detail_material_data = std_get([
            "select" => ["TR_PID_DETAIL_MATERIAL.*","TR_GR_DETAIL.*"],
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
                    "value" => $request->TR_PID_DETAIL_ID
                ]
            ],
            "first_row" => false
        ]);

        for ($i=0; $i < count($detail_material_data); $i++) { 
            if (isset($detail_material_data[$i]["TR_PID_DETAIL_PHOTO"]) && $detail_material_data[$i]["TR_PID_DETAIL_PHOTO"] != NULL && $detail_material_data[$i]["TR_PID_DETAIL_PHOTO"] != "") {
                $detail_material_data[$i]["TR_PID_DETAIL_PHOTO"] = asset('storage/PID_images/')."/".$detail_material_data[$i]["TR_PID_DETAIL_PHOTO"];
            }
        }

        return response()->json([
            "status" => "OK",
            "data" => [
                "detail" => $detail_data,
                "detail_material" => $detail_material_data,
            ]
        ],200);
    }
}
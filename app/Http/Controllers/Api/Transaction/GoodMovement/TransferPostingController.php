<?php

namespace App\Http\Controllers\Api\Transaction\GoodMovement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TransferPostingController extends Controller
{
    public function tp_view(Request $request)
    {
        $conditions = [
            [
                "field_name" => "TR_TP_HEADER_MOBILE_IS_SUBMIT",
                "operator" => "=",
                "value" => false
            ],
            [
                "field_name" => "TR_TP_HEADER_MVT_CODE",
                "operator" => "!=",
                "value" => "Y21"
            ],
            [
                "field_name" => "TR_TP_HEADER_MVT_CODE",
                "operator" => "!=",
                "value" => "511"
            ],
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
                "value" => $request->user_data->plant
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-")."01";
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

        $data = std_get([
            "select" => "TR_TP_HEADER.*",
            "table_name" => "TR_TP_HEADER",
            "where" => $conditions
        ]);

        return response()->json([
            "status" => "OK",
            "data" => $data
        ],200);
    }

    public function tp_detail(Request $request)
    {
        $tp_header_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_HEADER",
            "where" => [
                [
                    "field_name" => "TR_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_TP_HEADER_ID
                ]
            ],
            "first_row" => true
        ]);

        $tp_detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_TP_HEADER_ID
                ]
            ]
        ]);

        return response()->json([
            "status" => "OK",
            "data" => [
                "tp_header_data" => $tp_header_data,
                "tp_detail_data" => $tp_detail_data
            ]
        ],200);
    }

    public function scan_qr(Request $request)
    {
        $tp_detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_QR_CODE_NUMBER",
                    "operator" => "=",
                    "value" => $request->TR_TP_DETAIL_QR_CODE_NUMBER
                ],
                [
                    "field_name" => "TR_TP_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->TR_TP_DETAIL_ID
                ]
            ],
            "first_row" => true
        ]);

        if ($tp_detail_data == NUlL) {
            $tp_detail_data = [];
        }

        return response()->json([
            "status" => "OK",
            "data" => [
                "tp_detail_data" => $tp_detail_data
            ]
        ],200);
    }

    public function save_validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "TR_TP_HEADER_ID" => "required|max:255",
            "TR_TP_HEADER_PSTG_DATE" => "required|max:255",
            "TR_TP_HEADER_TXT" => "required|max:255",
            "TR_TP_HEADER_BOL" => "required|max:255",
        ]);

        $attributeNames = [
            "TR_TP_HEADER_ID" => "TR_TP_HEADER_ID",
            "TR_TP_HEADER_PSTG_DATE" => "TR_TP_HEADER_PSTG_DATE",
            "TR_TP_HEADER_TXT" => "TR_TP_HEADER_TXT",
            "TR_TP_HEADER_BOL" => "TR_TP_HEADER_BOL"
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
        if ($request->tp_materials == NULL) {
            return response()->json([
                'message' => "TP Material Data Not Exist / Empty"
            ],500);
        }

        $tp_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_HEADER",
            "where" => [
                [
                    "field_name" => "TR_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_TP_HEADER_ID,
                ]
            ],
            "first_row" => true
        ]);

        if ($tp_header == NULL) {
            return response()->json([
                'message' => "TP Not Exist"
            ],400);
        }

        if ($tp_header["TR_TP_HEADER_MOBILE_IS_SUBMIT"] == true) {
            return response()->json([
                'message' => "Data already Submitted, Transaction Cancelled"
            ],400);
        }

        $tp_material_arr = [];
        foreach ($request->tp_materials as $row) {
            $tp_detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_TP_DETAIL",
                "where" => [
                    [
                        "field_name" => "TR_TP_DETAIL_ID",
                        "operator" => "=",
                        "value" => $row["TR_TP_DETAIL_ID"]
                    ]
                ],
                "first_row" => true
            ]);
            if ($row["TR_TP_DETAIL_MOBILE_QTY"] > $tp_detail["TR_TP_DETAIL_BASE_QTY"]) {
                return response()->json([
                    "status" => "ERR",
                    "data" => "Mobile QTY must be < than TP Detail"
                ],500);
            }
        }

        foreach ($request->tp_materials as $row) {
            $filename = null;
            if (isset($row["materialPhoto"]) && $row["materialPhoto"] != NULL && $row["materialPhoto"] != "") {
                $upload_dir = public_path()."/storage/TP_images/";
                $img = $row["materialPhoto"];
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $image_data = base64_decode($img);
                $unique_id = uniqid();
                $file = $upload_dir.$unique_id.'.jpeg';
                $success = file_put_contents($file, $image_data);
                $filename = $unique_id.'.jpeg';
            }

            std_update([
                "table_name" => "TR_TP_DETAIL",
                "where" => ["TR_TP_DETAIL_ID" => $row["TR_TP_DETAIL_ID"]],
                "data" => [
                    "TR_TP_DETAIL_PHOTO" => $filename,
                    "TR_TP_DETAIL_MOBILE_QTY" => $row["TR_TP_DETAIL_MOBILE_QTY"],
                    "TR_TP_DETAIL_MOBILE_UOM" => $row["TR_TP_DETAIL_MOBILE_UOM"],
                    "TR_TP_DETAIL_NOTES" => $row["TR_TP_DETAIL_NOTES"],
                    "TR_TP_DETAIL_UPDATED_BY" => $request->user_data->user_id,
                    "TR_TP_DETAIL_UPDATED_TIMESTAMP" => $timestamp
                ]
            ]);
        }

        $filename_header = null;
        if (isset($request->TP_PHOTO) && $request->TP_PHOTO != NULL && $request->TP_PHOTO != "") {
            $upload_dir = public_path()."/storage/TP_images/";
            $img = $request->TP_PHOTO;
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $image_data = base64_decode($img);
            $unique_id = uniqid();
            $file = $upload_dir.$unique_id.'.jpeg';
            $success = file_put_contents($file, $image_data);
            $filename_header = $unique_id.'.jpeg';
        }

        std_update([
            "table_name" => "TR_TP_HEADER",
            "where" => ["TR_TP_HEADER_ID" => $request->TR_TP_HEADER_ID],
            "data" => [
                "TR_TP_HEADER_PHOTO" => $filename_header,
                "TR_TP_HEADER_BOL" => $request->TR_TP_HEADER_BOL,
                "TR_TP_HEADER_TXT" => $request->TR_TP_HEADER_TXT,
                "TR_TP_HEADER_PSTG_DATE" => $request->TR_TP_HEADER_PSTG_DATE,
                "TR_TP_HEADER_MOBILE_IS_SUBMIT" => true,
                "TR_TP_HEADER_UPDATED_BY" => $request->user_data->user_id,
                "TR_TP_HEADER_UPDATED_TIMESTAMP" => $timestamp
            ]
        ]);

        foreach ($request->tp_materials as $row) {
            $tp_detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_TP_DETAIL",
                "where" => [
                    [
                        "field_name" => "TR_TP_DETAIL_ID",
                        "operator" => "=",
                        "value" => $row["TR_TP_DETAIL_ID"]
                    ]
                ],
                "first_row" => true
            ]);
            if ($tp_header["TR_TP_HEADER_MVT_CODE"] != "Y21") {
                $gr_detail_new_gr = std_get([
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
                            "field_name" => "TR_GR_DETAIL_ID",
                            "operator" => "=",
                            "value" => $tp_detail["TR_TP_DETAIL_GR_DETAIL_ID"]
                        ]
                    ],
                    "first_row" => true
                ]);

                if ($row["TR_TP_DETAIL_MOBILE_QTY"] < $tp_detail["TR_TP_DETAIL_BASE_QTY"]) {
                    $difference = $tp_detail["TR_TP_DETAIL_BASE_QTY"] - $row["TR_TP_DETAIL_MOBILE_QTY"];

                    std_update([
                        "table_name" => "TR_GR_DETAIL",
                        "where" => ["TR_GR_DETAIL_ID" => $tp_detail["TR_TP_DETAIL_GR_DETAIL_ID"]],
                        "data" => [
                            "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" + '.$difference)
                        ]
                    ]);
                }
                
                insert_material_log([
                    "material_code" => $gr_detail_new_gr["TR_GR_DETAIL_MATERIAL_CODE"],
                    "plant_code" => $gr_detail_new_gr["TR_GR_DETAIL_UNLOADING_PLANT"],
                    "posting_date" => $request->TR_TP_HEADER_PSTG_DATE,
                    "movement_type" => $gr_detail_new_gr["TR_GR_HEADER_MVT_CODE"],
                    "gr_detail_id" => $gr_detail_new_gr["TR_GR_DETAIL_ID"],
                    "base_qty" => -$row["TR_TP_DETAIL_MOBILE_QTY"],
                    "base_uom" => $row["TR_TP_DETAIL_MOBILE_UOM"],
                    "created_by" => $request->user_data->user_id
                ]);

                if ($tp_detail["TR_TP_DETAIL_SLOC"] == "1419" || $tp_detail["TR_TP_DETAIL_SLOC"] == "1900") {
                    $gr_detail = $gr_detail_new_gr;

                    $gr_id = std_insert_get_id([
                        "table_name" => "TR_GR_HEADER",
                        "data" => [
                            "TR_GR_HEADER_PO_NUMBER" => NULL,
                            "TR_GR_HEADER_PLANT_CODE" => $gr_detail_new_gr["TR_GR_DETAIL_UNLOADING_PLANT"],
                            "TR_GR_HEADER_SAP_DOC" => "NO SAP",
                            "TR_GR_HEADER_PSTG_DATE" => $request->TR_TP_HEADER_PSTG_DATE,
                            "TR_GR_HEADER_DOC_DATE" => date("Y-m-d"),
                            "TR_GR_HEADER_BOL" => NULL,
                            "TR_GR_HEADER_TXT" => NULL,
                            "TR_GR_HEADER_MVT_CODE" => $tp_header["TR_TP_HEADER_MVT_CODE"],
                            "TR_GR_HEADER_SAP_YEAR" => NULL,
                            "TR_GR_HEADER_STATUS" => "SUCCESS",
                            "TR_GR_HEADER_ERROR" => NULL,
                            "TR_GR_HEADER_PHOTO" => NULL,
                            "TR_GR_HEADER_RECIPIENT" => "SYSTEM",
                            "TR_GR_HEADER_CREATED_BY" => "SYSTEM",
                            "TR_GR_HEADER_CREATED_TIMESTAMP" => $timestamp,
                            "TR_GR_HEADER_UPDATED_BY" => NULL,
                            "TR_GR_HEADER_UPDATED_TIMESTAMP" => NULL,
                            "TR_GR_HEADER_IS_ADJUSTMENT" => false,
                            "TR_GR_HEADER_PRINT_COUNT" => 0
                        ]
                    ]);

                    std_insert([
                        "table_name" => "TR_GR_DETAIL",
                        "data" => [
                            "TR_GR_DETAIL_HEADER_ID" => $gr_id,
                            "TR_GR_DETAIL_MATERIAL_CODE" => $gr_detail["TR_GR_DETAIL_MATERIAL_CODE"],
                            "TR_GR_DETAIL_MATERIAL_NAME" => $gr_detail["TR_GR_DETAIL_MATERIAL_NAME"],
                            "TR_GR_DETAIL_SAP_BATCH" => $gr_detail["TR_GR_DETAIL_SAP_BATCH"],
                            "TR_GR_DETAIL_QTY" => NULL,
                            "TR_GR_DETAIL_UOM" => NULL,
                            "TR_GR_DETAIL_BASE_QTY" => $row["TR_TP_DETAIL_MOBILE_QTY"],
                            "TR_GR_DETAIL_BASE_UOM" => $row["TR_TP_DETAIL_MOBILE_UOM"],
                            "TR_GR_DETAIL_LEFT_QTY" => $row["TR_TP_DETAIL_MOBILE_QTY"],
                            "TR_GR_DETAIL_UNLOADING_PLANT" => $gr_detail["TR_GR_DETAIL_UNLOADING_PLANT"],
                            "TR_GR_DETAIL_GL_ACCOUNT" => $gr_detail["TR_GR_DETAIL_GL_ACCOUNT"],
                            "TR_GR_DETAIL_COST_CENTER" => $gr_detail["TR_GR_DETAIL_COST_CENTER"],
                            "TR_GR_DETAIL_EXP_DATE" => $gr_detail["TR_GR_DETAIL_EXP_DATE"],
                            "TR_GR_DETAIL_IMG_QRCODE" => NULL,
                            "TR_GR_DETAIL_NOTES" => NULL,
                            "TR_GR_DETAIL_PHOTO" => NULL,
                            "TR_GR_DETAIL_CREATED_BY" => $request->user_data->user_id,
                            "TR_GR_DETAIL_CREATED_TIMESTAMP" => $timestamp,
                            "TR_GR_DETAIL_UPDATED_BY" => NULL,
                            "TR_GR_DETAIL_UPDATED_TIMESTAMP" => NULL,
                            "TR_GR_DETAIL_QR_CODE_NUMBER" => get_gr_detail_qr($tp_detail["TR_TP_DETAIL_GR_DETAIL_ID"]),
                            "TR_GR_DETAIL_SLOC" => $tp_detail["TR_TP_DETAIL_SLOC"],
                            "TR_GR_DETAIL_PO_DETAIL_ID" => $gr_detail["TR_GR_DETAIL_PO_DETAIL_ID"],
                            "TR_GR_DETAIL_GR_REFERENCE" => $tp_detail["TR_TP_DETAIL_GR_DETAIL_ID"]
                        ]
                    ]);
                    insert_material_log([
                        "material_code" => $gr_detail["TR_GR_DETAIL_MATERIAL_CODE"],
                        "plant_code" => $request->user_data->plant,
                        "posting_date" => $request->TR_TP_HEADER_PSTG_DATE,
                        "movement_type" => $gr_detail["TR_GR_HEADER_MVT_CODE"],
                        "gr_detail_id" => $tp_detail["TR_TP_DETAIL_GR_DETAIL_ID"],
                        "base_qty" => $row["TR_TP_DETAIL_MOBILE_QTY"],
                        "base_uom" => $row["TR_TP_DETAIL_MOBILE_UOM"],
                        "created_by" => $request->user_data->user_id
                    ]);
                }
            }
        }

        generate_tp_csv($request->TR_TP_HEADER_ID, $request->user_data->plant);

        return response()->json([
            "status" => "OK",
            "data" => "TP Successfully Created"
        ],200);
    }

    public function history_header(Request $request)
    {
        $conditions = [
            [
                "field_name" => "TR_TP_HEADER_UPDATED_BY",
                "operator" => "=",
                "value" => $request->user_data->user_id
            ],
            [
                "field_name" => "TR_TP_HEADER_MOBILE_IS_SUBMIT",
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
                    "field_name" => "TR_TP_HEADER_CREATED_TIMESTAMP",
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
                    "field_name" => "TR_TP_HEADER_CREATED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => $request->end_date." 23:59:59"
                ]
            ]);
        }

        $gr_data = std_get([
            "select" => "TR_TP_HEADER.*",
            "table_name" => "TR_TP_HEADER",
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
            "select" => "TR_TP_HEADER.*",
            "table_name" => "TR_TP_HEADER",
            "where" => [
                [
                    "field_name" => "TR_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_TP_HEADER_ID
                ]
            ],
            "first_row" => true
        ]);

        if (isset($header_data["TR_TP_HEADER_PHOTO"]) && $header_data["TR_TP_HEADER_PHOTO"] != NULL && $header_data["TR_TP_HEADER_PHOTO"] != "") {
            $header_data["TR_TP_HEADER_PHOTO"] = asset('storage/TP_images/')."/".$header_data["TR_TP_HEADER_PHOTO"];
        }

        $detail_data = std_get([
            "select" => "TR_TP_DETAIL.*",
            "table_name" => "TR_TP_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_TP_HEADER_ID
                ]
            ]
        ]);

        for ($i=0; $i < count($detail_data); $i++) { 
            if (isset($detail_data[$i]["TR_TP_DETAIL_PHOTO"]) && $detail_data[$i]["TR_TP_DETAIL_PHOTO"] != NULL && $detail_data[$i]["TR_TP_DETAIL_PHOTO"] != "") {
                $detail_data[$i]["TR_TP_DETAIL_PHOTO"] = asset('storage/TP_images/')."/".$detail_data[$i]["TR_TP_DETAIL_PHOTO"];
            }
        }

        return response()->json([
            "status" => "OK",
            "data" => [
                "header" => $header_data,
                "detail" => $detail_data,
            ]
        ],200);
    }
}
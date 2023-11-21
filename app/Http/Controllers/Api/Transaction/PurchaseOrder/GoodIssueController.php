<?php

namespace App\Http\Controllers\Api\Transaction\PurchaseOrder;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class GoodIssueController extends Controller {

    public function po_view(Request $request) {
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
                "value" => $request->user_data->plant
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-") . "01";
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

        $plant_data = get_master_data("MA_PLANT");
        $vendor_data = get_master_data("MA_VENDOR");

        $po_gi_non_zret = std_get([
            "select" => "TR_PO_HEADER.*",
            "table_name" => "TR_PO_HEADER",
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
                "value" => $request->user_data->plant
            ],
            [
                "field_name" => "TR_PO_HEADER_IS_DELETED",
                "operator" => "=",
                "value" => false
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-") . "01";
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
            "select" => ["TR_PO_HEADER.*", "MA_VENDOR.MA_VENDOR_NAME"],
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
            ],
            "where" => $conditions,
            "distinct" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => array_merge($po_gi_non_zret, $po_gi_data_zret)
                        ], 200);
    }

    public function po_header(Request $request) {
        $po_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_header_number
                ]
            ],
            "first_row" => true
        ]);
        return response()->json([
                    "status" => "OK",
                    "data" => $po_header
                        ], 200);
    }

    public function po_detail(Request $request) {
        if (isset($selects)) {
            $select_fields = $selects;
        } else {
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

        return response()->json([
                    "status" => "OK",
                    "data" => [
                        "header_data" => $data,
                        "detail_data" => $detail_data,
                        "gi_data" => $gi_data
                    ]
                        ], 200);
    }

    public function gi_detail(Request $request) {
        $gi_header_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPHEADER_ID
                ]
            ],
            "first_row" => true
        ]);

        $gi_detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPDETAIL",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPHEADER_ID
                ]
            ]
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => [
                        "gi_header_data" => $gi_header_data,
                        "gi_detail_data" => $gi_detail_data
                    ]
                        ], 200);
    }

    public function scan_qr_non_plan(Request $request) {
        try {

            if (empty($request->TR_GR_DETAIL_QR_CODE_NUMBER)) {
                return response()->json([
                            "status" => "Bad Request",
                            "data" => [
                                "gr_detail_data" => []
                            ]
                                ], 400);
            }


            $gr_detail_data = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_DETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GR_DETAIL_QR_CODE_NUMBER",
                        "operator" => "=",
                        "value" => $request->TR_GR_DETAIL_QR_CODE_NUMBER
                    ]
                ],
                "first_row" => true
            ]);

            if ($gr_detail_data) {
                return response()->json([
                            "status" => "OK",
                            "data" => [
                                "gr_detail_data" => $gr_detail_data
                            ]
                                ], 200);
            } else {
                return response()->json([
                            "status" => "Not Found",
                            "data" => [
                                "gr_detail_data" => []
                            ]
                                ], 404);
            }
        } catch (Exception $ex) {
            return response()->json([
                        "status" => "Internal server error.",
                        "data" => [
                            "gr_detail_data" => []
                        ]
                            ], 500);
        }
    }

    public function scan_qr(Request $request) {
        $gi_detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPDETAIL",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPDETAIL_QR_CODE_NUMBER",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPDETAIL_QR_CODE_NUMBER
                ],
                [
                    "field_name" => "TR_GI_SAPDETAIL_ID",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPDETAIL_ID
                ]
            ],
            "first_row" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => [
                        "gi_detail_data" => $gi_detail_data
                    ]
                        ], 200);
    }

    public function save_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "TR_GI_SAPHEADER_ID" => "required|max:255",
                    "TR_GI_SAPHEADER_PSTG_DATE" => "required|max:255",
                    "TR_GI_SAPHEADER_TXT" => "required|max:255",
                    "TR_GI_SAPHEADER_PSTG_DATE" => "required|max:255",
        ]);

        $attributeNames = [
            "TR_GI_SAPHEADER_ID" => "TR_GI_SAPHEADER_ID",
            "TR_GI_SAPHEADER_PSTG_DATE" => "TR_GI_SAPHEADER_PSTG_DATE",
            "TR_GI_SAPHEADER_TXT" => "TR_GI_SAPHEADER_TXT",
            "TR_GI_SAPHEADER_PSTG_DATE" => "TR_GI_SAPHEADER_PSTG_DATE"
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function submit_gi(Request $req_post) {
        /**
         * 2023 Nov
         * waluyosejati99@gmail.com
         * 
         * GI from mobile, adalah GI non plan
         * GI upload dari mobile adala new GI
         * 
         * PO Left Qty harus langsung dipotong
         */
        
        $validation_res = $this->save_validate_input($req_post);
        if ($validation_res !== true) {
            return response()->json([
                        'message' => $validation_res
                            ], 400);
        }

        $request = (object) $req_post->all();
//        dd($request);
        $timestamp = date("Y-m-d H:i:s");
        if ($request->TR_GI_MATERIALS == NULL) {
            return response()->json([
                        'message' => "GI Material Data Not Exist / Empty"
                            ], 500);
        }

        $gi_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPHEADER_ID,
                ]
            ],
            "first_row" => true
        ]);

        if ($gi_header && ($gi_header["TR_GI_SAPHEADER_MOBILE_IS_SUBMIT"] == true)) {
            return response()->json([
                        'message' => "Data already Submitted, Transaction Cancelled"
                            ], 400);
        }

        /**
         * Insert GI Header
         */
        $plant_code = $request->user_data->plant;
        $movement_code = "351"; //ZRAW
        if ($request->TR_PO_HEADER_TYPE == "ZSTO") {
            $movement_code = "351";
        } elseif ($request->TR_PO_HEADER_TYPE == "ZRET") {
            $movement_code = "101";
        }
        $filename_header = null;
        if (isset($request->GI_PHOTO) && $request->GI_PHOTO != NULL && $request->GI_PHOTO != "") {
            $upload_dir = public_path() . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "GI_images" . DIRECTORY_SEPARATOR;
            $img = $request->GI_PHOTO;
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $image_data = base64_decode($img);
//            dd($upload_dir);
            $unique_id = uniqid();
            $file = $upload_dir . $unique_id . '.jpeg';
            $success = file_put_contents($file, $image_data);
            $filename_header = $unique_id . '.jpeg';
        }
        $new_gi = [
            "table_name" => "TR_GI_SAPHEADER",
            "data" => [
                "TR_GI_SAPHEADER_PO_NUMBER" => $request->TR_PO_HEADER_NUMBER,
                "TR_GI_SAPHEADER_PLANT_CODE" => $plant_code,
                "TR_GI_SAPHEADER_CREATED_PLANT_CODE" => $plant_code,
                "TR_GI_SAPHEADER_SAP_DOC" => NULL,
                "TR_GI_SAPHEADER_PSTG_DATE" => $request->TR_GI_SAPHEADER_PSTG_DATE,
                "TR_GI_SAPHEADER_DOC_DATE" => date("Y-m-d"),
                "TR_GI_SAPHEADER_BOL" => $request->TR_GI_SAPHEADER_BOL,
                "TR_GI_SAPHEADER_TXT" => $request->TR_GI_SAPHEADER_TXT,
                "TR_GI_SAPHEADER_MVT_CODE" => $movement_code,
                "TR_GI_SAPHEADER_SAP_YEAR" => NULL,
                "TR_GI_SAPHEADER_STATUS" => "PENDING",
                "TR_GI_SAPHEADER_ERROR" => NULL,
                "TR_GI_SAPHEADER_PHOTO" => $filename_header,
                "TR_GI_SAPHEADER_MOBILE_IS_SUBMIT" => true,
                "TR_GI_SAPHEADER_CREATED_BY" => $request->user_data->user_id,
                "TR_GI_SAPHEADER_CREATED_TIMESTAMP" => $timestamp
            ]
        ];
        
        $gi_id = std_insert_get_id($new_gi);

        if ($gi_id == false) {
            return response()->json([
                        'message' => "Error on saving GI header"
                            ], 500);
        }

        $gi_material_arr = [];
        $count_sap_line_id = 1;

        foreach ($request->TR_GI_MATERIALS as $row) {


            $filename = null;
            if (isset($row["materialPhoto"]) && $row["materialPhoto"] != NULL && $row["materialPhoto"] != "") {
                $upload_dir = public_path() . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "GI_images" . DIRECTORY_SEPARATOR;
                $img = $row["materialPhoto"];
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $image_data = base64_decode($img);
                $unique_id = uniqid();
                $file = $upload_dir . $unique_id . '.jpeg';
                $success = file_put_contents($file, $image_data);
                $filename = $unique_id . '.jpeg';
            }


            $gi_material_arr = array_merge($gi_material_arr, [
                [
                    "TR_GI_SAPDETAIL_SAPHEADER_ID" => $gi_id,
                    "TR_GI_SAPDETAIL_MATERIAL_CODE" => $row["TR_GR_DETAIL_MATERIAL_CODE"],
                    "TR_GI_SAPDETAIL_MATERIAL_NAME" => $row["TR_GR_DETAIL_MATERIAL_NAME"],
                    "TR_GI_SAPDETAIL_SAP_BATCH" => $row["TR_GR_DETAIL_SAP_BATCH"],
                    "TR_GI_SAPDETAIL_GI_QTY" => $row["TR_GI_SAPDETAIL_MOBILE_QTY"],
                    "TR_GI_SAPDETAIL_GI_UOM" => $row["TR_GI_SAPDETAIL_MOBILE_UOM"],
                    "TR_GI_SAPDETAIL_MOBILE_QTY" => $row["TR_GI_SAPDETAIL_MOBILE_QTY"],
                    "TR_GI_SAPDETAIL_MOBILE_UOM" => $row["TR_GI_SAPDETAIL_MOBILE_UOM"],
                    "TR_GI_SAPDETAIL_BASE_QTY" => $row["TR_GR_DETAIL_BASE_QTY"],
                    "TR_GI_SAPDETAIL_BASE_UOM" => $row["TR_GR_DETAIL_BASE_UOM"],
                    "TR_GI_SAPDETAIL_SLOC" => $row["TR_GR_DETAIL_SLOC"],
                    "TR_GI_SAPDETAIL_QR_CODE_NUMBER" => $row["TR_GR_DETAIL_QR_CODE_NUMBER"],
                    "TR_GI_SAPDETAIL_NOTES" => $row["TR_GI_SAPDETAIL_NOTES"],
                    "TR_GI_SAPDETAIL_PHOTO" => NULL,
                    "TR_GI_SAPDETAIL_CREATED_BY" => $request->user_data->user_id,
                    "TR_GI_SAPDETAIL_CREATED_TIMESTAMP" => $timestamp,
                    "TR_GI_SAPDETAIL_UPDATED_BY" => NULL,
                    "TR_GI_SAPDETAIL_UPDATED_TIMESTAMP" => NULL,
                    "TR_GI_SAPDETAIL_GR_DETAIL_ID" => $row["TR_GR_DETAIL_ID"],
                    "TR_GI_SAPDETAIL_PO_DETAIL_ID" => $row["TR_GR_DETAIL_PO_DETAIL_ID"],
//                    "TR_GI_SAPDETAIL_SAPLINE_ID" => $row["TR_PO_DETAIL_MATERIAL_LINE_NUM"],
                    "TR_GI_SAPDETAIL_SAPLINE_ID" => $count_sap_line_id
                ]
            ]);
            $count_sap_line_id = $count_sap_line_id + 2;
        }

        $insert_res = std_insert([
            "table_name" => "TR_GI_SAPDETAIL",
            "data" => $gi_material_arr
        ]);

//        dd('ok save');
//
            $gi_detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPDETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                        "operator" => "=",
                        "value" => $gi_id
                    ]
                ],
            ]);
//            dd($gi_detail);
        foreach ($gi_detail as $row) {
            std_update([
                "table_name" => "TR_GR_DETAIL",
                "where" => ["TR_GR_DETAIL_ID" => $row["TR_GI_SAPDETAIL_GR_DETAIL_ID"]],
                "data" => [
                    "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" - ' . $row["TR_GI_SAPDETAIL_MOBILE_QTY"]),
                    "TR_GR_DETAIL_UPDATED_BY" => $request->user_data->user_id,
                    "TR_GR_DETAIL_UPDATED_TIMESTAMP" => $timestamp,                    
                ]
            ]);

            insert_material_log([
                "material_code" => $row["TR_GI_SAPDETAIL_MATERIAL_CODE"],
                "plant_code" => $request->user_data->plant,
                "posting_date" => $request->TR_GI_SAPHEADER_PSTG_DATE,
                "movement_type" => $movement_code,
                "gr_detail_id" => $row["TR_GI_SAPDETAIL_GR_DETAIL_ID"],
                "base_qty" => -$row["TR_GI_SAPDETAIL_MOBILE_QTY"],
                "base_uom" => $row["TR_GI_SAPDETAIL_BASE_UOM"],
                "created_by" => $request->user_data->user_id
            ]);
        }

        generate_gi_csv($gi_id, $request->user_data->plant);

        return response()->json([
                    "status" => "OK",
                    "data" => "GI Successfully Created"
                        ], 200);
    }

    public function submit(Request $request) {
        $validation_res = $this->save_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                        'message' => $validation_res
                            ], 400);
        }

        $timestamp = date("Y-m-d H:i:s");
        if ($request->gi_materials == NULL) {
            return response()->json([
                        'message' => "GI Material Data Not Exist / Empty"
                            ], 500);
        }

        $gi_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPHEADER_ID,
                ]
            ],
            "first_row" => true
        ]);

        if ($gi_header == NULL) {
            return response()->json([
                        'message' => "GI Not Exist"
                            ], 400);
        }

        if ($gi_header["TR_GI_SAPHEADER_MOBILE_IS_SUBMIT"] == true) {
            return response()->json([
                        'message' => "Data already Submitted, Transaction Cancelled"
                            ], 400);
        }

        $gi_material_arr = [];
        foreach ($request->gi_materials as $row) {

            $filename = null;
            if (isset($row["materialPhoto"]) && $row["materialPhoto"] != NULL && $row["materialPhoto"] != "") {
                $upload_dir = public_path() . "/storage/GI_images/";
                $img = $row["materialPhoto"];
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $image_data = base64_decode($img);
                $unique_id = uniqid();
                $file = $upload_dir . $unique_id . '.jpeg';
                $success = file_put_contents($file, $image_data);
                $filename = $unique_id . '.jpeg';
            }

            $gi_detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPDETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPDETAIL_ID",
                        "operator" => "=",
                        "value" => $row["TR_GI_SAPDETAIL_ID"]
                    ]
                ],
                "first_row" => true
            ]);
            if ($row["TR_GI_SAPDETAIL_MOBILE_QTY"] > $gi_detail["TR_GI_SAPDETAIL_GI_QTY"]) {
                return response()->json([
                            "status" => "ERR",
                            "data" => "Mobile QTY must be < than GI Detail"
                                ], 500);
            }
        }

        foreach ($request->gi_materials as $row) {
            std_update([
                "table_name" => "TR_GI_SAPDETAIL",
                "where" => ["TR_GI_SAPDETAIL_ID" => $row["TR_GI_SAPDETAIL_ID"]],
                "data" => [
                    "TR_GI_SAPDETAIL_PHOTO" => $filename,
                    "TR_GI_SAPDETAIL_MOBILE_QTY" => $row["TR_GI_SAPDETAIL_MOBILE_QTY"],
                    "TR_GI_SAPDETAIL_MOBILE_UOM" => $row["TR_GI_SAPDETAIL_MOBILE_UOM"],
                    "TR_GI_SAPDETAIL_NOTES" => $row["TR_GI_SAPDETAIL_NOTES"],
                    "TR_GI_SAPDETAIL_UPDATED_BY" => $request->user_data->user_id,
                    "TR_GI_SAPDETAIL_UPDATED_TIMESTAMP" => $timestamp
                ]
            ]);
        }

        $filename_header = null;
        if (isset($request->GI_PHOTO) && $request->GI_PHOTO != NULL && $request->GI_PHOTO != "") {
            $upload_dir = public_path() . "/storage/GI_images/";
            $img = $request->GI_PHOTO;
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $image_data = base64_decode($img);
            $unique_id = uniqid();
            $file = $upload_dir . $unique_id . '.jpeg';
            $success = file_put_contents($file, $image_data);
            $filename_header = $unique_id . '.jpeg';
        }

        std_update([
            "table_name" => "TR_GI_SAPHEADER",
            "where" => ["TR_GI_SAPHEADER_ID" => $request->TR_GI_SAPHEADER_ID],
            "data" => [
                "TR_GI_SAPHEADER_PHOTO" => $filename_header,
                "TR_GI_SAPHEADER_BOL" => $request->TR_GI_SAPHEADER_BOL,
                "TR_GI_SAPHEADER_TXT" => $request->TR_GI_SAPHEADER_TXT,
                "TR_GI_SAPHEADER_PSTG_DATE" => $request->TR_GI_SAPHEADER_PSTG_DATE,
                "TR_GI_SAPHEADER_MOBILE_IS_SUBMIT" => true,
                "TR_GI_SAPHEADER_UPDATED_BY" => $request->user_data->user_id,
                "TR_GI_SAPHEADER_UPDATED_TIMESTAMP" => $timestamp
            ]
        ]);

        foreach ($request->gi_materials as $row) {
            $gi_detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GI_SAPDETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPDETAIL_ID",
                        "operator" => "=",
                        "value" => $row["TR_GI_SAPDETAIL_ID"]
                    ]
                ],
                "first_row" => true
            ]);
            // if ($row["TR_GI_SAPDETAIL_MOBILE_QTY"] < $gi_detail["TR_GI_SAPDETAIL_GI_QTY"]) {
            // $difference = $tp_detail["TR_GI_SAPDETAIL_GI_QTY"] - $row["TR_GI_SAPDETAIL_MOBILE_QTY"];
            std_update([
                "table_name" => "TR_GR_DETAIL",
                "where" => ["TR_GR_DETAIL_ID" => $gi_detail["TR_GI_SAPDETAIL_GR_DETAIL_ID"]],
                "data" => [
                    "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" - ' . $row["TR_GI_SAPDETAIL_MOBILE_QTY"])
                ]
            ]);
            // }

            insert_material_log([
                "material_code" => $gi_detail["TR_GI_SAPDETAIL_MATERIAL_CODE"],
                "plant_code" => $request->user_data->plant,
                "posting_date" => $request->TR_GI_SAPHEADER_PSTG_DATE,
                "movement_type" => $gi_header["TR_GI_SAPHEADER_MVT_CODE"],
                "gr_detail_id" => $gi_detail["TR_GI_SAPDETAIL_GR_DETAIL_ID"],
                "base_qty" => -$row["TR_GI_SAPDETAIL_MOBILE_QTY"],
                "base_uom" => $gi_detail["TR_GI_SAPDETAIL_BASE_UOM"],
                "created_by" => $request->user_data->user_id
            ]);
        }

        generate_gi_csv($request->TR_GI_SAPHEADER_ID, $request->user_data->plant);

        return response()->json([
                    "status" => "OK",
                    "data" => "GI Successfully Created"
                        ], 200);
    }

    public function history_header_created_by(Request $request) {
        $plant_code = $request->user_data->plant;
        $conditions = [
            [
                "field_name" => "TR_GI_SAPHEADER_CREATED_BY",
                "operator" => "=",
                "value" => $request->user_data->user_id
            ],
            [
                "field_name" => "TR_GI_SAPHEADER_MOBILE_IS_SUBMIT",
                "operator" => "=",
                "value" => true
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-") . "01";
        }
        if (isset($request->start_date) && $request->start_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_GI_SAPHEADER_CREATED_TIMESTAMP",
                    "operator" => ">=",
                    "value" => $request->start_date . " 00:00:00"
                ]
            ]);
        }

        if (!isset($request->end_date) || $request->end_date == "") {
            $request->end_date = date("Y-m-d");
        }
        if (isset($request->end_date) && $request->end_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_GI_SAPHEADER_CREATED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => $request->end_date . " 23:59:59"
                ]
            ]);
        }

        $header_data = std_get([
            "select" => "TR_GI_SAPHEADER.*",
            "table_name" => "TR_GI_SAPHEADER",
            "where" => $conditions,
            "distinct" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => $header_data
                        ], 200);
    }
    public function history_header(Request $request) {
        $plant_code = $request->user_data->plant;
        $conditions = [
            [
                "field_name" => "TR_GI_SAPHEADER_UPDATED_BY",
                "operator" => "=",
                "value" => $request->user_data->user_id
            ],
            [
                "field_name" => "TR_GI_SAPHEADER_MOBILE_IS_SUBMIT",
                "operator" => "=",
                "value" => true
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-") . "01";
        }
        if (isset($request->start_date) && $request->start_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_GI_SAPHEADER_CREATED_TIMESTAMP",
                    "operator" => ">=",
                    "value" => $request->start_date . " 00:00:00"
                ]
            ]);
        }

        if (!isset($request->end_date) || $request->end_date == "") {
            $request->end_date = date("Y-m-d");
        }
        if (isset($request->end_date) && $request->end_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_GI_SAPHEADER_CREATED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => $request->end_date . " 23:59:59"
                ]
            ]);
        }

        $header_data = std_get([
            "select" => "TR_GI_SAPHEADER.*",
            "table_name" => "TR_GI_SAPHEADER",
            "where" => $conditions,
            "distinct" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => $header_data
                        ], 200);
    }

    public function history_detail(Request $request) {
        $header_data = std_get([
            "select" => "TR_GI_SAPHEADER.*",
            "table_name" => "TR_GI_SAPHEADER",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPHEADER_ID
                ]
            ],
            "first_row" => true
        ]);

        if (isset($header_data["TR_GI_SAPHEADER_PHOTO"]) && $header_data["TR_GI_SAPHEADER_PHOTO"] != NULL && $header_data["TR_GI_SAPHEADER_PHOTO"] != "") {
            $header_data["TR_GI_SAPHEADER_PHOTO"] = asset('storage/GI_images/') . "/" . $header_data["TR_GI_SAPHEADER_PHOTO"];
        }

        $detail_data = std_get([
            "select" => "TR_GI_SAPDETAIL.*",
            "table_name" => "TR_GI_SAPDETAIL",
            "where" => [
                [
                    "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GI_SAPHEADER_ID
                ]
            ]
        ]);

        for ($i = 0; $i < count($detail_data); $i++) {
            if (isset($detail_data[$i]["TR_GI_SAPDETAIL_PHOTO"]) && $detail_data[$i]["TR_GI_SAPDETAIL_PHOTO"] != NULL && $detail_data[$i]["TR_GI_SAPDETAIL_PHOTO"] != "") {
                $detail_data[$i]["TR_GI_SAPDETAIL_PHOTO"] = asset('storage/GI_images/') . "/" . $detail_data[$i]["TR_GI_SAPDETAIL_PHOTO"];
            }
        }

        return response()->json([
                    "status" => "OK",
                    "data" => [
                        "header" => $header_data,
                        "detail" => $detail_data,
                    ]
                        ], 200);
    }
}

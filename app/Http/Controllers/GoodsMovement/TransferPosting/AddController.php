<?php

namespace App\Http\Controllers\GoodsMovement\TransferPosting;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use DB;

function get_gr_data($material_code, $plant_code, $movement_code) {
    if ($movement_code == "551") {
        return get_tp_material([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                ],
                [
                    "join_type" => "left",
                    "table_name" => "TR_GR_DETAIL_LOCK",
                    "on1" => "TR_GR_DETAIL_LOCK.TR_GR_DETAIL_LOCK_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "operator" => "=",
                    "value" => $material_code
                ],
                [
                    "field_name" => "TR_GR_DETAIL_UNLOADING_PLANT",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "TR_GR_DETAIL_LEFT_QTY",
                    "operator" => ">",
                    "value" => 0
                ],
                [
                    "field_name" => "TR_GR_HEADER_IS_ADJUSTMENT",
                    "operator" => "=",
                    "value" => false
                ],
                [
                    "field_name" => "TR_GR_HEADER_SAP_DOC",
                    "operator" => "!=",
                    "value" => null
                ],
                [
                    "field_name" => "TR_GR_DETAIL_LOCK_ID",
                    "operator" => "=",
                    "value" => null
                ],
                [
                    "field_name" => "TR_GR_DETAIL_IS_CANCELLED",
                    "operator" => "=",
                    "value" => false
                ],
            ],
            "order_by" => [
                [
                    "field" => "TR_GR_HEADER_CREATED_TIMESTAMP",
                    "type" => "ASC",
                ]
            ],
            "first_row" => false
        ]);
    } else {
        return std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                ],
                [
                    "join_type" => "left",
                    "table_name" => "TR_GR_DETAIL_LOCK",
                    "on1" => "TR_GR_DETAIL_LOCK.TR_GR_DETAIL_LOCK_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "operator" => "=",
                    "value" => $material_code
                ],
                [
                    "field_name" => "TR_GR_DETAIL_UNLOADING_PLANT",
                    "operator" => "=",
                    "value" => $plant_code
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
                    "field_name" => "TR_GR_DETAIL_LOCK_ID",
                    "operator" => "=",
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
            ],
            "order_by" => [
                [
                    "field" => "TR_GR_HEADER_CREATED_TIMESTAMP",
                    "type" => "ASC",
                ]
            ],
            "first_row" => false
        ]);
    }
}

function get_lock_data() {
    return std_get([
        "select" => ["*"],
        "table_name" => "TR_GR_DETAIL_LOCK",
        "join" => [
            [
                "join_type" => "inner",
                "table_name" => "TR_GR_DETAIL",
                "on1" => "TR_GR_DETAIL_LOCK.TR_GR_DETAIL_LOCK_GR_DETAIL_ID",
                "operator" => "=",
                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
            ]
        ],
        "where" => [
            [
                "field_name" => "TR_GR_DETAIL_LOCK_CREATED_BY",
                "operator" => "=",
                "value" => session("id")
            ],
            [
                "field_name" => "TR_GR_IS_TP",
                "operator" => "=",
                "value" => true
            ]
        ],
        "first_row" => false
    ]);
}

class AddController extends Controller {

    public function index(Request $request) {
        $lock_data = get_lock_data();
        $cost_center = get_cost_center([
            "select" => ["MA_COSTCNTR_CODE", "MA_COSTCNTR_DESC"],
            "table_name" => "MA_COSTCNTR",
            "order_by" => [
                [
                    "field" => "MA_COSTCNTR_COSTCENTER",
                    "type" => "ASC",
                ]
            ],
            "first_row" => false
        ]);

        $gl_account = std_get([
            "select" => ["MA_GLACC_CODE", "MA_GLACC_DESC"],
            "table_name" => "MA_GLACC",
            "order_by" => [
                [
                    "field" => "MA_GLACC_CODE",
                    "type" => "ASC",
                ]
            ],
            "first_row" => false
        ]);

        $y21_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_TP_Y21_DETAIL_TEMP",
            "where" => [
                [
                    "field_name" => "TR_TP_Y21_DETAIL_TEMP_CREATED_BY",
                    "operator" => "=",
                    "value" => session("id")
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_TP_Y21_DETAIL_TEMP_ID",
                    "type" => "ASC",
                ]
            ],
            "first_row" => false
        ]);

        return view('transaction/goods_movement/transfer_posting/add', [
            "temp_material" => $lock_data,
            "y21_data" => $y21_data,
            "header_movement_code" => $request->header_movement_code,
            "header_cost_center" => $request->header_cost_center,
            "header_gl_account" => $request->header_gl_account,
            "header_posting_date" => $request->header_posting_date,
            "header_bill_of_landing" => $request->TR_TP_HEADER_BOL,
            "header_note" => $request->TR_TP_HEADER_TXT,
            "cost_center" => $cost_center,
            "gl_account" => $gl_account
        ]);
    }

    public function get_materials(Request $request) {
        $materials = std_get([
            "select" => ["MA_MATL_CODE as id", "MA_MATL_DESC as text"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ]
            ],
            "order_by" => [
                [
                    "field" => "MA_MATL_DESC",
                    "type" => "ASC",
                ]
            ],
            "distinct" => true
        ]);
        if ($materials != null) {
            foreach ($materials as $row) {
                $materials_adj[] = [
                    "id" => $row["id"],
                    "text" => $row["id"] . " - " . $row["text"]
                ];
            }
            return response()->json([
                        "status" => "OK",
                        "data" => $materials_adj,
                        "sloc_data" => get_sloc(session("plant"))
                            ], 200);
        } else {
            return response()->json([
                        "status" => "OK",
                        "data" => [],
                        "sloc_data" => get_sloc(session("plant"))
                            ], 200);
        }
    }

    public function get_materials_y21(Request $request) {
        $materials = std_get([
            "select" => ["MA_MATL_CODE as id", "MA_MATL_DESC as text"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ]
            ],
            "order_by" => [
                [
                    "field" => "MA_MATL_DESC",
                    "type" => "ASC",
                ]
            ],
            "distinct" => true
        ]);
        if ($materials != null) {
            foreach ($materials as $row) {
                $materials_adj[] = [
                    "id" => $row["id"],
                    "text" => $row["id"] . " - " . $row["text"]
                ];
            }
            return response()->json([
                        "status" => "OK",
                        "data" => $materials_adj,
                        "sloc_data" => get_sloc(session("plant"))
                            ], 200);
        } else {
            return response()->json([
                        "status" => "OK",
                        "data" => [],
                        "sloc_data" => get_sloc(session("plant"))
                            ], 200);
        }
    }

    public function get_material_batch_y21(Request $request) {
        $material = std_get([
            "select" => ["MA_MATL_BATCH as id", "MA_MATL_BATCH as text"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_CODE",
                    "operator" => "=",
                    "value" => $request->material_code
                ],
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ],
                [
                    "field_name" => "MA_MATL_BATCH",
                    "operator" => "!=",
                    "value" => ""
                ]
            ],
            "distinct" => true
        ]);

        $base_material = std_get([
            "select" => ["MA_MATL_UOM"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_CODE",
                    "operator" => "=",
                    "value" => $request->material_code
                ],
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ]
            ],
            "first_row" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => [
                        "batch" => $material,
                        "base_uom" => $base_material["MA_MATL_UOM"]
                    ]
                        ], 200);
    }

    public function get_material_gr(Request $request) {
        $gr_data = get_gr_data($request->material_code, session("plant"), $request->movement_type);
        $select2 = [];
        foreach ($gr_data as $row) {
            $select2 = array_merge($select2, [
                [
                    "id" => $row["TR_GR_DETAIL_ID"],
                    "text" => $row["TR_GR_DETAIL_ID"] . " | " . number_format($row["TR_GR_DETAIL_LEFT_QTY"]) . " " . $row["TR_GR_DETAIL_BASE_UOM"] . " | " . $row["TR_GR_DETAIL_SAP_BATCH"] . " | " . $row["TR_GR_DETAIL_EXP_DATE"]
                ]
            ]);
        }

        return response()->json([
                    "status" => "OK",
                    "data" => $select2
                        ], 200);
    }

    public function get_material_status(Request $request) {
        $gr_data = std_get([
            "select" => ["TR_GR_DETAIL.*"],
            "table_name" => "TR_GR_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->gr_detail_id
                ],
                [
                    "field_name" => "TR_GR_HEADER_IS_ADJUSTMENT",
                    "operator" => "=",
                    "value" => false
                ]
            ],
            "first_row" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => $gr_data
                        ], 200);
    }

    public function save_material_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "gr_detail_id" => "required|max:255",
                    "posting_qty" => "required|max:255|regex:/^\d+(\.\d{1,2})?$/",
                    "TR_TP_DETAIL_SLOC" => "required|max:255"
        ]);

        $attributeNames = [
            "gr_detail_id" => "GR DETAIL ID",
            "posting_qty" => "Posting QTY",
            "TR_TP_DETAIL_SLOC" => "SLOC"
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function save_material(Request $request) {
        $validation_res = $this->save_material_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                        'message' => $validation_res
                            ], 400);
        }
        $gr_data = std_get([
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

        std_insert([
            "table_name" => "TR_GR_DETAIL_LOCK",
            "data" => [
                "TR_GR_DETAIL_LOCK_GR_DETAIL_ID" => $request->gr_detail_id,
                "TR_GR_DETAIL_LOCK_PO_HEADER_ID" => NULL,
                "TR_GR_DETAIL_LOCK_BOOKED_QTY" => $request->posting_qty,
                "TR_GR_DETAIL_LOCK_BOOKED_UOM" => $gr_data["TR_GR_DETAIL_BASE_UOM"],
                "TR_GR_DETAIL_LOCK_BOOKED_SLOC" => $request->TR_TP_DETAIL_SLOC,
                "TR_GR_DETAIL_LOCK_EXPIRED_TIMESTAMP" => date("Y-m-d H:i:s", strtotime("+1 hours")),
                "TR_GR_DETAIL_LOCK_CREATED_BY" => session("id"),
                "TR_GR_DETAIL_LOCK_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TR_GR_IS_TP" => true
            ]
        ]);

        return redirect()->route("goods_movement_transfer_posting_add", [
                    'header_movement_code' => $request->TR_TP_HEADER_MVT_CODE,
                    'header_cost_center' => $request->TR_TP_COST_CENTER_CODE,
                    'header_gl_account' => $request->TR_TP_GL_ACCOUNT_CODE,
                    'header_posting_date' => $request->TR_TP_HEADER_PSTG_DATE
        ]);
    }

    public function save_material_y21_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "material_code_y21" => "required|max:255",
                    "batch_sap_y21" => "max:255",
                    "expired_date_y21" => "required|max:10",
                    "posting_qty_y21" => "required|regex:/^\d+(\.\d{1,2})?$/",
                    "from_sloc_y21" => "required|max:255",
                    "to_sloc_y21" => "required|max:255",
                    "note_y21" => "max:1000",
        ]);

        $attributeNames = [
            "material_code_y21" => "Material Code",
            "batch_sap_y21" => "SAP Batch",
            "expired_date_y21" => "Expired Date",
            "posting_qty_y21" => "Posting Qty",
            "from_sloc_y21" => "From SLOC",
            "to_sloc_y21" => "SLOC Destination",
            "note_y21" => "Note",
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function save_material_y21(Request $request) {
        $validation_res = $this->save_material_y21_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                        'message' => $validation_res
                            ], 400);
        }

        $material = std_get([
            "select" => ["*"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_CODE",
                    "operator" => "=",
                    "value" => $request->material_code_y21
                ],
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => session("plant")
                ]
            ],
            "first_row" => true
        ]);

        std_insert([
            "table_name" => "TR_TP_Y21_DETAIL_TEMP",
            "data" => [
                "TR_TP_Y21_DETAIL_TEMP_MATERIAL_CODE" => $request->material_code_y21,
                "TR_TP_Y21_DETAIL_TEMP_MATERIAL_NAME" => $material["MA_MATL_DESC"],
                "TR_TP_Y21_DETAIL_TEMP_SAP_BATCH" => $request->batch_sap_y21,
                "TR_TP_Y21_DETAIL_TEMP_BASE_QTY" => $request->posting_qty_y21,
                "TR_TP_Y21_DETAIL_TEMP_BASE_UOM" => $material["MA_MATL_UOM"],
                "TR_TP_Y21_DETAIL_TEMP_SLOC_FROM" => $request->from_sloc_y21,
                "TR_TP_Y21_DETAIL_TEMP_SLOC_TO" => $request->to_sloc_y21,
                "TR_TP_Y21_DETAIL_TEMP_EXP_DATE" => convert_to_y_m_d($request->expired_date_y21),
                "TR_TP_Y21_DETAIL_TEMP_NOTES" => $request->note_y21,
                "TR_TP_Y21_DETAIL_TEMP_CREATED_BY" => session("id"),
                "TR_TP_Y21_DETAIL_TEMP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
            ]
        ]);

        return redirect()->route("goods_movement_transfer_posting_add", [
                    'header_movement_code' => $request->TR_TP_HEADER_MVT_CODE,
                    'header_cost_center' => $request->TR_TP_COST_CENTER_CODE,
                    'header_gl_account' => $request->TR_TP_GL_ACCOUNT_CODE,
                    'header_posting_date' => $request->TR_TP_HEADER_PSTG_DATE
        ]);
    }

    public function delete_material(Request $request) {
        $delete_res = std_delete([
            "table_name" => "TR_GR_DETAIL_LOCK",
            "where" => [
                "TR_GR_DETAIL_LOCK_ID" => $request->uniqid
            ]
        ]);
        return redirect()->route("goods_movement_transfer_posting_add", [
                    'header_movement_code' => $request->TR_TP_HEADER_MVT_CODE,
                    'header_cost_center' => $request->TR_TP_COST_CENTER_CODE,
                    'header_gl_account' => $request->TR_TP_GL_ACCOUNT_CODE,
                    'header_posting_date' => $request->TR_TP_HEADER_PSTG_DATE
        ]);
    }

    public function delete_material_y21(Request $request) {
        $delete_res = std_delete([
            "table_name" => "TR_TP_Y21_DETAIL_TEMP",
            "where" => [
                "TR_TP_Y21_DETAIL_TEMP_ID" => $request->uniqid
            ]
        ]);
        return redirect()->route("goods_movement_transfer_posting_add", [
                    'header_movement_code' => $request->TR_TP_HEADER_MVT_CODE,
                    'header_cost_center' => $request->TR_TP_COST_CENTER_CODE,
                    'header_gl_account' => $request->TR_TP_GL_ACCOUNT_CODE,
                    'header_posting_date' => $request->TR_TP_HEADER_PSTG_DATE
        ]);
    }

    public function save_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "TR_TP_HEADER_MVT_CODE" => "required|in:311,Y21,551,411",
                    "TR_TP_COST_CENTER_CODE" => "max:255",
                    "TR_TP_GL_ACCOUNT_CODE" => "max:255",
                    "TR_TP_HEADER_PSTG_DATE" => "max:10",
                    "TR_TP_HEADER_TXT" => "required|max:1000"
        ]);

        $attributeNames = [
            "TR_TP_HEADER_MVT_CODE" => "Movement Code",
            "TR_TP_COST_CENTER_CODE" => "Cost Center Code",
            "TR_TP_GL_ACCOUNT_CODE" => "GL Account Code",
            "TR_TP_HEADER_PSTG_DATE" => "Posting Date",
            "TR_TP_HEADER_TXT" => "Note"
        ];

        $validate->setAttributeNames($attributeNames);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function save(Request $request) {
        $validation_res = $this->save_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                        'message' => $validation_res
                            ], 400);
        }

        $timestamp = date("Y-m-d H:i:s");

        if (in_array($request->TR_TP_HEADER_MVT_CODE,["551","Y21","311","411"])) {
            if ($request->TR_TP_HEADER_PSTG_DATE == NULL || $request->TR_TP_HEADER_PSTG_DATE == "") {
                return response()->json([
                            'message' => "Posting Date is Required"
                                ], 500);
            }
        }

        if ($request->TR_TP_HEADER_MVT_CODE == "Y21") {
            $posting_materials = std_get([
                "select" => ["*"],
                "table_name" => "TR_TP_Y21_DETAIL_TEMP",
                "where" => [
                    [
                        "field_name" => "TR_TP_Y21_DETAIL_TEMP_CREATED_BY",
                        "operator" => "=",
                        "value" => session("id")
                    ]
                ],
                "first_row" => false
            ]);
        } else {
            $posting_materials = get_lock_data();
        }

        if ($posting_materials == NULL) {
            return response()->json([
                        'message' => "Posting Material Data Not Exist / Empty"
                            ], 500);
        }

        if ($request->TR_TP_HEADER_MVT_CODE != "Y21") {
            $delete_status = false;
            foreach ($posting_materials as $row) {
                if ($row["TR_GR_DETAIL_LOCK_EXPIRED_TIMESTAMP"] < date("Y-m-d H:i:s")) {
                    std_delete([
                        "table_name" => "TR_GR_DETAIL_LOCK",
                        "where" => [
                            "TR_GR_DETAIL_LOCK_ID" => $row["TR_GR_DETAIL_LOCK_ID"]
                        ]
                    ]);
                    $delete_status = true;
                }
            }
            if ($delete_status === true) {
                return response()->json([
                            'message' => "GR Locked data is already expired, Posting Material Will Be Repopulated!"
                                ], 500);
            }
        }

        if ($request->TR_TP_HEADER_MVT_CODE == 551) {
            if ($request->TR_TP_COST_CENTER_CODE == null || $request->TR_TP_COST_CENTER_CODE == "") {
                return response()->json([
                            'message' => "Cost Center Is Required"
                                ], 500);
            }

            if ($request->TR_TP_GL_ACCOUNT_CODE == null || $request->TR_TP_GL_ACCOUNT_CODE == "") {
                return response()->json([
                            'message' => "GL Account Is Required"
                                ], 500);
            }
        }

        if ($request->TR_TP_HEADER_MVT_CODE != "Y21") {
            foreach ($posting_materials as $row) {
                if ($row["TR_GR_DETAIL_LEFT_QTY"] < $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"]) {
                    return response()->json([
                                'message' => "Max Qty for " . $row["TR_GR_DETAIL_MATERIAL_CODE"] . " " . $row["TR_GR_DETAIL_MATERIAL_NAME"] . " is " . $row["TR_GR_DETAIL_LEFT_QTY"] . " current input Qty is " . $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"]
                                    ], 500);
                }
            }
        }

        $mobile_is_submit = false;
        /**
         * 2023 Nov
         * waluyosejati99@gmail.com
         * 
         * TP Plan for mobile is disabled,
         * TP from web can be directly posted to SAP
         * So, there is no mobile submit
         * 
         */
//        if ($request->TR_TP_HEADER_MVT_CODE == "Y21") {
//            $mobile_is_submit = true;
//        }

        $converted_date = null;
        if ($request->TR_TP_HEADER_PSTG_DATE != NULL && $request->TR_TP_HEADER_PSTG_DATE != "") {
            $converted_date = convert_to_y_m_d($request->TR_TP_HEADER_PSTG_DATE);
        }
        //Insert TP Header
        $tp_id = std_insert_get_id([
            "table_name" => "TR_TP_HEADER",
            "data" => [
                "TR_TP_HEADER_PLANT_CODE" => session("plant"),
                "TR_TP_HEADER_SAP_DOC" => NULL,
                "TR_TP_HEADER_PSTG_DATE" => $converted_date,
                "TR_TP_HEADER_DOC_DATE" => date("Y-m-d"),
                "TR_TP_HEADER_BOL" => $request->TR_TP_HEADER_BOL,
                "TR_TP_HEADER_TXT" => $request->TR_TP_HEADER_TXT,
                "TR_TP_HEADER_MVT_CODE" => $request->TR_TP_HEADER_MVT_CODE,
                "TR_TP_HEADER_SAP_YEAR" => NULL,
                "TR_TP_HEADER_STATUS" => "PENDING",
                "TR_TP_HEADER_PHOTO" => NULL,
                "TR_TP_HEADER_CREATED_BY" => session("id"),
                "TR_TP_HEADER_CREATED_TIMESTAMP" => $timestamp,
                "TR_TP_HEADER_UPDATED_BY" => NULL,
                "TR_TP_HEADER_UPDATED_TIMESTAMP" => NULL,
                "TR_TP_HEADER_MOBILE_IS_SUBMIT" => $mobile_is_submit,
                "TR_TP_COST_CENTER_CODE" => $request->TR_TP_COST_CENTER_CODE,
                "TR_TP_GL_ACCOUNT_CODE" => $request->TR_TP_GL_ACCOUNT_CODE
            ]
        ]);

        if ($tp_id == false) {
            return response()->json([
                        'message' => "Error on saving TP header"
                            ], 500);
        }

        $count = 1;
        $qr_code_codes_temp = NULL;
        $y21_unique_ids_temp = NULL;
        //Populate TP detail
        if ($request->TR_TP_HEADER_MVT_CODE != "Y21") {
            $posting_material_arr = [];
            foreach ($posting_materials as $row) {
                $mobile_qty = NULL;
                $mobile_uom = NULL;
                if ($request->TR_TP_HEADER_MVT_CODE == "551") {
                    $mobile_qty = $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"];
                    $mobile_uom = $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"];
                }


                $posting_material_arr = array_merge($posting_material_arr, [
                    [
                        "TR_TP_DETAIL_TP_HEADER_ID" => $tp_id,
                        "TR_TP_DETAIL_MATERIAL_CODE" => $row["TR_GR_DETAIL_MATERIAL_CODE"],
                        "TR_TP_DETAIL_MATERIAL_NAME" => $row["TR_GR_DETAIL_MATERIAL_NAME"],
                        "TR_TP_DETAIL_SAP_BATCH" => $row["TR_GR_DETAIL_SAP_BATCH"],
                        "TR_TP_DETAIL_QTY" => $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],
                        "TR_TP_DETAIL_UOM" => $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"],
                        "TR_TP_DETAIL_BASE_QTY" => $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],
                        "TR_TP_DETAIL_BASE_UOM" => $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"],
                        "TR_TP_DETAIL_MOBILE_QTY" => $mobile_qty,
                        "TR_TP_DETAIL_MOBILE_UOM" => $mobile_uom,
                        "TR_TP_DETAIL_SLOC" => $row["TR_GR_DETAIL_LOCK_BOOKED_SLOC"],
                        "TR_TP_DETAIL_QR_CODE_NUMBER" => get_gr_detail_qr($row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"]),
                        "TR_TP_DETAIL_NOTES" => NULL,
                        "TR_TP_DETAIL_PHOTO" => NULL,
                        "TR_TP_DETAIL_CREATED_BY" => session("id"),
                        "TR_TP_DETAIL_CREATED_TIMESTAMP" => $timestamp,
                        "TR_TP_DETAIL_UPDATED_BY" => NULL,
                        "TR_TP_DETAIL_UPDATED_TIMESTAMP" => NULL,
                        "TR_TP_DETAIL_GR_DETAIL_ID" => $row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"],
                        "TR_TP_DETAIL_SAPLINE_ID" => $count,
                    ]
                ]);

                if ($request->TR_TP_HEADER_MVT_CODE != "551") {
                    $count = $count + 2;
                } else {
                    $count++;
                }
            }
        } else {
            $posting_material_arr = [];
            foreach ($posting_materials as $row) {
                $qr_code_temp = session("plant") . "-" . uniqid();
                $y21_code_temp = uniqid();
                $qr_code_codes_temp[] = $qr_code_temp;
                $y21_unique_ids_temp[] = $y21_code_temp;
                $posting_material_arr = array_merge($posting_material_arr, [
                    [
                        "TR_TP_DETAIL_TP_HEADER_ID" => $tp_id,
                        "TR_TP_DETAIL_MATERIAL_CODE" => $row["TR_TP_Y21_DETAIL_TEMP_MATERIAL_CODE"],
                        "TR_TP_DETAIL_MATERIAL_NAME" => $row["TR_TP_Y21_DETAIL_TEMP_MATERIAL_NAME"],
                        "TR_TP_DETAIL_SAP_BATCH" => $row["TR_TP_Y21_DETAIL_TEMP_SAP_BATCH"],
                        "TR_TP_DETAIL_QTY" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_QTY"],
                        "TR_TP_DETAIL_UOM" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_UOM"],
                        "TR_TP_DETAIL_BASE_QTY" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_QTY"],
                        "TR_TP_DETAIL_BASE_UOM" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_UOM"],
                        "TR_TP_DETAIL_MOBILE_QTY" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_QTY"],
                        "TR_TP_DETAIL_MOBILE_UOM" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_UOM"],
                        "TR_TP_DETAIL_SLOC" => $row["TR_TP_Y21_DETAIL_TEMP_SLOC_TO"],
                        "TR_TP_DETAIL_SLOC_Y21_FROM" => $row["TR_TP_Y21_DETAIL_TEMP_SLOC_FROM"],
                        "TR_TP_DETAIL_Y21_EXP_DATE" => $row["TR_TP_Y21_DETAIL_TEMP_EXP_DATE"],
                        "TR_TP_DETAIL_QR_CODE_NUMBER" => $qr_code_temp,
                        "TR_TP_DETAIL_NOTES" => $row["TR_TP_Y21_DETAIL_TEMP_NOTES"],
                        "TR_TP_DETAIL_PHOTO" => NULL,
                        "TR_TP_DETAIL_CREATED_BY" => session("id"),
                        "TR_TP_DETAIL_CREATED_TIMESTAMP" => $timestamp,
                        "TR_TP_DETAIL_UPDATED_BY" => NULL,
                        "TR_TP_DETAIL_UPDATED_TIMESTAMP" => NULL,
                        "TR_TP_DETAIL_GR_DETAIL_ID" => NULL,
                        "TR_TP_DETAIL_SAPLINE_ID" => $count,
                        "TR_TP_DETAIL_Y21_GR_REF" => $y21_code_temp
                    ]
                ]);
                $count = $count + 2;
            }
        }

        //Insert TP Detail
        $insert_res = std_insert([
            "table_name" => "TR_TP_DETAIL",
            "data" => $posting_material_arr
        ]);

        //Create New GR Case
        if ($request->TR_TP_HEADER_MVT_CODE != "Y21") {
            foreach ($posting_materials as $row) {
                std_update([
                    "table_name" => "TR_GR_DETAIL",
                    "where" => ["TR_GR_DETAIL_ID" => $row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"]],
                    "data" => [
                        "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" - ' . $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"])
                    ]
                ]);

                if ($request->TR_TP_HEADER_MVT_CODE == "551") {
                    insert_material_log([
                        "material_code" => $row["TR_GR_DETAIL_MATERIAL_CODE"],
                        "plant_code" => session("plant"),
                        "posting_date" => convert_to_y_m_d($request->TR_TP_HEADER_PSTG_DATE),
                        "movement_type" => $request->TR_TP_HEADER_MVT_CODE,
                        "gr_detail_id" => $row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"],
                        "base_qty" => (-1 * $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"]),
                        "base_uom" => $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"],
                        "created_by" => session("id")
                    ]);
                }
            }
        } else {
            $gr_id = std_insert_get_id([
                "table_name" => "TR_GR_HEADER",
                "data" => [
                    "TR_GR_HEADER_PO_NUMBER" => NULL,
                    "TR_GR_HEADER_PLANT_CODE" => session("plant"),
                    "TR_GR_HEADER_SAP_DOC" => "NO SAP",
                    "TR_GR_HEADER_PSTG_DATE" => convert_to_y_m_d($request->TR_TP_HEADER_PSTG_DATE),
                    "TR_GR_HEADER_DOC_DATE" => date("Y-m-d"),
                    "TR_GR_HEADER_BOL" => NULL,
                    "TR_GR_HEADER_TXT" => NULL,
                    "TR_GR_HEADER_MVT_CODE" => $request->TR_TP_HEADER_MVT_CODE,
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

            $count + 0;
            $qr_counter = 0;
            foreach ($posting_materials as $row) {

                $count++;

                $exp_date_y21 = $row["TR_TP_Y21_DETAIL_TEMP_EXP_DATE"];
                if($row['TR_TP_Y21_DETAIL_TEMP_EXP_DATE']){
                    $exploded_exp_date = explode("-", $row['TR_TP_Y21_DETAIL_TEMP_EXP_DATE']);
                    if(count($exploded_exp_date) == 3){
                        if(strlen($exploded_exp_date[0]) == 2){
                            $exp_date_y21 = convert_to_y_m_d($row["TR_TP_Y21_DETAIL_TEMP_EXP_DATE"]);
                        }
                    }else{
                        $exp_date_y21 = convert_to_y_m_d($row["TR_TP_Y21_DETAIL_TEMP_EXP_DATE"]);
                    }
                }
                $gr_detail_id = std_insert_get_id([
                    "table_name" => "TR_GR_DETAIL",
                    "data" => [
                        "TR_GR_DETAIL_HEADER_ID" => $gr_id,
                        "TR_GR_DETAIL_MATERIAL_CODE" => $row["TR_TP_Y21_DETAIL_TEMP_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $row["TR_TP_Y21_DETAIL_TEMP_MATERIAL_NAME"],
                        "TR_GR_DETAIL_SAP_BATCH" => $row["TR_TP_Y21_DETAIL_TEMP_SAP_BATCH"],
                        "TR_GR_DETAIL_QTY" => NULL,
                        "TR_GR_DETAIL_UOM" => NULL,
                        "TR_GR_DETAIL_BASE_QTY" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_QTY"],
                        "TR_GR_DETAIL_BASE_UOM" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_UOM"],
                        "TR_GR_DETAIL_LEFT_QTY" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_QTY"],
                        "TR_GR_DETAIL_UNLOADING_PLANT" => session("plant"),
                        "TR_GR_DETAIL_GL_ACCOUNT" => NULL,
                        "TR_GR_DETAIL_COST_CENTER" => NULL,
                        "TR_GR_DETAIL_EXP_DATE" => $exp_date_y21,
                        "TR_GR_DETAIL_IMG_QRCODE" => NULL,
                        "TR_GR_DETAIL_NOTES" => $row["TR_TP_Y21_DETAIL_TEMP_NOTES"],
                        "TR_GR_DETAIL_PHOTO" => NULL,
                        "TR_GR_DETAIL_CREATED_BY" => session("id"),
                        "TR_GR_DETAIL_CREATED_TIMESTAMP" => $timestamp,
                        "TR_GR_DETAIL_UPDATED_BY" => NULL,
                        "TR_GR_DETAIL_UPDATED_TIMESTAMP" => NULL,
                        "TR_GR_DETAIL_QR_CODE_NUMBER" => $qr_code_codes_temp[$qr_counter],
                        "TR_GR_DETAIL_SLOC" => $row["TR_TP_Y21_DETAIL_TEMP_SLOC_TO"],
                        "TR_GR_DETAIL_PO_DETAIL_ID" => NULL,
                        "TR_GR_DETAIL_GR_REFERENCE" => NULL,
                        "TR_GR_DETAIL_SAPLINE_ID" => $count,
                        //New field to connect TP DETAIL TO GR DETAIL
                        "TR_GR_DETAIL_Y21_TP_REF" => $y21_unique_ids_temp[$qr_counter]
                    ]
                ]);
                $qr_counter++;
                insert_material_log([
                    "material_code" => $row["TR_TP_Y21_DETAIL_TEMP_MATERIAL_CODE"],
                    "plant_code" => session("plant"),
                    "posting_date" => convert_to_y_m_d($request->TR_TP_HEADER_PSTG_DATE),
                    "movement_type" => $request->TR_TP_HEADER_MVT_CODE,
                    "gr_detail_id" => $gr_detail_id,
                    "base_qty" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_QTY"],
                    "base_uom" => $row["TR_TP_Y21_DETAIL_TEMP_BASE_UOM"],
                    "created_by" => session("id")
                ]);
            }
        }

        //Remove temp table & generate CSV
        if ($request->TR_TP_HEADER_MVT_CODE == "Y21") {
            //Y21
            std_delete([
                "table_name" => "TR_TP_Y21_DETAIL_TEMP",
                "where" => [
                    "TR_TP_Y21_DETAIL_TEMP_CREATED_BY" => session("id")
                ]
            ]);
            generate_tp_csv($tp_id, session("plant"));
        } else {
            //311 / 511
            foreach ($posting_materials as $row) {
                std_delete([
                    "table_name" => "TR_GR_DETAIL_LOCK",
                    "where" => [
                        "TR_GR_DETAIL_LOCK_ID" => $row["TR_GR_DETAIL_LOCK_ID"]
                    ]
                ]);
            }
            /**
             * 2023 November
             * waluyosejati99@gmail.com
             * Fitur TP Plan dinon-aktifkan
             * Save TP langsung create CSV untuk siap posting
             */
            if (in_array($request->TR_TP_HEADER_MVT_CODE, ['511', '311'])) {
                generate_tp_csv($tp_id, session("plant"));
            }
        }

        return response()->json([
                    'message' => "Posting Successfully Created"
                        ], 200);
    }
}

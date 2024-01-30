<?php

namespace App\Http\Controllers\PurchaseOrder\GoodIssue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use DB;

function get_gr_data($material_code, $plant_code, $batch = null) {
    $cons = [
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
                "field_name" => "TR_GR_HEADER_STATUS",
                "operator" => "!=",
                "value" => "ERROR"
            ],
            [
                "field_name" => "TR_GR_HEADER_STATUS",
                "operator" => "!=",
                "value" => "PENDING"
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
                "field_name" => "TR_GR_HEADER_IS_CANCELLED",
                "operator" => "=",
                "value" => false
            ],
            [
                "field_name" => "TR_GR_DETAIL_IS_CANCELLED",
                "operator" => "=",
                "value" => false
            ],
            [
                "field_name" => "TR_GR_DETAIL_SLOC",
                "operator" => "!=",
                "value" => "1419"
            ],
            [
                "field_name" => "TR_GR_DETAIL_SLOC",
                "operator" => "!=",
                "value" => "1319"
            ],
            [
                "field_name" => "TR_GR_DETAIL_SLOC",
                "operator" => "!=",
                "value" => "1900"
            ],
        ],
        "order_by" => [
            [
                "field" => "TR_GR_HEADER_CREATED_TIMESTAMP",
                "type" => "ASC",
            ]
        ],
        "first_row" => false
    ];
    /**
     * Nov 2023
     * waluyosejati@gmail.com
     * 
     * Filter ini diPending dulu
     */
//    if (!empty($batch)) {
//        $cons['where'][] = [
//            "field_name" => "TR_GR_DETAIL_SAP_BATCH",
//            "operator" => "=",
//            "value" => $batch
//        ];
//    }
    return std_get($cons);
}

function get_gr_fifo_list($po_detail) {
    $res_data = [];
    $gr_data = get_gr_data($po_detail["TR_PO_DETAIL_MATERIAL_CODE"], session("plant"), $po_detail["TR_PO_DETAIL_MATERIAL_BATCH"]);

    $master_uom_comparison = std_get([
        "select" => ["*"],
        "table_name" => "MA_UOM",
        "where" => [
            [
                "field_name" => "MA_UOM_MATCODE",
                "operator" => "=",
                "value" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"]
            ],
            [
                "field_name" => "MA_UOM_UOM",
                "operator" => "=",
                "value" => $po_detail["TR_PO_DETAIL_UOM"]
            ]
        ],
        "first_row" => true
    ]);

    $gi_qty_check = ($po_detail["TR_PO_DETAIL_QTY_ORDER"] * $master_uom_comparison["MA_UOM_NUM"]) / $master_uom_comparison["MA_UOM_DEN"];

    if (!isset($po_detail["TR_GI_DETAIL_NOTES"])) {
        $po_detail["TR_GI_DETAIL_NOTES"] = NULL;
    }

    $po_qty = $gi_qty_check;

    foreach ($gr_data as $gr_row) {
        $gr_qty = $gr_row["TR_GR_DETAIL_LEFT_QTY"];
        $gi_qty_check -= $gr_row["TR_GR_DETAIL_LEFT_QTY"];

        if ($gi_qty_check <= 0) {

            $res_data = array_merge($res_data, [
                [
                    "uniqid" => uniqid(),
                    "gr_detail_id" => $gr_row["TR_GR_DETAIL_ID"],
                    "po_detail_id" => $po_detail["TR_PO_DETAIL_ID"],
                    "material_code" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"],
                    "material_name" => $po_detail["TR_PO_DETAIL_MATERIAL_NAME"],
                    "qty" => $gr_row["TR_GR_DETAIL_LEFT_QTY"] - abs($gi_qty_check),
                    "uom" => $gr_row["TR_GR_DETAIL_BASE_UOM"],
                    "batch" => $gr_row["TR_GR_DETAIL_SAP_BATCH"],
                    "expired_date" => $gr_row["TR_GR_DETAIL_EXP_DATE"],
                    "TR_GI_DETAIL_NOTES" => $po_detail["TR_GI_DETAIL_NOTES"]
                ]
            ]);
            break;
        } else {
            if ($po_qty > $gr_qty) {
                $gi_qty = $gr_qty;
            }
            $res_data = array_merge($res_data, [
                [
                    "uniqid" => uniqid(),
                    "gr_detail_id" => $gr_row["TR_GR_DETAIL_ID"],
                    "po_detail_id" => $po_detail["TR_PO_DETAIL_ID"],
                    "material_code" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"],
                    "material_name" => $po_detail["TR_PO_DETAIL_MATERIAL_NAME"],
                    "qty" => $gi_qty,
                    "uom" => $gr_row["TR_GR_DETAIL_BASE_UOM"],
                    "batch" => $gr_row["TR_GR_DETAIL_SAP_BATCH"],
                    "expired_date" => $gr_row["TR_GR_DETAIL_EXP_DATE"],
                    "TR_GI_DETAIL_NOTES" => $po_detail["TR_GI_DETAIL_NOTES"]
                ]
            ]);
        }
    }

    if ($gi_qty_check > 0) {
        return [
            "status" => false,
            "data" => [
                "material_code" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"],
                "material_name" => $po_detail["TR_PO_DETAIL_MATERIAL_NAME"],
                "err_qty" => $gi_qty_check,
            ]
        ];
    } else {
        return [
            "status" => true,
            "data" => $res_data
        ];
    }
}

function get_lock_data($gi_po_number) {
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
                "field_name" => "TR_GR_DETAIL_LOCK_PO_HEADER_ID",
                "operator" => "=",
                "value" => $gi_po_number
            ]
        ],
        "first_row" => false
    ]);
}

class AddController extends Controller {

    public function index(Request $request) {
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
//        dd($gi_data);
        if ($gi_data != NULL) {
            return back()->withInput();
        }

        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->gi_po_number
                ]
            ],
            "first_row" => true
        ]);

        $detail_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->gi_po_number,
                ]
            ],
            "first_row" => false
        ]);

        $lock_data = get_lock_data($request->gi_po_number);

        if ($lock_data == null) {
            $gi_cart_system_generated = [];
            foreach ($detail_data as $row) {
                $fifo_res = get_gr_fifo_list($row);
                if ($fifo_res["status"] == true) {
                    $gi_cart_system_generated = array_merge($gi_cart_system_generated, $fifo_res["data"]);
                } else {
                    $request->session()->flash('fifo_res', $fifo_res["data"]);
                    return redirect()->route('purchase_order_good_issue_view');
                }
            }
            $gi_cart = $gi_cart_system_generated;
            $insert_lock_data = [];
            foreach ($gi_cart as $row) {
                $insert_lock_data = array_merge($insert_lock_data, [
                    [
                        "TR_GR_DETAIL_LOCK_GR_DETAIL_ID" => $row["gr_detail_id"],
                        "TR_GR_DETAIL_LOCK_PO_HEADER_ID" => $request->gi_po_number,
                        "TR_GR_DETAIL_LOCK_BOOKED_QTY" => $row["qty"],
                        "TR_GR_DETAIL_LOCK_BOOKED_UOM" => $row["uom"],
                        "TR_GR_DETAIL_LOCK_EXPIRED_TIMESTAMP" => date("Y-m-d H:i:s", strtotime("+5 minutes")),
                        "TR_GR_DETAIL_LOCK_CREATED_BY" => session("id"),
                        "TR_GR_DETAIL_LOCK_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
                    ]
                ]);
            }
            /**
             * 2023, Nov
             * waluyosejati99@gmail.com
             * 
             * Disable automatic insert of gr lock, requested by IT user
             */
//            std_insert([
//                "table_name" => "TR_GR_DETAIL_LOCK",
//                "data" => $insert_lock_data
//            ]);
//            $lock_data = get_lock_data($request->gi_po_number);
        }

        $movement_code = "351";
        if ($data["TR_PO_HEADER_TYPE"] == "ZSTO") {
            $movement_code = "351";
        } elseif ($data["TR_PO_HEADER_TYPE"] == "ZRET") {
            $movement_code = "161";
        }

        return view('transaction/purchase_order/good_issue/add', [
            "header_data" => $data,
            "detail_data" => $detail_data,
            "po_number" => $request->gi_po_number,
            "temp_material" => $lock_data,
            "header_posting_date" => $request->header_posting_date,
            "header_bill_of_landing" => $request->header_bill_of_landing,
            "header_recipient" => $request->header_recipient,
            "header_note" => $request->header_note,
            "movement_code" => $movement_code
        ]);
    }

    public function get_materials(Request $request) {
        $materials = std_get([
            "select" => ["TR_PO_DETAIL_ID", "TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_MATERIAL_NAME", "TR_PO_DETAIL_MATERIAL_LINE_NUM"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ],
            "order_by" =>[
                    [
                        "field" => "TR_PO_DETAIL_MATERIAL_CODE",
                        "type" => "ASC",
                    ],                
                    [
                        "field" => "TR_PO_DETAIL_MATERIAL_LINE_NUM",
                        "type" => "ASC",
                    ],                
            ]
        ]);
        if ($materials != null) {
            foreach ($materials as $row) {
                $linenum = empty($row["TR_PO_DETAIL_MATERIAL_LINE_NUM"])? "0" : $row["TR_PO_DETAIL_MATERIAL_LINE_NUM"];
                $materials_adj[] = [
                    "id" => $row["TR_PO_DETAIL_ID"],
                    "text" => $linenum . " - " . $row["TR_PO_DETAIL_MATERIAL_CODE"] . " - " . $row["TR_PO_DETAIL_MATERIAL_NAME"]
                ];
            }
            return response()->json([
                        "status" => "OK",
                        "data" => $materials_adj
                            ], 200);
        } else {
            return response()->json([
                        "status" => "OK",
                        "data" => []
                            ], 200);
        }
    }

    public function get_material_gr(Request $request) {
        $po_detail = std_get([
            "select" => ["TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_PLANT_RCV"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_ID",
                    "operator" => "=",
                    "value" => $request->po_detail_id
                ]
            ],
            "first_row" => true
        ]);

        $gr_data = get_gr_data($po_detail["TR_PO_DETAIL_MATERIAL_CODE"], session("plant"));

        $select2 = [];
        foreach ($gr_data as $row) {
            $select2 = array_merge($select2, [
                [
                    "id" => $row["TR_GR_DETAIL_ID"],
                    "text" => $row["TR_GR_DETAIL_ID"] . " - " . number_format($row["TR_GR_DETAIL_LEFT_QTY"]) . " " . $row["TR_GR_DETAIL_BASE_UOM"] . " | " . $row["TR_GR_DETAIL_SAP_BATCH"] . " | " . $row["TR_GR_DETAIL_EXP_DATE"]
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

        $gr_data["TR_GR_DETAIL_LEFT_QTY"] = number_format($gr_data["TR_GR_DETAIL_LEFT_QTY"]);

        return response()->json([
                    "status" => "OK",
                    "data" => $gr_data
                        ], 200);
    }

    public function save_material_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "gr_detail_id" => "required|max:255",
                    "gi_qty" => "required|max:255",
                    "gi_note" => "required|max:1000",
        ]);

        $attributeNames = [
            "gr_detail_id" => "GR DETAIL ID",
            "gi_qty" => "GI QTY",
            "gi_note" => "GI Note",
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
                "TR_GR_DETAIL_LOCK_PO_HEADER_ID" => $request->po_number,
                "TR_GR_DETAIL_LOCK_BOOKED_QTY" => $request->gi_qty,
                "TR_GR_DETAIL_LOCK_BOOKED_UOM" => $gr_data["TR_GR_DETAIL_BASE_UOM"],
                "TR_GR_DETAIL_LOCK_EXPIRED_TIMESTAMP" => date("Y-m-d H:i:s", strtotime("+1 hours")),
                "TR_GR_DETAIL_LOCK_CREATED_BY" => session("id"),
                "TR_GR_DETAIL_LOCK_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TR_GR_DETAIL_LOCK_NOTE" => $request->gi_note
            ]
        ]);

        return redirect()->route("purchase_order_good_issue_add", [
                    'gi_po_number' => $request->po_number,
                    'header_posting_date' => $request->TR_GI_HEADER_PSTG_DATE,
                    'header_bill_of_landing' => $request->TR_GI_HEADER_BOL,
                    "header_recipient" => $request->header_recipient,
                    "header_note" => $request->TR_GI_HEADER_TXT,
        ]);
    }

    public function delete_material(Request $request) {
        $delete_res = std_delete([
            "table_name" => "TR_GR_DETAIL_LOCK",
            "where" => [
                "TR_GR_DETAIL_LOCK_ID" => $request->uniqid
            ]
        ]);
        return redirect()->back();
    }

    public function save_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "po_number" => "required|max:255",
                    "TR_GI_HEADER_PSTG_DATE" => "required",
                    "TR_GI_HEADER_BOL" => "max:255",
                    // "TR_GI_HEADER_RECIPIENT" => "required|max:255",
                    "TR_GI_HEADER_TXT" => "required|max:1000"
        ]);

        $attributeNames = [
            "po_number" => "PO Number",
            "TR_GI_HEADER_PSTG_DATE" => "Posting Date",
            // "TR_GI_HEADER_BOL" => "Bill Of Landing",
            // "TR_GI_HEADER_RECIPIENT" => "Recipient",
            "TR_GI_HEADER_TXT" => "Note"
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

        $gi_materials = get_lock_data($request->po_number);
        $timestamp = date("Y-m-d H:i:s");
        if ($gi_materials == NULL) {
            return response()->json([
                        'message' => "GI Material Data Not Exist / Empty"
                            ], 500);
        }

        $delete_status = false;

        foreach ($gi_materials as $row) {
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
        $global_gi_materials = $gi_materials;
        if ($delete_status === true) {
            return response()->json([
                        'message' => "GR Locked data is already expired, GI Material Will Be Repopulated!"
                            ], 500);
        }

        $po_header = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ],
            "first_row" => true
        ]);

        if ($po_header == NULL) {
            return response()->json([
                        'message' => "PO Header Not Exist"
                            ], 500);
        }

        $po_detail1 = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ],
            "first_row" => true
        ]);

        $plant_code = $po_header["TR_PO_HEADER_SUP_PLANT"];
        $plant_code_file = $po_header["TR_PO_HEADER_SUP_PLANT"];
        if ($po_header["TR_PO_HEADER_TYPE"] == "ZSTO") {
            $plant_code = $po_detail1["TR_PO_DETAIL_PLANT_RCV"];
        }

        $po_detail = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ],
            "order_by" =>[
                    [
                        "field" => "TR_PO_DETAIL_MATERIAL_CODE",
                        "type" => "ASC",
                    ],                
                    [
                        "field" => "TR_PO_DETAIL_MATERIAL_LINE_NUM",
                        "type" => "ASC",
                    ],                
            ],
            "first_row" => false
        ]);
        
        $temp_gi = $gi_materials;
        
        $count = 0;
        foreach ($po_detail as $row) {
            $master_material = std_get([
                "select" => ["*"],
                "table_name" => "MA_MATL",
                "where" => [
                    [
                        "field_name" => "MA_MATL_CODE",
                        "operator" => "=",
                        "value" => $row["TR_PO_DETAIL_MATERIAL_CODE"]
                    ],
                    [
                        "field_name" => "MA_MATL_PLANT",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ],
                "first_row" => true
            ]);

            if ($master_material == NULL) {
                return response()->json([
                            'message' => "Master Material Tidak Ditemukan"
                                ], 500);
            }

            $master_uom_base = std_get([
                "select" => ["*"],
                "table_name" => "MA_UOM",
                "where" => [
                    [
                        "field_name" => "MA_UOM_MATCODE",
                        "operator" => "=",
                        "value" => $row["TR_PO_DETAIL_MATERIAL_CODE"]
                    ],
                    [
                        "field_name" => "MA_UOM_UOM",
                        "operator" => "=",
                        "value" => $master_material["MA_MATL_UOM"]
                    ]
                ],
                "first_row" => true
            ]);

            $master_uom_comparison = std_get([
                "select" => ["*"],
                "table_name" => "MA_UOM",
                "where" => [
                    [
                        "field_name" => "MA_UOM_MATCODE",
                        "operator" => "=",
                        "value" => $row["TR_PO_DETAIL_MATERIAL_CODE"]
                    ],
                    [
                        "field_name" => "MA_UOM_UOM",
                        "operator" => "=",
                        "value" => $row["TR_PO_DETAIL_UOM"]
                    ]
                ],
                "first_row" => true
            ]);

            if ($master_uom_base["MA_UOM_ID"] == $master_uom_comparison["MA_UOM_ID"]) {
                $base_qty = $row["TR_PO_DETAIL_QTY_ORDER"];
            } else {
                $base_qty = ($row["TR_PO_DETAIL_QTY_ORDER"] * $master_uom_comparison["MA_UOM_NUM"]) / $master_uom_comparison["MA_UOM_DEN"];
            }

            $check_qty = $base_qty;

            $material_code = $row["TR_PO_DETAIL_MATERIAL_CODE"];
            $material_name = $row["TR_PO_DETAIL_MATERIAL_NAME"];



            for ($i = 0; $i < count($temp_gi); $i++) {
//            echo "PODETAIL:".json_encode($row);
//            echo "<br/>";
//            echo "<br/>";
//            echo "<br/>";
//            echo "TEMPDETIL:".json_encode($temp_gi[$i]);
//            echo "<br/>";
//            echo "<br/>";
//            echo "<br/>";

                if (($row["TR_PO_DETAIL_MATERIAL_CODE"] == $temp_gi[$i]["TR_GR_DETAIL_MATERIAL_CODE"]) && ($base_qty >= $temp_gi[$i]["TR_GR_DETAIL_LOCK_BOOKED_QTY"])) {
                    $global_gi_materials[$count]["po_id"] = $row["TR_PO_DETAIL_ID"];
                    $check_qty -= $temp_gi[$i]["TR_GR_DETAIL_LOCK_BOOKED_QTY"];
                    unset($gi_materials[$i]);
                    $count++;
                }else{
                    
                }

                if ($check_qty == 0) {
                    break;
                }
            }

//            echo "QTY:".json_encode($check_qty);
//            echo "<br/>";
//            echo "<br/>";
//            echo "<br/>";
            if ($check_qty != 0) {
                return response()->json([
                            'message' => $material_code . " " . $material_name . " Qty harus full GI"
                                ], 500);
//                            'message' => $material_code . " " . $material_name . " Qty GI harus sama dengan Qty PO"
            }
//            else{
//                return response()->json([
//                            'message' => $material_code . " " . $material_name . " Qty harus full GI"
//                                ], 500);
//            }
            $gi_materials = array_values($gi_materials);
        }
//            echo json_encode($temp_gi);echo "<br/>";
//            dd("ANU=");
        $movement_code = "351"; //ZRAW
        if ($po_header["TR_PO_HEADER_TYPE"] == "ZSTO") {
            $movement_code = "351";
        } elseif ($po_header["TR_PO_HEADER_TYPE"] == "ZRET") {
            $movement_code = "101";
        }

        foreach ($global_gi_materials as $row) {
            $check_res = false;
            foreach ($po_detail as $po_detail_row) {
                if ($po_detail_row["TR_PO_DETAIL_MATERIAL_CODE"] == $row["TR_GR_DETAIL_MATERIAL_CODE"]) {
                    $check_res = true;
                }
            }
            if ($check_res == false) {
                return response()->json([
                            'message' => "GI item not exist on PO detail data"
                                ], 500);
            }
        }

        $gi_id = std_insert_get_id([
            "table_name" => "TR_GI_SAPHEADER",
            "data" => [
                "TR_GI_SAPHEADER_PO_NUMBER" => $request->po_number,
                "TR_GI_SAPHEADER_PLANT_CODE" => $plant_code,
                "TR_GI_SAPHEADER_CREATED_PLANT_CODE" => session("plant"),
                "TR_GI_SAPHEADER_SAP_DOC" => NULL,
                "TR_GI_SAPHEADER_PSTG_DATE" => convert_to_y_m_d($request->TR_GI_HEADER_PSTG_DATE),
                "TR_GI_SAPHEADER_DOC_DATE" => date("Y-m-d"),
                "TR_GI_SAPHEADER_BOL" => $request->TR_GI_HEADER_BOL,
                "TR_GI_SAPHEADER_TXT" => $request->TR_GI_HEADER_TXT,
                "TR_GI_SAPHEADER_MVT_CODE" => $movement_code,
                "TR_GI_SAPHEADER_SAP_YEAR" => NULL,
                "TR_GI_SAPHEADER_STATUS" => "PENDING",
                "TR_GI_SAPHEADER_ERROR" => NULL,
                "TR_GI_SAPHEADER_PHOTO" => NULL,
                "TR_GI_SAPHEADER_CREATED_BY" => session("id"),
                "TR_GI_SAPHEADER_CREATED_TIMESTAMP" => $timestamp,
                "TR_GI_SAPHEADER_UPDATED_BY" => NULL,
                "TR_GI_SAPHEADER_UPDATED_TIMESTAMP" => NULL
            ]
        ]);

        if ($gi_id == false) {
            return response()->json([
                        'message' => "Error on saving GI header"
                            ], 500);
        }

        $gi_material_arr = [];
        $count_sap_line_id = 1;

        foreach ($global_gi_materials as $row) {
            $gi_material_arr = array_merge($gi_material_arr, [
                [
                    "TR_GI_SAPDETAIL_SAPHEADER_ID" => $gi_id,
                    "TR_GI_SAPDETAIL_MATERIAL_CODE" => $row["TR_GR_DETAIL_MATERIAL_CODE"],
                    "TR_GI_SAPDETAIL_MATERIAL_NAME" => $row["TR_GR_DETAIL_MATERIAL_NAME"],
                    "TR_GI_SAPDETAIL_SAP_BATCH" => $row["TR_GR_DETAIL_SAP_BATCH"],
                    "TR_GI_SAPDETAIL_GI_QTY" => $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],
                    "TR_GI_SAPDETAIL_GI_UOM" => $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"],
                    "TR_GI_SAPDETAIL_MOBILE_QTY" => $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],
                    "TR_GI_SAPDETAIL_MOBILE_UOM" => $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"],
                    "TR_GI_SAPDETAIL_BASE_QTY" => $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],
                    "TR_GI_SAPDETAIL_BASE_UOM" => $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"],
                    "TR_GI_SAPDETAIL_SLOC" => $row["TR_GR_DETAIL_SLOC"],
                    "TR_GI_SAPDETAIL_QR_CODE_NUMBER" => get_gr_detail_qr($row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"]),
                    "TR_GI_SAPDETAIL_NOTES" => NULL,
                    "TR_GI_SAPDETAIL_PHOTO" => NULL,
                    "TR_GI_SAPDETAIL_CREATED_BY" => session("id"),
                    "TR_GI_SAPDETAIL_CREATED_TIMESTAMP" => $timestamp,
                    "TR_GI_SAPDETAIL_UPDATED_BY" => NULL,
                    "TR_GI_SAPDETAIL_UPDATED_TIMESTAMP" => NULL,
                    "TR_GI_SAPDETAIL_GR_DETAIL_ID" => $row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"],
                    "TR_GI_SAPDETAIL_PO_DETAIL_ID" => $row["po_id"],
                    "TR_GI_SAPDETAIL_SAPLINE_ID" => $count_sap_line_id
                ]
            ]);
            $count_sap_line_id = $count_sap_line_id + 2;
        }

        $insert_res = std_insert([
            "table_name" => "TR_GI_SAPDETAIL",
            "data" => $gi_material_arr
        ]);

        foreach ($global_gi_materials as $row) {
            std_update([
                "table_name" => "TR_GR_DETAIL",
                "where" => ["TR_GR_DETAIL_ID" => $row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"]],
                "data" => [
                    "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" - ' . $row["TR_GR_DETAIL_LOCK_BOOKED_QTY"])
                ]
            ]);
            insert_material_log([
                "material_code" => $row["TR_GR_DETAIL_MATERIAL_CODE"],
                "plant_code" => session("plant"),
                "posting_date" => convert_to_y_m_d($request->TR_GI_HEADER_PSTG_DATE),
                "movement_type" => $movement_code,
                "gr_detail_id" => $row["TR_GR_DETAIL_LOCK_GR_DETAIL_ID"],
                "base_qty" => -$row["TR_GR_DETAIL_LOCK_BOOKED_QTY"],
                "base_uom" => $row["TR_GR_DETAIL_LOCK_BOOKED_UOM"],
                "created_by" => session("id")
            ]);
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
         * Fitur GI Plan dinon-aktifkan
         * Save GI langsung create CSV untuk siap posting
         */
        generate_gi_csv($gi_id, $plant_code_file);

        return response()->json([
                    'message' => "GI Successfully Created"
                        ], 200);
    }
}

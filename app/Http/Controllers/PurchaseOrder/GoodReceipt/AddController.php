<?php

namespace App\Http\Controllers\PurchaseOrder\GoodReceipt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use DB;

class AddController extends Controller {

    public function index(Request $request) {
        $data = std_get([
            "select" => ["*"],
            "table_name" => "TR_PO_HEADER",
            "where" => [
                [
                    "field_name" => "TR_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->gr_po_number
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
                    "value" => $request->gr_po_number,
                ]
            ],
            "first_row" => false
        ]);

        if (session("gr_cart") == null) {
            $gr_cart = [];
        } else {
            $gr_cart = session("gr_cart");
        }

        return view('transaction/purchase_order/good_receipt/add', [
            "header_data" => $data,
            "detail_data" => $detail_data,
            "po_number" => $request->gr_po_number,
            "temp_material" => $gr_cart,
            "header_posting_date" => $request->header_posting_date,
            "header_bill_of_landing" => $request->header_bill_of_landing,
            "header_recipient" => $request->header_recipient,
            "header_note" => $request->header_note,
            "err_message" => $request->message
        ]);
    }

    public function get_materials(Request $request) {
        $materials = std_get([
            "select" => ["TR_PO_DETAIL_ID", "TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_MATERIAL_NAME","TR_PO_DETAIL_MATERIAL_LINE_NUM"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ],
            "order_by" => [
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

    public function get_material_status(Request $request) {
        $po_detail = std_get([
            "select" => ["TR_PO_DETAIL_SLOC", "TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_PLANT_RCV", "TR_PO_DETAIL_QTY_ORDER", "TR_PO_DETAIL_QTY_DELIV", "TR_PO_DETAIL_UOM", "TR_PO_DETAIL_PO_HEADER_NUMBER", "TR_PO_DETAIL_MATERIAL_BATCH"],
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
        if (empty($po_detail['TR_PO_DETAIL_MATERIAL_CODE'])) {
            $gr_data = [];
        } else {

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
                        "field_name" => "TR_GR_HEADER_PO_NUMBER",
                        "operator" => "=",
                        "value" => $po_detail["TR_PO_DETAIL_PO_HEADER_NUMBER"]
                    ],
                    [
                        "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                        "operator" => "=",
                        "value" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"]
                    ],
                    [
                        "field_name" => "TR_GR_HEADER_IS_CANCELLED",
                        "operator" => "!=",
                        "value" => true
                    ]
                ]
            ]);
        }


        /* f ($gr_data != NULL) {
          foreach ($gr_data as $row) {
          $po_detail["TR_PO_DETAIL_QTY_ORDER"] -= $row["TR_GR_DETAIL_QTY"];
          }
          } */

        $po_detail["TR_PO_DETAIL_QTY_ORDER"] -= $po_detail["TR_PO_DETAIL_QTY_DELIV"];
        $material = std_get([
            "select" => ["MA_MATL_BATCH as id", "MA_MATL_BATCH as text"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_CODE",
                    "operator" => "=",
                    "value" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"]
                ],
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => $po_detail["TR_PO_DETAIL_PLANT_RCV"]
                ]
            ],
            "distinct" => true
        ]);

        if ($gr_data == NULL) {
            $qty_left = $po_detail["TR_PO_DETAIL_QTY_ORDER"];
        } else {
            $qty_left = ($po_detail["TR_PO_DETAIL_QTY_ORDER"]);
        }

        return response()->json([
                    "status" => "OK",
                    "data" => [
                        "sloc" => $po_detail["TR_PO_DETAIL_SLOC"],
                        "batch" => $po_detail["TR_PO_DETAIL_MATERIAL_BATCH"],
                        "qty_left" => $qty_left . " " . $po_detail["TR_PO_DETAIL_UOM"],
                        "batch_list" => $material
                    ]
                        ], 200);
    }

    public function save_material_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "po_detail_id" => "required|numeric",
                    "batch_sap" => "max:255",
                    "expired_date" => "required|max:255",
                    "qty" => "required|max:255",
                    "TR_GR_DETAIL_NOTES" => "max:65000",
                    "TR_GR_DETAIL_SLOC" => "required|max:255"
        ]);

        $attributeNames = [
            "po_detail_id" => "Material Code",
            "batch_sap" => "Batch SAP",
            "expired_date" => "Expired Date",
            "qty" => "Qty",
            "TR_GR_DETAIL_NOTES" => "Note",
            "TR_GR_DETAIL_SLOC" => "Material SLOC"
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

        $po_detail = std_get([
            "select" => ["TR_PO_DETAIL_SLOC", "TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_MATERIAL_NAME", "TR_PO_DETAIL_PO_HEADER_NUMBER", "TR_PO_DETAIL_PLANT_RCV", "TR_PO_DETAIL_QTY_ORDER", "TR_PO_DETAIL_QTY_DELIV", "TR_PO_DETAIL_UOM"],
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
                    "field_name" => "TR_GR_HEADER_PO_NUMBER",
                    "operator" => "=",
                    "value" => $po_detail["TR_PO_DETAIL_PO_HEADER_NUMBER"]
                ],
                [
                    "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "operator" => "=",
                    "value" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"]
                ],
                [
                    "field_name" => "TR_GR_DETAIL_MATERIAL_NAME",
                    "operator" => "=",
                    "value" => $po_detail["TR_PO_DETAIL_MATERIAL_NAME"]
                ]
            ]
        ]);

        /* if ($gr_data != NULL) {
          foreach ($gr_data as $row) {
          $po_detail["TR_PO_DETAIL_QTY_ORDER"] -= $row["TR_GR_DETAIL_QTY"];
          }
          } */
        $po_detail["TR_PO_DETAIL_QTY_ORDER"] -= $po_detail["TR_PO_DETAIL_QTY_DELIV"];

        //var_dump($po_detail["TR_PO_DETAIL_QTY_ORDER"]);die;
        if (round($request->qty, 3) > round($po_detail["TR_PO_DETAIL_QTY_ORDER"], 3)) {
            return redirect()->route("purchase_order_good_receipt_add", [
                        'gr_po_number' => $po_detail["TR_PO_DETAIL_PO_HEADER_NUMBER"],
                        'header_posting_date' => $request->TR_GR_HEADER_PSTG_DATE,
                        'header_bill_of_landing' => $request->TR_GR_HEADER_BOL,
                        'header_recipient' => $request->TR_GR_HEADER_RECIPIENT,
                        'header_note' => $request->TR_GR_HEADER_TXT,
                        'message' => "Qty input must be lower than Qty Left"
            ]);
        } else {
            if (session("gr_cart") != null) {
                $gr_cart = session("gr_cart");
                $gr_cart = array_merge($gr_cart, [
                    [
                        "uniqid" => uniqid(),
                        "po_detail_id" => $request->po_detail_id,
                        "material_code" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"],
                        "material_name" => $po_detail["TR_PO_DETAIL_MATERIAL_NAME"],
                        "qty" => $request->qty,
                        "uom" => $po_detail["TR_PO_DETAIL_UOM"],
                        "batch" => $request->batch_sap,
                        "expired_date" => convert_to_y_m_d($request->expired_date),
                        "TR_GR_DETAIL_NOTES" => $request->TR_GR_DETAIL_NOTES,
                        "TR_GR_DETAIL_SLOC" => $request->TR_GR_DETAIL_SLOC
                    ]
                ]);
                session([
                    "gr_cart" => $gr_cart
                ]);
            } else {
                session([
                    "gr_cart" => [
                        [
                            "uniqid" => uniqid(),
                            "po_detail_id" => $request->po_detail_id,
                            "material_code" => $po_detail["TR_PO_DETAIL_MATERIAL_CODE"],
                            "material_name" => $po_detail["TR_PO_DETAIL_MATERIAL_NAME"],
                            "qty" => $request->qty,
                            "uom" => $po_detail["TR_PO_DETAIL_UOM"],
                            "batch" => $request->batch_sap,
                            "expired_date" => convert_to_y_m_d($request->expired_date),
                            "TR_GR_DETAIL_NOTES" => $request->TR_GR_DETAIL_NOTES,
                            "TR_GR_DETAIL_SLOC" => $request->TR_GR_DETAIL_SLOC
                        ]
                    ]
                ]);
            }

            return redirect()->route("purchase_order_good_receipt_add", [
                        'gr_po_number' => $po_detail["TR_PO_DETAIL_PO_HEADER_NUMBER"],
                        'header_posting_date' => $request->TR_GR_HEADER_PSTG_DATE,
                        'header_bill_of_landing' => $request->TR_GR_HEADER_BOL,
                        'header_recipient' => $request->TR_GR_HEADER_RECIPIENT,
                        'header_note' => $request->TR_GR_HEADER_TXT
            ]);
        }
    }

    public function delete_material(Request $request) {
        if (session("gr_cart") != null) {
            $gr_cart = session("gr_cart");
            $key = array_search($request->uniqid, array_column($gr_cart, 'uniqid'));
            if ($key !== false) {
                $po_detail = std_get([
                    "select" => ["TR_PO_DETAIL_SLOC", "TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_MATERIAL_NAME", "TR_PO_DETAIL_PO_HEADER_NUMBER", "TR_PO_DETAIL_PLANT_RCV", "TR_PO_DETAIL_QTY_ORDER", "TR_PO_DETAIL_QTY_DELIV", "TR_PO_DETAIL_UOM"],
                    "table_name" => "TR_PO_DETAIL",
                    "where" => [
                        [
                            "field_name" => "TR_PO_DETAIL_ID",
                            "operator" => "=",
                            "value" => $gr_cart[$key]["po_detail_id"]
                        ]
                    ],
                    "first_row" => true
                ]);
                unset($gr_cart[$key]);
                $gr_cart = array_values($gr_cart);
                session([
                    "gr_cart" => $gr_cart
                ]);
                return redirect()->route("purchase_order_good_receipt_add", [
                            'gr_po_number' => $po_detail["TR_PO_DETAIL_PO_HEADER_NUMBER"],
                            'header_posting_date' => $request->TR_GR_HEADER_PSTG_DATE,
                            'header_bill_of_landing' => $request->TR_GR_HEADER_BOL,
                            'header_recipient' => $request->TR_GR_HEADER_RECIPIENT,
                            'header_note' => $request->TR_GR_HEADER_TXT
                ]);
            }
        }
        return redirect()->back();
    }

    public function save_validate_input($request) {
        $validate = Validator::make($request->all(), [
                    "po_number" => "required|max:255",
                    "TR_GR_HEADER_PSTG_DATE" => "required",
                    "TR_GR_HEADER_BOL" => "max:255",
                    "TR_GR_HEADER_RECIPIENT" => "required|max:255",
                    "TR_GR_HEADER_TXT" => "required|max:1000"
        ]);

        $attributeNames = [
            "po_number" => "PO Number",
            "TR_GR_HEADER_PSTG_DATE" => "Posting Date",
            "TR_GR_HEADER_BOL" => "Bill Of Landing",
            "TR_GR_HEADER_RECIPIENT" => "Recipient",
            "TR_GR_HEADER_TXT" => "Note"
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

        $gr_materials = session('gr_cart');
        $timestamp = date("Y-m-d H:i:s");
        if ($gr_materials == NULL) {
            return response()->json([
                        'message' => "GR Material Data Not Exist / Empty"
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
            "first_row" => true
        ]);

        $plant_code = $po_header["TR_PO_HEADER_SUP_PLANT"];
        if ($po_header["TR_PO_HEADER_TYPE"] == "ZSTO") {
            $plant_code = $po_detail["TR_PO_DETAIL_PLANT_RCV"];
        }

        foreach ($gr_materials as $row) {
            $master_material = std_get([
                "select" => ["*"],
                "table_name" => "MA_MATL",
                "where" => [
                    [
                        "field_name" => "MA_MATL_CODE",
                        "operator" => "=",
                        "value" => $row["material_code"]
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
                            'message' => "Material Master Not Found [$row[material_code]]"
                                ], 500);
            }
        }

        $gr_adjustment = false;
        if (isset($request->TR_GR_HEADER_IS_ADJUSTMENT) && $request->TR_GR_HEADER_IS_ADJUSTMENT == "on") {
            $gr_adjustment = true;
        }
        $gr_id = std_insert_get_id([
            "table_name" => "TR_GR_HEADER",
            "data" => [
                "TR_GR_HEADER_PO_NUMBER" => $request->po_number,
                "TR_GR_HEADER_PLANT_CODE" => $plant_code,
                "TR_GR_HEADER_SAP_DOC" => NULL,
                "TR_GR_HEADER_PSTG_DATE" => convert_to_y_m_d($request->TR_GR_HEADER_PSTG_DATE),
                "TR_GR_HEADER_DOC_DATE" => date("Y-m-d"),
                "TR_GR_HEADER_BOL" => $request->TR_GR_HEADER_BOL,
                "TR_GR_HEADER_TXT" => $request->TR_GR_HEADER_TXT,
                "TR_GR_HEADER_MVT_CODE" => 101,
                "TR_GR_HEADER_SAP_YEAR" => NULL,
                "TR_GR_HEADER_STATUS" => "PENDING",
                "TR_GR_HEADER_ERROR" => NULL,
                "TR_GR_HEADER_PHOTO" => NULL,
                "TR_GR_HEADER_RECIPIENT" => $request->TR_GR_HEADER_RECIPIENT,
                "TR_GR_HEADER_CREATED_BY" => session("id"),
                "TR_GR_HEADER_CREATED_TIMESTAMP" => $timestamp,
                "TR_GR_HEADER_UPDATED_BY" => NULL,
                "TR_GR_HEADER_UPDATED_TIMESTAMP" => NULL,
                "TR_GR_HEADER_IS_ADJUSTMENT" => $gr_adjustment,
                "TR_GR_HEADER_PRINT_COUNT" => 0
            ]
        ]);

        if ($gr_id == false) {
            return response()->json([
                        'message' => "Error on saving GR header"
                            ], 500);
        }

        $gr_material_arr = [];
        $count = 0;
        foreach ($gr_materials as $row) {
            $qr_code_number = session("plant") . "-" . uniqid();

            $po_detail1 = std_get([
                "select" => ["TR_PO_DETAIL_SLOC", "TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_PLANT_RCV", "TR_PO_DETAIL_QTY_ORDER", "TR_PO_DETAIL_QTY_DELIV", "TR_PO_DETAIL_UOM"],
                "table_name" => "TR_PO_DETAIL",
                "where" => [
                    [
                        "field_name" => "TR_PO_DETAIL_ID",
                        "operator" => "=",
                        "value" => $row["po_detail_id"]
                    ]
                ],
                "first_row" => true
            ]);

            $master_material = std_get([
                "select" => ["*"],
                "table_name" => "MA_MATL",
                "where" => [
                    [
                        "field_name" => "MA_MATL_CODE",
                        "operator" => "=",
                        "value" => $row["material_code"]
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
                            'message' => "Material Master Not Found [$row[material_code]], but GR header already saved, please contact your admin"
                                ], 500);
            }

            $master_uom_base = std_get([
                "select" => ["*"],
                "table_name" => "MA_UOM",
                "where" => [
                    [
                        "field_name" => "MA_UOM_MATCODE",
                        "operator" => "=",
                        "value" => $row["material_code"]
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
                        "value" => $row["material_code"]
                    ],
                    [
                        "field_name" => "MA_UOM_UOM",
                        "operator" => "=",
                        "value" => $po_detail1["TR_PO_DETAIL_UOM"]
                    ]
                ],
                "first_row" => true
            ]);

            if ($master_uom_base["MA_UOM_ID"] == $master_uom_comparison["MA_UOM_ID"]) {
                $base_qty = $row["qty"];
                $qty_left = $base_qty;
            } else {
                $base_qty = ($row["qty"] * $master_uom_comparison["MA_UOM_NUM"]) / $master_uom_comparison["MA_UOM_DEN"];
                $qty_left = $base_qty;
            }

            $count++;

            $gr_material_arr = array_merge($gr_material_arr, [
                [
                    "TR_GR_DETAIL_HEADER_ID" => $gr_id,
                    "TR_GR_DETAIL_MATERIAL_CODE" => $row["material_code"],
                    "TR_GR_DETAIL_MATERIAL_NAME" => $row["material_name"],
                    "TR_GR_DETAIL_SAP_BATCH" => $row["batch"],
                    "TR_GR_DETAIL_QTY" => $row["qty"],
                    "TR_GR_DETAIL_UOM" => $row["uom"],
                    "TR_GR_DETAIL_BASE_QTY" => $base_qty,
                    "TR_GR_DETAIL_BASE_UOM" => $master_material["MA_MATL_UOM"],
                    "TR_GR_DETAIL_LEFT_QTY" => NULL,
                    "TR_GR_DETAIL_UNLOADING_PLANT" => $po_detail1["TR_PO_DETAIL_PLANT_RCV"],
                    "TR_GR_DETAIL_GL_ACCOUNT" => NULL,
                    "TR_GR_DETAIL_COST_CENTER" => NULL,
                    "TR_GR_DETAIL_EXP_DATE" => $row["expired_date"],
                    "TR_GR_DETAIL_IMG_QRCODE" => NULL,
                    "TR_GR_DETAIL_NOTES" => $row["TR_GR_DETAIL_NOTES"],
                    "TR_GR_DETAIL_PHOTO" => NULL,
                    "TR_GR_DETAIL_CREATED_BY" => session("id"),
                    "TR_GR_DETAIL_CREATED_TIMESTAMP" => $timestamp,
                    "TR_GR_DETAIL_UPDATED_BY" => NULL,
                    "TR_GR_DETAIL_UPDATED_TIMESTAMP" => NULL,
                    "TR_GR_DETAIL_QR_CODE_NUMBER" => $qr_code_number,
                    "TR_GR_DETAIL_SLOC" => $row["TR_GR_DETAIL_SLOC"],
                    "TR_GR_DETAIL_PO_DETAIL_ID" => $row["po_detail_id"],
                    "TR_GR_DETAIL_SAPLINE_ID" => $count
                ]
            ]);
        }

        $insert_res = std_insert([
            "table_name" => "TR_GR_DETAIL",
            "data" => $gr_material_arr
        ]);

        generate_gr_csv($gr_id, session("plant"));

        $request->session()->forget('gr_cart');

        return response()->json([
                    'message' => "GR Successfully Created"
                        ], 200);
    }
}

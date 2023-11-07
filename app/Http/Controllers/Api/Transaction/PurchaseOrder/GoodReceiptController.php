<?php

namespace App\Http\Controllers\Api\Transaction\PurchaseOrder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoodReceiptController extends Controller
{
    public function po_view(Request $request)
    {
        $plant_code = $request->user_data->plant;
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
                "field_name" => "TR_PO_DETAIL_PLANT_RCV",
                "operator" => "=",
                "value" => $plant_code
            ],
            [
                "field_name" => "TR_PO_HEADER_IS_DELETED",
                "operator" => "=",
                "value" => false
            ],
            [
                "field_name" => "TR_PO_HEADER_TYPE",
                "operator" => "!=",
                "value" => "ZRET"
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-")."01";
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

        $po_gr_data = std_get([
            "select" => ["TR_PO_HEADER.*","MA_VENDOR.MA_VENDOR_NAME"],
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
                ]
            ],
            "where" => $conditions,
            "distinct" => true
        ]);

        return response()->json([
            "status" => "OK",
            "data" => $po_gr_data
        ],200);
    }

    public function po_header(Request $request)
    {
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
        ],200);
    }

    public function po_detail(Request $request)
    {
        if (isset($selects)) {
            $select_fields = $selects;
        }
        else {
            $select_fields = ["*"];
        }

        $conditions = [];
        $detail_conditions = [];
        if (isset($request->gr_po_number)) {
            $conditions[0]["field_name"] = "TR_PO_HEADER_NUMBER";
            $conditions[0]["operator"] = "=";
            $conditions[0]["value"] = $request->gr_po_number;
            $conditions[1]["field_name"] = "TR_PO_HEADER_IS_DELETED";
            $conditions[1]["operator"] = "=";
            $conditions[1]["value"] = false;
            $detail_conditions[0]["field_name"] = "TR_PO_DETAIL_PO_HEADER_NUMBER";
            $detail_conditions[0]["operator"] = "=";
            $detail_conditions[0]["value"] = $request->gr_po_number;
        }

        $data = std_get([
            "select" => $select_fields,
            "table_name" => "TR_PO_HEADER",
            "where" => $conditions,
            "first_row" => true
        ]);

        if ($data == null) {
            return response()->json([
                "status" => "BAD_REQUEST",
                "message" => "PO Header Not Found"
            ],400);
        }


        $detail_data = std_get([
            "select" => $select_fields,
            "table_name" => "TR_PO_DETAIL",
            "where" => $detail_conditions,
        ]);

        $gr_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_HEADER",
            "where" => [
                [
                    "field_name" => "TR_GR_HEADER_PO_NUMBER",
                    "operator" => "=",
                    "value" => $request->gr_po_number
                ]
            ]
        ]);

        return response()->json([
            "status" => "OK",
            "data" => [
                "header_data" => $data,
                "detail_data" => $detail_data,
                "gr_data" => $gr_data
            ]
        ],200);
    }

    public function gr_material(Request $request)
    {
        $materials = std_get([
            "select" => ["TR_PO_DETAIL_ID","TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_MATERIAL_NAME"],
            "table_name" => "TR_PO_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_PO_DETAIL_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "value" => $request->po_number
                ]
            ]
        ]);
        if ($materials != null) {
            foreach ($materials as $row) {
                $materials_adj[] = [
                    "id" => $row["TR_PO_DETAIL_ID"],
                    "text" => $row["TR_PO_DETAIL_MATERIAL_CODE"]." - ".$row["TR_PO_DETAIL_MATERIAL_NAME"]
                ];
            }
            return response()->json([
                "status" => "OK",
                "data" => $materials_adj
            ],200);
        }
        else{
            return response()->json([
                "status" => "OK",
                "data" => []
            ],200);
        }
    }

    public function gr_material_info(Request $request)
    {
        $po_detail = std_get([
            "select" => ["TR_PO_DETAIL_PO_HEADER_NUMBER","TR_PO_DETAIL_SLOC","TR_PO_DETAIL_MATERIAL_CODE","TR_PO_DETAIL_PLANT_RCV","TR_PO_DETAIL_QTY_ORDER","TR_PO_DETAIL_QTY_DELIV","TR_PO_DETAIL_UOM"],
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
                ]
            ]
        ]);
        if ($gr_data != NULL) {
            foreach ($gr_data as $row) {
                $po_detail["TR_PO_DETAIL_QTY_ORDER"] -= $row["TR_GR_DETAIL_QTY"];
            }
        }

        $material = std_get([
            "select" => ["MA_MATL_BATCH"],
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
        }
        else{
            $qty_left = ($po_detail["TR_PO_DETAIL_QTY_ORDER"]);
        }
        return response()->json([
            "status" => "OK",
            "data" => [
                "sloc" => $po_detail["TR_PO_DETAIL_SLOC"],
                "qty_left" => $qty_left,
                "qty_left_uom" => $po_detail["TR_PO_DETAIL_UOM"],
                "batch_list" => $material
            ]
        ],200);
    }

    public function save_validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "po_number" => "required|max:255",
            "TR_GR_HEADER_PSTG_DATE" => "required",
            "TR_GR_HEADER_BOL" => "required|max:255",
            "TR_GR_HEADER_RECIPIENT" => "required|max:255",
            "TR_GR_HEADER_TXT" => "max:1000",
            "login_user_id" => "required|max:255",
            "login_plant_code" => "required|max:255",
        ]);

        $attributeNames = [
            "po_number" => "PO Number",
            "TR_GR_HEADER_PSTG_DATE" => "Posting Date",
            "TR_GR_HEADER_BOL" => "Bill Of Landing",
            "TR_GR_HEADER_RECIPIENT" => "Recipient",
            "TR_GR_HEADER_TXT" => "Note",
            "login_user_id" => "required|max:255",
            "login_plant_code" => "required|max:255",
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
        if ($request->gr_materials == NULL) {
            return response()->json([
                'message' => "GR Material Data Not Exist / Empty"
            ],500);
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
            ],500);
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

        foreach ($request->gr_materials as $row) {
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
                        "value" => $request->user_data->plant
                    ]
                ],
                "first_row" => true
            ]);

            if ($master_material == null) {
                return response()->json([
                    'message' => "Material Master Not Found"
                ], 500);
            }
        }

        $filename_header = null;
        if (isset($request->GR_PHOTO) && $request->GR_PHOTO != NULL && $request->GR_PHOTO != "") {
            $upload_dir = public_path()."/storage/GR_images/";
            $img = $request->GR_PHOTO;
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $image_data = base64_decode($img);
            $unique_id = uniqid();
            $file = $upload_dir.$unique_id.'.jpeg';
            $success = file_put_contents($file, $image_data);
            $filename_header = $unique_id.'.jpeg';
        }

        $gr_adjustment = false;
        if (isset($request->TR_GR_HEADER_IS_ADJUSTMENT) && $request->TR_GR_HEADER_IS_ADJUSTMENT == "true") {
            $gr_adjustment = true;
        }

        $gr_id = std_insert_get_id([
            "table_name" => "TR_GR_HEADER",
            "data" => [
                "TR_GR_HEADER_PO_NUMBER" => $request->po_number,
                "TR_GR_HEADER_PLANT_CODE" => $plant_code,
                "TR_GR_HEADER_SAP_DOC" => NULL,
                "TR_GR_HEADER_PSTG_DATE" => $request->TR_GR_HEADER_PSTG_DATE,
                "TR_GR_HEADER_DOC_DATE" => date("Y-m-d"),
                "TR_GR_HEADER_BOL" => $request->TR_GR_HEADER_BOL,
                "TR_GR_HEADER_TXT" => $request->TR_GR_HEADER_TXT,
                "TR_GR_HEADER_MVT_CODE" => 101,
                "TR_GR_HEADER_SAP_YEAR" => NULL,
                "TR_GR_HEADER_STATUS" => "PENDING",
                "TR_GR_HEADER_ERROR" => NULL,
                "TR_GR_HEADER_PHOTO" => $filename_header,
                "TR_GR_HEADER_RECIPIENT" => $request->TR_GR_HEADER_RECIPIENT,
                "TR_GR_HEADER_CREATED_BY" => $request->user_data->user_id,
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
            ],500);
        }
        
        $gr_material_arr = [];
        foreach ($request->gr_materials as $row) {
            $filename = null;
            if (isset($row["materialPhoto"]) && $row["materialPhoto"] != NULL && $row["materialPhoto"] != "") {
                $upload_dir = public_path()."/storage/GR_images/";
                $img = $row["materialPhoto"];
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $image_data = base64_decode($img);
                $unique_id = uniqid();
                $file = $upload_dir.$unique_id.'.jpeg';
                $success = file_put_contents($file, $image_data);
                $filename = $unique_id.'.jpeg';
            }
        
            $qr_code_number = $request->login_plant_code."-".uniqid();

            $po_detail = std_get([
                "select" => ["TR_PO_DETAIL_ID","TR_PO_DETAIL_SLOC","TR_PO_DETAIL_MATERIAL_CODE","TR_PO_DETAIL_PLANT_RCV","TR_PO_DETAIL_QTY_ORDER","TR_PO_DETAIL_QTY_DELIV","TR_PO_DETAIL_UOM"],
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
                        "value" => $request->user_data->plant
                    ]
                ],
                "first_row" => true
            ]);
            
            if ($master_material == NULL) {
                return response()->json([
                    'message' => "Material Master Not Found, but GR header already saved, please contact your admin"
                ],500);
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
                        "value" => $po_detail["TR_PO_DETAIL_UOM"]
                    ]
                ],
                "first_row" => true
            ]);

            if ($master_uom_base["MA_UOM_ID"] == $master_uom_comparison["MA_UOM_ID"]) {
                $base_qty = $row["qty"];
                $qty_left = $base_qty;
            }
            else{
                $base_qty = ($row["qty"] * $master_uom_comparison["MA_UOM_NUM"]) / $master_uom_comparison["MA_UOM_DEN"];
                $qty_left = $base_qty;
            }

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
                    "TR_GR_DETAIL_SLOC" => $row["TR_GR_DETAIL_SLOC"],
                    "TR_GR_DETAIL_UNLOADING_PLANT" => $po_detail["TR_PO_DETAIL_PLANT_RCV"],
                    "TR_GR_DETAIL_GL_ACCOUNT" => NULL,
                    "TR_GR_DETAIL_COST_CENTER" => NULL,
                    "TR_GR_DETAIL_EXP_DATE" => $row["expired_date"],
                    "TR_GR_DETAIL_IMG_QRCODE" => NULL,
                    "TR_GR_DETAIL_NOTES" => $row["TR_GR_DETAIL_NOTES"],
                    "TR_GR_DETAIL_PHOTO" => $filename,
                    "TR_GR_DETAIL_CREATED_BY" => $request->user_data->user_id,
                    "TR_GR_DETAIL_CREATED_TIMESTAMP" => $timestamp,
                    "TR_GR_DETAIL_UPDATED_BY" => NULL,
                    "TR_GR_DETAIL_UPDATED_TIMESTAMP" => NULL,
                    "TR_GR_DETAIL_QR_CODE_NUMBER" => $qr_code_number,
                    "TR_GR_DETAIL_PO_DETAIL_ID" => $po_detail["TR_PO_DETAIL_ID"]
                ]
            ]);
        }

        $insert_res = std_insert([
            "table_name" => "TR_GR_DETAIL",
            "data" => $gr_material_arr
        ]);
        
        generate_gr_csv($gr_id, $request->user_data->plant);

        return response()->json([
            "status" => "OK",
            "data" => "GR Successfully Created"
        ],200);
    }

    public function scan_qr(Request $request)
    {
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

        return response()->json([
            "status" => "OK",
            "data" => [
                "gr_detail_data" => $gr_detail_data
            ]
        ],200);
    }

    public function history_header(Request $request)
    {
        $plant_code = $request->user_data->plant;
        $conditions = [
            [
                "field_name" => "TR_GR_HEADER_CREATED_BY",
                "operator" => "=",
                "value" => $request->user_data->user_id
            ]
        ];

        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-")."01";
        }
        if (isset($request->start_date) && $request->start_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_GR_HEADER_CREATED_TIMESTAMP",
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
                    "field_name" => "TR_GR_HEADER_CREATED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => $request->end_date." 23:59:59"
                ]
            ]);
        }

        $gr_data = std_get([
            "select" => "TR_GR_HEADER.*",
            "table_name" => "TR_GR_HEADER",
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
            "select" => "TR_GR_HEADER.*",
            "table_name" => "TR_GR_HEADER",
            "where" => [
                [
                    "field_name" => "TR_GR_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GR_HEADER_ID
                ]
            ],
            "first_row" => true
        ]);

        if (isset($header_data["TR_GR_HEADER_PHOTO"]) && $header_data["TR_GR_HEADER_PHOTO"] != NULL && $header_data["TR_GR_HEADER_PHOTO"] != "") {
            $header_data["TR_GR_HEADER_PHOTO"] = asset('storage/GR_images/')."/".$header_data["TR_GR_HEADER_PHOTO"];
        }

        $detail_data = std_get([
            "select" => "TR_GR_DETAIL.*",
            "table_name" => "TR_GR_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $request->TR_GR_HEADER_ID
                ]
            ]
        ]);

        for ($i=0; $i < count($detail_data); $i++) { 
            if (isset($detail_data[$i]["TR_GR_DETAIL_PHOTO"]) && $detail_data[$i]["TR_GR_DETAIL_PHOTO"] != NULL && $detail_data[$i]["TR_GR_DETAIL_PHOTO"] != "") {
                $detail_data[$i]["TR_GR_DETAIL_PHOTO"] = asset('storage/GR_images/')."/".$detail_data[$i]["TR_GR_DETAIL_PHOTO"];
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
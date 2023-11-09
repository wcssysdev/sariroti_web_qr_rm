<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoodsController extends Controller {

    /**
     * 
     * @Todo Get Master Data Cost Center
     * @param Request $request
     * @return Array
     */
    public function get_cc(Request $request) {
        $plant_code = $request->user_data->plant;

        $data = std_get([
            "select" => ["MA_COSTCNTR_CODE", "MA_COSTCNTR_DESC"],
            "table_name" => "MA_COSTCNTR",
            "order_by" => [
                [
                    "field" => "MA_COSTCNTR_CODE",
                    "type" => "ASC",
                ]
            ],
        ]);

        $cc_arr = [];
        foreach ($data as $row) {
            $cc_arr = array_merge($cc_arr, [
                [
                    "id" => intval($row["MA_COSTCNTR_CODE"]),
                    "text" => intval($row["MA_COSTCNTR_CODE"]) . " - " . $row["MA_COSTCNTR_DESC"]
                ]
            ]);
        }
        return response()->json([
                    "status" => "OK",
                    "data" => $cc_arr
                        ], 200);
    }

    public function get_gl(Request $request) {
        $plant_code = $request->user_data->plant;

        $data = std_get([
            "select" => ["MA_GLACC_CODE", "MA_GLACC_DESC"],
            "table_name" => "MA_GLACC",
            "order_by" => [
                [
                    "field" => "MA_GLACC_CODE",
                    "type" => "ASC",
                ]
            ],
        ]);

        $gl_arr = [];
        foreach ($data as $row) {
            $gl_arr = array_merge($gl_arr, [
                [
                    "id" => intval($row["MA_GLACC_CODE"]),
                    "text" => intval($row["MA_GLACC_CODE"]) . " - " . $row["MA_GLACC_DESC"]
                ]
            ]);
        }
        return response()->json([
                    "status" => "OK",
                    "data" => $gl_arr
                        ], 200);
    }

    public function get_mvt_type(Request $request) {
        $plant_code = $request->user_data->plant;

        $data = std_get([
            "select" => ["MA_MVT_CODE", "MA_MVT_DESC"],
            "table_name" => "MA_MVT",
            "order_by" => [
                [
                    "field" => "MA_MVT_ID",
                    "type" => "ASC",
                ]
            ],
        ]);

        $mvt_arr = [];
        foreach ($data as $row) {
            $mvt_arr = array_merge($mvt_arr, [
                [
                    "id" => intval($row["MA_MVT_CODE"]),
                    "text" => intval($row["MA_MVT_CODE"]) . " - " . $row["MA_MVT_DESC"]
                ]
            ]);
        }
        return response()->json([
                    "status" => "OK",
                    "data" => $mvt_arr
                        ], 200);
    }

    public function get_uom(Request $request) {
        $plant_code = $request->user_data->plant;

        $data = std_get([
            "select" => ["MA_UOM_MATCODE", "MA_UOM_UOM", "MA_UOM_NUM", "MA_UOM_DEN"],
            "table_name" => "MA_UOM",
            "order_by" => [
                [
                    "field" => "MA_UOM_MATCODE",
                    "type" => "ASC",
                ]
            ],
        ]);

        $uom_arr = [];
        $i = 0;
        foreach ($data as $row) {
            $uom_arr = array_merge($uom_arr, [
                [
                    "id" => $i,
                    "mat_code" => ($row["MA_UOM_MATCODE"]),
                    "uom" => ($row["MA_UOM_UOM"]),
                    "num" => ($row["MA_UOM_NUM"]),
                    "denum" => ($row["MA_UOM_DEN"]),
                ]
            ]);
            $i++;
        }
        return response()->json([
                    "status" => "OK",
                    "data" => $uom_arr
                        ], 200);
    }

    public function get_list_mat(Request $request) {
        $plant_code = $request->user_data->plant;
        $data = std_get([
            "select" => ["MA_MATL_CODE", "MA_MATL_DESC", "MA_MATL_TYPE", "MA_MATL_GROUP", "MA_MATL_PLANT", "MA_MATL_UOM"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => $plant_code,
                ]
            ],
            "order_by" => [
                [
                    "field" => "MA_MATL_CODE",
                    "type" => "ASC",
                ]
            ],
            "distinct" => true,
            "first_row" => false
        ]);

        $mat_arr = [];
        foreach ($data as $row) {
            $mat_arr = array_merge($mat_arr, [
                [
                    "id" => intval($row["MA_MATL_CODE"]),
                    "mat_code" => ($row["MA_MATL_CODE"]),
                    "desc" => ($row["MA_MATL_DESC"]),
                    "type" => ($row["MA_MATL_TYPE"]),
                    "group" => ($row["MA_MATL_GROUP"]),
                    "uom" => ($row["MA_MATL_UOM"]),
                ]
            ]);
        }
        return response()->json([
                    "status" => "OK",
                    "data" => $mat_arr
                        ], 200);
    }

    public function get_list_po_gi(Request $request) {
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
                "field_name" => "TR_PO_HEADER_SUP_PLANT",
                "operator" => "=",
                "value" => $plant_code
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

        $po_gi_non_zret = std_get([
            "select" => "TR_PO_HEADER.*",
            "table_name" => "TR_PO_HEADER",
            "where" => $conditions
        ]);

        $conds = [
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
                "value" => $plant_code
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
            $conds = array_merge($conds, [
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
            $conds = array_merge($conds, [
                [
                    "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                    "operator" => "<=",
                    "value" => $request->end_date
                ]
            ]);
        }

        if (isset($request->plant_code) && $request->plant_code != "") {
            $conds = array_merge($conds, [
                [
                    "field_name" => "TR_PO_HEADER_SUP_PLANT",
                    "operator" => "=",
                    "value" => $request->plant_code
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
            "where" => $conds,
            "distinct" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => array_merge($po_gi_non_zret, $po_gi_data_zret)
                        ], 200);
    }

    public function get_list_po_gr(Request $request) {
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


        $dumpped = TRUE;
        if (isset($request->dump) && $request->dump != "") {
            $dumpped = $request->dump;
        }        
        $po_gr_data = std_get([
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
                ]
            ],
            "where" => $conditions,
            "dump" => $dumpped,
            "distinct" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => $po_gr_data
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
    
    public function get_materials(Request $request) {
        $materials = std_get([
            "select" => ["TR_PO_DETAIL_ID", "TR_PO_DETAIL_MATERIAL_CODE", "TR_PO_DETAIL_MATERIAL_NAME"],
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
                    "text" => $row["TR_PO_DETAIL_MATERIAL_CODE"] . " - " . $row["TR_PO_DETAIL_MATERIAL_NAME"]
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
        $plant_code = $request->user_data->plant;
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
        $gr_data = get_list_gr_data($po_detail["TR_PO_DETAIL_MATERIAL_CODE"], $plant_code);
//dd($gr_data);

        $select2 = [];
        foreach ($gr_data as $row) {
            $select2 = array_merge($select2, [
                [
                    "id" => $row["TR_GR_DETAIL_ID"],
                    "text" => $row["TR_GR_DETAIL_ID"] . " - " . number_format($row["TR_GR_DETAIL_LEFT_QTY"]) . " " . $row["TR_GR_DETAIL_BASE_UOM"]
                ]
            ]);
        }

        return response()->json([
                    "status" => "OK",
                    "data" => $select2
                        ], 200);
    } 
    
function get_list_gr_data($material_code, $plant_code) {
//    dd([$material_code,$plant_code]);
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

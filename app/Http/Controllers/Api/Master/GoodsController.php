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
            "where" => [
                [
                    "field_name" => "MA_MVT_CODE",
                    "operator" => "=",
                    "value" => '311',
                ],
//                [
//                    "field_name" => "MA_MVT_CODE",
//                    "operator" => "=",
//                    "value" => '411',
//                ]
            ],
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
        $startdate = date("Y-m-") . "01";
        if (!isset($request->start_date) || $request->start_date == "") {
            
        } else {
            $startdate = $request->start_date;
        }

        $enddate = date("Y-m-d");
        if (!isset($request->end_date) || $request->end_date == "") {
            
        } else {
            $enddate = $request->end_date;
        }
        $conditions = array_merge($conditions, [
            [
                "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                "operator" => ">=",
                "value" => $startdate
            ],
            [
                "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                "operator" => "<=",
                "value" => $enddate
            ],
            [
                "field_name" => "TR_PO_HEADER_IS_DELETED",
                "operator" => "=",
                "value" => false
            ],
        ]);
        $conditions = array_merge($conditions, [
        ]);

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
            "select" => [
                "TR_PO_HEADER_ID",
                "TR_PO_HEADER_NUMBER",
                "TR_PO_HEADER_TYPE",
                "TR_PO_HEADER_STATUS",
                "TR_PO_HEADER_VENDOR",
                "TR_PO_HEADER_SUP_PLANT",
                "TR_PO_HEADER_SAP_CREATED_DATE",
                "TR_PO_HEADER_FLAG",
                "TR_PO_HEADER_CREATED_BY",
                "TR_PO_HEADER_CREATED_TIMESTAMP",
                "TR_PO_DETAIL_MATERIAL_LINE_NUM",
                "TR_PO_DETAIL_MATERIAL_CODE",
                "TR_PO_DETAIL_MATERIAL_NAME",
                "TR_PO_DETAIL_MATERIAL_BATCH",
                "TR_PO_DETAIL_QTY_ORDER",
                "TR_PO_DETAIL_QTY_DELIV",
                "TR_PO_DETAIL_UOM",
                "TR_PO_DETAIL_SLOC",
                "TR_PO_DETAIL_PLANT_RCV",
                "TR_PO_DETAIL_ID",
                "MA_VENDOR.MA_VENDOR_NAME"
            ],
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
//            'dump' => true,
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
            ],
            [
                "field_name" => "TR_GR_HEADER_IS_ADJUSTMENT",
                "operator" => "=",
                "value" => false
            ],
            [
                "field_name" => "TR_GR_HEADER_IS_CANCELLED",
                "operator" => "!=",
                "value" => true
            ]
        ];

        $conds = array_merge($conds, [
            [
                "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                "operator" => ">=",
                "value" => $startdate
            ],
            [
                "field_name" => "TR_PO_HEADER_SAP_CREATED_DATE",
                "operator" => "<=",
                "value" => $enddate
            ],
            [
                "field_name" => "TR_PO_HEADER_IS_DELETED",
                "operator" => "=",
                "value" => false
            ]
        ]);

        if (isset($request->plant_code) && $request->plant_code != "") {
            $conds = array_merge($conds, [
                [
                    "field_name" => "TR_PO_HEADER_SUP_PLANT",
                    "operator" => "=",
                    "value" => $request->plant_code
                ]
            ]);
        }
//dd($conditions);
        $po_gi_data_zret = std_get([
            "select" => [
                "TR_PO_HEADER_ID",
                "TR_PO_HEADER_NUMBER",
                "TR_PO_HEADER_TYPE",
                "TR_PO_HEADER_STATUS",
                "TR_PO_HEADER_VENDOR",
                "TR_PO_HEADER_SUP_PLANT",
                "TR_PO_HEADER_SAP_CREATED_DATE",
                "TR_PO_HEADER_FLAG",
                "TR_PO_HEADER_CREATED_BY",
                "TR_PO_HEADER_CREATED_TIMESTAMP",
                "TR_PO_DETAIL_MATERIAL_LINE_NUM",
                "TR_PO_DETAIL_MATERIAL_CODE",
                "TR_PO_DETAIL_MATERIAL_NAME",
                "TR_PO_DETAIL_MATERIAL_BATCH",
                "TR_PO_DETAIL_QTY_ORDER",
                "TR_PO_DETAIL_QTY_DELIV",
                "TR_PO_DETAIL_UOM",
                "TR_PO_DETAIL_SLOC",
                "TR_PO_DETAIL_PLANT_RCV",
                "TR_PO_DETAIL_ID",
                "MA_VENDOR.MA_VENDOR_NAME"
            ],
            "table_name" => "TR_PO_HEADER",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_HEADER",
                    "on1" => "TR_GR_HEADER.TR_GR_HEADER_PO_NUMBER",
                    "operator" => "=",
                    "on2" => "TR_PO_HEADER.TR_PO_HEADER_NUMBER",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "TR_PO_DETAIL",
                    "on1" => "TR_PO_HEADER.TR_PO_HEADER_NUMBER",
                    "operator" => "=",
                    "on2" => "TR_PO_DETAIL.TR_PO_DETAIL_PO_HEADER_NUMBER",
                ],
                [
                    "join_type" => "multi_clause",
                    "table_name" => "TR_GR_DETAIL",
                    "clauses" => [
                        [
                            "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                            "operator" => "=",
                            "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                        ],
                        [
                            "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                            "operator" => "=",
                            "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID"
                        ]
                    ],
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

        $data_non_zret = [];
        foreach ($po_gi_non_zret as $non) {
            $details = [];
            foreach ($non as $nonkey => $nonval) {
                if (strpos($nonkey, 'DETAIL_') === FALSE) {
                    if (empty($data_non_zret[$non['TR_PO_HEADER_NUMBER']][$nonkey])) {
                        $data_non_zret[$non['TR_PO_HEADER_NUMBER']][$nonkey] = (($nonval) ? $nonval : "");
                    }
                } else {
                    $details[$nonkey] = $nonval;
                }
            }
            $data_non_zret[$non['TR_PO_HEADER_NUMBER']]['materials'][] = $details;
        }
        $data_zret = [];
        foreach ($po_gi_data_zret as $non) {
            $details = [];
            foreach ($non as $nonkey => $nonval) {
                if (strpos($nonkey, 'DETAIL_') === FALSE) {
                    if (empty($data_zret[$non['TR_PO_HEADER_NUMBER']][$nonkey])) {
                        $data_zret[$non['TR_PO_HEADER_NUMBER']][$nonkey] = (($nonval) ? $nonval : "");
                    }
                } else {
                    $details[$nonkey] = $nonval;
                }
            }
            $data_zret[$non['TR_PO_HEADER_NUMBER']]['materials'][] = $details;
        }
        return response()->json([
                    "status" => "OK",
                    "params" => ["plant_code" => $request->plant_code, "start_date" => $request->start_date, "end_date" => $request->end_date],
                    "data" => array_merge(array_filter($data_non_zret), array_filter($data_zret))
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
                    "text" => $row["TR_PO_DETAIL_MATERIAL_NAME"]
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

    public function get_tp_list_materials(Request $request) {
        $plant_code = $request->user_data->plant;
//        $materials = std_get([
//            "select" => ["MA_MATL_CODE as id","MA_MATL_DESC as text"],
//            "table_name" => "MA_MATL",
//            "where" => [
//                [
//                    "field_name" => "MA_MATL_PLANT",
//                    "operator" => "=",
//                    "value" => $plant_code
//                ]
//            ],
//            "order_by" => [
//                [
//                    "field" => "MA_MATL_DESC",
//                    "type" => "ASC",
//                ]
//            ],
//            "distinct" => true
//        ]);


        $materials = std_get([
            "select" => ["TR_GR_DETAIL_MATERIAL_CODE as id", "TR_GR_DETAIL_MATERIAL_NAME as text"],
            "table_name" => "TR_GR_DETAIL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_HEADER",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_HEADER.TR_GR_HEADER_ID",
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
                    "field" => "TR_GR_DETAIL_MATERIAL_NAME",
                    "type" => "ASC",
                ]
            ],
            "distinct" => true,
            "first_row" => false
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

    public function get_tp_list_gr_details_by_mat_code(Request $request) {

        $plant_code = $request->user_data->plant;

        $clause = [
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
                    "field" => "TR_GR_DETAIL_MATERIAL_NAME",
                    "type" => "ASC",
                ]
            ],
            "distinct" => true,
            "first_row" => false
        ];
        if (isset($request->material_code) && $request->material_code != "") {
            $clause['where'][] = [
                "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                "operator" => "=",
                "value" => $request->material_code
            ];
        }
        if (isset($request->movement_type) && $request->movement_type != "") {
            /**
             * 2023 Nov
             * 
             * sebelum Nov :
             * 1. ada TP movement code 311,411 dan 551
             * 
             * setelah CR GR/GI:
             * 1. hanya ada mvt code 311/TP,411
             */
//            $conditions = array_merge($conditions, [
//                [
//                    "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
//                    "operator" => "=",
//                    "value" => $request->movement_type
//                ]
//            ]);
        }
        $gr_data = std_get($clause);

        $select2 = [];
        if ($gr_data != null) {
            foreach ($gr_data as $row) {
                $batch = empty($row["TR_GR_DETAIL_SAP_BATCH"]) ? "-" : $row["TR_GR_DETAIL_SAP_BATCH"];
                $select2 = array_merge($select2, [
                    [
                        "id" => $row["TR_GR_DETAIL_ID"],
                        "text" => $row["TR_GR_DETAIL_ID"] . " | " . number_format($row["TR_GR_DETAIL_LEFT_QTY"]) . " " . $row["TR_GR_DETAIL_BASE_UOM"] . " | " . $batch . " | " . $row["TR_GR_DETAIL_EXP_DATE"] .' | '.$row['TR_GR_DETAIL_SLOC'],
                        "itemSplit" => $row["TR_GR_DETAIL_ID"] . "|" . $row["TR_GR_DETAIL_LEFT_QTY"] . " " . $row["TR_GR_DETAIL_BASE_UOM"] . "|" . $batch . "|" . $row["TR_GR_DETAIL_EXP_DATE"] .'|'.$row['TR_GR_DETAIL_SLOC']
                    ]
                ]);
            }
        }
        return response()->json([
                    "status" => "OK",
                    "data" => $select2
                        ], 200);
    }

    public function get_materials_for_type_y21(Request $request) {
        $plant_code = $request->user_data->plant;
        $materials = std_get([
            "select" => ["MA_MATL_CODE as id", "MA_MATL_DESC as text"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => $plant_code
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
                            ], 200);
        } else {
            return response()->json([
                        "status" => "OK",
                        "data" => [],
                            ], 200);
        }
    }

    public function get_material_batch_y21(Request $request) {
        $plant_code = $request->user_data->plant;
        $material = std_get([
            "select" => ["MA_MATL_ID as id", "MA_MATL_BATCH as text"],
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
                    "value" => $plant_code
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
                    "value" => $plant_code
                ]
            ],
            "first_row" => true
        ]);

        return response()->json([
                    "status" => "OK",
                    "data" => $material,
                    "base_uom" => $base_material["MA_MATL_UOM"]
                        ], 200);
    }
}

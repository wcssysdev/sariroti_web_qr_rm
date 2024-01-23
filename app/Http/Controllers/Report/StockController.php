<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockController extends Controller {

    public function get_opening_balance($plant_code, $start_date, $sloc_code = "", $mat_code = "", $is_detail = FALSE) {
        $statement = [
            "select" => [
                "LG_MATERIAL_CODE",
                "LG_MATERIAL_UOM", "TR_GR_DETAIL_SLOC",
                DB::raw('SUM("LG_MATERIAL_QTY") as ACTUAL_QTY')
            ],
            "table_name" => "LG_MATERIAL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "LG_MATERIAL_PLANT_CODE",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => "<",
                    "value" => $start_date
                ]
            ],
//            "dump" => TRUE,
            "order_by" => [
                [
                    "field" => "TR_GR_DETAIL_SLOC",
                    "type" => "ASC",
                ],
                [
                    "field" => "LG_MATERIAL_CODE",
                    "type" => "ASC",
                ],
            ],
            "group_by" => ["LG_MATERIAL_CODE", "LG_MATERIAL_UOM", "TR_GR_DETAIL_SLOC"]
        ];
        if ($is_detail) {
            $statement['select'][] = "TR_GR_DETAIL_MATERIAL_NAME";
            $statement['group_by'][] = "TR_GR_DETAIL_MATERIAL_NAME";
        }
        if ($sloc_code) {
            $statement['where'][] = [
                "field_name" => "TR_GR_DETAIL_SLOC",
                "operator" => "=",
                "value" => $sloc_code
            ];
        }
        if ($mat_code) {
            $statement['where'][] = [
                "field_name" => "LG_MATERIAL_CODE",
                "operator" => "=",
                "value" => $mat_code
            ];
        }
        $data = std_get($statement);
//        echo $this->db->last_query();die();
        return $data;
    }

    public function get_receipt_balance($plant_code, $start_date, $end_date, $sloc_code = "", $mat_code = "", $is_detail = FALSE) {
        $statement = [
            "select" => ["TR_GR_DETAIL_SLOC", "LG_MATERIAL_CODE", "LG_MATERIAL_UOM", DB::raw('SUM("LG_MATERIAL_QTY") as Qty')],
            "table_name" => "LG_MATERIAL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "LG_MATERIAL_PLANT_CODE",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => ">=",
                    "value" => $start_date
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => "<=",
                    "value" => $end_date
                ],
                [
                    "field_name" => "LG_MATERIAL_QTY",
                    "operator" => ">=",
                    "value" => 0
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_GR_DETAIL_SLOC",
                    "type" => "ASC",
                ],
                [
                    "field" => "LG_MATERIAL_CODE",
                    "type" => "ASC",
                ],
            ],
            "group_by" => ["TR_GR_DETAIL_SLOC", "LG_MATERIAL_CODE", "LG_MATERIAL_UOM"]
        ];
        if ($is_detail) {
            $statement['select'][] = "TR_GR_DETAIL_MATERIAL_NAME";
            $statement['group_by'][] = "TR_GR_DETAIL_MATERIAL_NAME";
        }
        if ($sloc_code) {
            $statement['where'][] = [
                "field_name" => "TR_GR_DETAIL_SLOC",
                "operator" => "=",
                "value" => $sloc_code
            ];
        }
        if ($mat_code) {
            $statement['where'][] = [
                "field_name" => "LG_MATERIAL_CODE",
                "operator" => "=",
                "value" => $mat_code
            ];
        }
        $data = std_get($statement);
        return $data;
    }

    public function get_issued_balance($plant_code, $start_date, $end_date, $sloc_code = "", $mat_code = "", $is_detail = FALSE) {
        $statement = [
            "select" => ["TR_GR_DETAIL_SLOC", "LG_MATERIAL_CODE", "LG_MATERIAL_UOM", DB::raw('SUM("LG_MATERIAL_QTY") as Qty')],
            "table_name" => "LG_MATERIAL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "LG_MATERIAL_PLANT_CODE",
                    "operator" => "=",
                    "value" => $plant_code
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => ">=",
                    "value" => $start_date
                ],
                [
                    "field_name" => "LG_MATERIAL_POSTING_DATE",
                    "operator" => "<=",
                    "value" => $end_date
                ],
                [
                    "field_name" => "LG_MATERIAL_QTY",
                    "operator" => "<",
                    "value" => 0
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_GR_DETAIL_SLOC",
                    "type" => "ASC",
                ],
                [
                    "field" => "LG_MATERIAL_CODE",
                    "type" => "ASC",
                ],
            ],
            "group_by" => ["TR_GR_DETAIL_SLOC", "LG_MATERIAL_CODE", "LG_MATERIAL_UOM"]
        ];
        if ($is_detail) {
            $statement['select'][] = "TR_GR_DETAIL_MATERIAL_NAME";
            $statement['group_by'][] = "TR_GR_DETAIL_MATERIAL_NAME";
        }
        if ($sloc_code) {
            $statement['where'][] = [
                "field_name" => "TR_GR_DETAIL_SLOC",
                "operator" => "=",
                "value" => $sloc_code
            ];
        }
        if ($mat_code) {
            $statement['where'][] = [
                "field_name" => "LG_MATERIAL_CODE",
                "operator" => "=",
                "value" => $mat_code
            ];
        }
        $data = std_get($statement);
        return $data;
    }

    private function get_mat_name($plant_code, $list_code) {
        if (empty($list_code)) {
            return [];
        }
        $data = std_get([
            "select" => ["MA_MATL_CODE", "MA_MATL_DESC", "MA_MATL_UOM"],
            "table_name" => "MA_MATL",
            "where" => [
                [
                    "field_name" => "MA_MATL_PLANT",
                    "operator" => "=",
                    "value" => $plant_code,
                ]
            ],
            "where_in" => [
                "field_name" => 'MA_MATL_CODE',
                "ids" => $list_code
            ],
            "distinct" => true,
            "first_row" => false
        ]);
        $res = [];
        foreach ($data as $dtmat) {
            $res[$dtmat['MA_MATL_CODE']]['DESC'] = $dtmat['MA_MATL_DESC'];
            $res[$dtmat['MA_MATL_CODE']]['UOM'] = $dtmat['MA_MATL_UOM'];
        }
        return $res;
    }

    public function get_gr_detail($plant_code, $start_date, $sloc_code = "", $mat_code = "", $page_header = TRUE, $end_date) {
        $statement = [
//            "select" => ["*"],
            "select" => ["TR_GR_DETAIL_ID", "TR_GR_DETAIL_MATERIAL_CODE", "TR_GR_DETAIL_MATERIAL_NAME", "TR_GR_DETAIL_SAP_BATCH", "TR_GR_DETAIL_LEFT_QTY", "TR_GR_DETAIL_BASE_UOM", "TR_GR_DETAIL_EXP_DATE"],
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
                    "field_name" => "TR_GR_DETAIL_IS_CANCELLED",
                    "operator" => "=",
                    "value" => false
                ],
            ],
            "dump" => FALSE,
            "order_by" => [
                [
                    "field" => "TR_GR_DETAIL_MATERIAL_CODE",
                    "type" => "ASC",
                ],
                [
                    "field" => "TR_GR_DETAIL_EXP_DATE",
                    "type" => "ASC",
                ]
            ]
        ];
        if ($sloc_code) {
            $statement['where'][] = [
                "field_name" => "TR_GR_DETAIL_SLOC",
                "operator" => "=",
                "value" => $sloc_code
            ];
        }
        if ($mat_code) {
            $statement['where'][] = [
                "field_name" => "TR_GR_DETAIL_MATERIAL_CODE",
                "operator" => "=",
                "value" => $mat_code
            ];
        }
        if ($page_header) {
            $statement['distinct'] = TRUE;
            $statement['select'][] = "TR_GR_DETAIL_SLOC";
            $statement['where'][] = [
                "field_name" => DB::raw('TO_DATE("TR_GR_HEADER_PSTG_DATE",\'YYYY-MM-DD\')'),
                "operator" => ">=",
                "value" => $start_date
            ];
            $statement['where'][] = [
                "field_name" => DB::raw('TO_DATE("TR_GR_HEADER_PSTG_DATE",\'YYYY-MM-DD\')'),
                "operator" => "<=",
                "value" => $end_date
            ];
        } else {
            $statement['distinct'] = TRUE;
            $statement['where'][] = [
                "field_name" => DB::raw('TO_DATE("TR_GR_HEADER_PSTG_DATE",\'YYYY-MM-DD\')'),
                "operator" => ">=",
                "value" => $start_date
            ];
            $statement['where'][] = [
                "field_name" => DB::raw('TO_DATE("TR_GR_HEADER_PSTG_DATE",\'YYYY-MM-DD\')'),
                "operator" => "<=",
                "value" => $end_date
            ];
        }
        $data = std_get($statement);
//        echo $this->db->last_query();die();

        return $data;
    }

    public function index(Request $request) {
        $open_balance = [];
        $receipt_balance = [];
        $issued_balance = [];
        $gr_detail = [];

        if (session("user_role") == 6) {
            $plant = std_get([
                "select" => ["MA_PLANT_CODE", "MA_PLANT_NAME"],
                "table_name" => "MA_PLANT",
                "order_by" => [
                    [
                        "field" => "MA_PLANT_CODE",
                        "type" => "ASC",
                    ]
                ],
                "first_row" => false
            ]);
        } else {
            $plant = std_get([
                "select" => ["MA_PLANT_CODE", "MA_PLANT_NAME"],
                "table_name" => "MA_PLANT",
                "where" => [
                    [
                        "field_name" => "MA_PLANT_CODE",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ],
                "order_by" => [
                    [
                        "field" => "MA_PLANT_CODE",
                        "type" => "ASC",
                    ]
                ],
                "first_row" => false
            ]);
        }
        if (empty($request->start_date)) {
            $request->start_date = "01" . date("/m/Y");
        }
        if (empty($request->end_date)) {
            $request->end_date = date("d/m/Y");
        }
        if ($request->plant_code != NULL && $request->start_date != NULL && $request->end_date != NULL) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }
            $open_balance = $this->get_opening_balance($request->plant_code, convert_to_y_m_d($request->start_date));
            $receipt_balance = $this->get_receipt_balance($request->plant_code, convert_to_y_m_d($request->start_date), convert_to_y_m_d($request->end_date));
            $issued_balance = $this->get_issued_balance($request->plant_code, convert_to_y_m_d($request->start_date), convert_to_y_m_d($request->end_date));
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($request->start_date), "", "", TRUE, convert_to_y_m_d($request->end_date));
            if (!empty($request->dump)) {
                echo "OB:" . json_encode($open_balance);
                echo "<br/>";
                echo "<br/>";
                echo "<br/>";
                echo "RB:" . json_encode($receipt_balance);
                echo "<br/>";
                echo "<br/>";
                echo "<br/>";
                echo "IB:" . json_encode($issued_balance);
                echo "<br/>";
                echo "<br/>";
                echo "<br/>";
                echo "GR:" . json_encode($gr_detail);
                echo "<br/>";
            }
            $list_mat_code = [];
            for ($i = 0; $i < count($open_balance); $i++) {
                $list_mat_code[$open_balance[$i]["LG_MATERIAL_CODE"]] = 0;
            }
            for ($i = 0; $i < count($receipt_balance); $i++) {
                $list_mat_code[$receipt_balance[$i]["LG_MATERIAL_CODE"]] = 0;
            }
            for ($i = 0; $i < count($issued_balance); $i++) {
                $list_mat_code[$issued_balance[$i]["LG_MATERIAL_CODE"]] = 0;
            }
            $nm_material = $this->get_mat_name($request->plant_code, array_keys($list_mat_code));
            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if (!empty($request->dump)) {
                    echo "RB-MAT-CODE:" . json_encode($receipt_balance[$i]["LG_MATERIAL_CODE"]);
                    echo "<br/>";
                }
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $receipt_balance[$i]["TR_GR_DETAIL_SLOC"];
                    if (!empty($nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]])) {
                        if (!empty($request->dump)) {
                            echo "RM-MAT-NAME:" . $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                            echo "<br/>";
                        }
                        $open_balance[$key]['mat_name'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                        $open_balance[$key]['LG_MATERIAL_UOM'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                    }
                } else {
                    $newarr = [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $receipt_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => "",
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ];
                    if (!empty($nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]])) {
                        $newarr['mat_name'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                        $newarr['LG_MATERIAL_UOM'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                    }
                    if (!empty($request->dump)) {
                        echo "RM-NEWARR-NAME:" . json_encode($newarr);
                        echo "<br/>";
                    }
                    array_push($open_balance, $newarr);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $issued_balance[$i]["TR_GR_DETAIL_SLOC"];
                } else {
                    if (!empty($request->dump)) {
                        echo json_encode($issued_balance[$i]["LG_MATERIAL_CODE"]);
                        echo "<br/>";
                    }
                    $newarr = [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $issued_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => "",
                        "LG_MATERIAL_UOM" => $issued_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "issued_qty" => $issued_balance[$i]["qty"]
                    ];
                    if (!empty($nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]])) {
                        $newarr['mat_name'] = $nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                        $newarr['LG_MATERIAL_UOM'] = $nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                    }
                    array_push($open_balance, $newarr);
                }
            }

            for ($i = 0; $i < count($open_balance); $i++) {
                if (!isset($open_balance[$i]["actual_qty"])) {
                    $open_balance[$i]["actual_qty"] = "0";
                }
                if (!isset($open_balance[$i]["issued_qty"])) {
                    $open_balance[$i]["issued_qty"] = "0";
                }
                if (!isset($open_balance[$i]["receipt_qty"])) {
                    $open_balance[$i]["receipt_qty"] = "0";
                }
                $open_balance[$i]["closing_qty"] = $open_balance[$i]["actual_qty"] + $open_balance[$i]["receipt_qty"] - abs($open_balance[$i]["issued_qty"]);
                $open_balance[$i]["gr_detail"] = [];
                if (!empty($request->dump)) {
                    echo "OP-MAT-CODE:" . json_encode($open_balance[$i]["LG_MATERIAL_CODE"]);
                    echo "<br/>";
                }
                if (!empty($nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]])) {
                    $open_balance[$i]['mat_name'] = $nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                    $open_balance[$i]['LG_MATERIAL_UOM'] = $nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                }
                for ($j = 0; $j < count($gr_detail); $j++) {
                    if (!empty($request->dump)) {
                        echo "GR-DETAIL-CODE:" . $gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"];
                        echo "<br/>";
                    }
                    if ($gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"] == $open_balance[$i]["LG_MATERIAL_CODE"]) {
                        $open_balance[$i]["gr_detail"][] = $gr_detail[$j];
                    }
                }

//                die();
            }
        }
        if (!empty($request->dump)) {
            echo "LIST-MAT-CODE:" . json_encode($list_mat_code);
            echo "<br/>";
            echo "<br/>";
            echo "NM-MAT:" . json_encode($nm_material);
            echo "<br/>";
            echo "<br/>";
            echo json_encode($open_balance);
            echo "<BR/>";
            dd();
        }
        return view('report/stock', [
            "open_balance" => $open_balance,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "plant" => $plant,
            "plant_selected" => $request->plant_code,
        ]);
    }

    public function excel(Request $request) {
        $open_balance = [];
        $receipt_balance = [];
        $issued_balance = [];
        $gr_detail = [];

        if (session("user_role") == 6) {
            $plant = std_get([
                "select" => ["MA_PLANT_CODE", "MA_PLANT_NAME"],
                "table_name" => "MA_PLANT",
                "order_by" => [
                    [
                        "field" => "MA_PLANT_CODE",
                        "type" => "ASC",
                    ]
                ],
                "first_row" => false
            ]);
        } else {
            $plant = std_get([
                "select" => ["MA_PLANT_CODE", "MA_PLANT_NAME"],
                "table_name" => "MA_PLANT",
                "where" => [
                    [
                        "field_name" => "MA_PLANT_CODE",
                        "operator" => "=",
                        "value" => session("plant")
                    ]
                ],
                "order_by" => [
                    [
                        "field" => "MA_PLANT_CODE",
                        "type" => "ASC",
                    ]
                ],
                "first_row" => false
            ]);
        }
        if (empty($request->start_date)) {
            $request->start_date = "01" . date("/m/Y");
        }
        if (empty($request->end_date)) {
            $request->end_date = date("d/m/Y");
        }
        if ($request->plant_code != NULL && $request->start_date != NULL && $request->end_date != NULL) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }
            $open_balance = $this->get_opening_balance($request->plant_code, convert_to_y_m_d($request->start_date));
            $receipt_balance = $this->get_receipt_balance($request->plant_code, convert_to_y_m_d($request->start_date), convert_to_y_m_d($request->end_date));
            $issued_balance = $this->get_issued_balance($request->plant_code, convert_to_y_m_d($request->start_date), convert_to_y_m_d($request->end_date));
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($request->start_date), "", "", TRUE, convert_to_y_m_d($request->end_date));

            $list_mat_code = [];
            for ($i = 0; $i < count($open_balance); $i++) {
                $list_mat_code[$open_balance[$i]["LG_MATERIAL_CODE"]] = 0;
            }
            for ($i = 0; $i < count($receipt_balance); $i++) {
                $list_mat_code[$receipt_balance[$i]["LG_MATERIAL_CODE"]] = 0;
            }
            for ($i = 0; $i < count($issued_balance); $i++) {
                $list_mat_code[$issued_balance[$i]["LG_MATERIAL_CODE"]] = 0;
            }
            $nm_material = $this->get_mat_name($request->plant_code, array_keys($list_mat_code));

            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $receipt_balance[$i]["TR_GR_DETAIL_SLOC"];
                    if (!empty($nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]])) {
                        $open_balance[$key]['mat_name'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                        $open_balance[$key]['LG_MATERIAL_UOM'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                    }
                } else {
                    $newarr = [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $receipt_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => "",
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ];
                    if (!empty($nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]])) {
                        $newarr['mat_name'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                        $newarr['LG_MATERIAL_UOM'] = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                    }

                    array_push($open_balance, $newarr);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $issued_balance[$i]["TR_GR_DETAIL_SLOC"];
                } else {
                    $newarr = [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $issued_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => "",
                        "LG_MATERIAL_UOM" => $issued_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "issued_qty" => $issued_balance[$i]["qty"]
                    ];
                    if (!empty($nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]])) {
                        $newarr['mat_name'] = $nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                        $newarr['LG_MATERIAL_UOM'] = $nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                    }
                    array_push($open_balance, $newarr);
                }
            }

            for ($i = 0; $i < count($open_balance); $i++) {
                if (!isset($open_balance[$i]["actual_qty"])) {
                    $open_balance[$i]["actual_qty"] = "0";
                }
                if (!isset($open_balance[$i]["issued_qty"])) {
                    $open_balance[$i]["issued_qty"] = "0";
                }
                if (!isset($open_balance[$i]["receipt_qty"])) {
                    $open_balance[$i]["receipt_qty"] = "0";
                }
                $open_balance[$i]["closing_qty"] = $open_balance[$i]["actual_qty"] + $open_balance[$i]["receipt_qty"] - abs($open_balance[$i]["issued_qty"]);
                $open_balance[$i]["gr_detail"] = [];
                if (!empty($nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]])) {
                    $open_balance[$i]['mat_name'] = $nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                    $open_balance[$i]['LG_MATERIAL_UOM'] = $nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]]["UOM"];
                }
                for ($j = 0; $j < count($gr_detail); $j++) {
                    if ($gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"] == $open_balance[$i]["LG_MATERIAL_CODE"]) {
                        $open_balance[$i]["gr_detail"][] = $gr_detail[$j];
                    }
                }
            }
        }
        $dtkmnt = date("YmdHis");
        $file_name = "stock_report_" . $request->plant_code . "_" . $dtkmnt . ".xlsx";
        $file_name_url = "storage/app/$file_name";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', "SLoc");
        $sheet->setCellValue('B1', "Material Code");
        $sheet->setCellValue('C1', "Material Name");
        $sheet->setCellValue('D1', "Opening Qty");
        $sheet->setCellValue('E1', "Total Receipt Qty");
        $sheet->setCellValue('F1', "Total Issued Qty");
        $sheet->setCellValue('G1', "Closing Qty");
        $counter = 2;
        $id = 1;
        for ($i = 0; $i < count($open_balance); $i++) {
            $row = $open_balance[$i];
            if (empty($row["mat_name"])) {
                $matname = "";
            } else {
                $matname = $row["mat_name"];
            }
            $sloc = "";
            if (!empty($row["TR_GR_DETAIL_SLOC"])) {
                $sloc = $row["TR_GR_DETAIL_SLOC"];
            } elseif (!empty($row["gr_detail"][0]["TR_GR_DETAIL_SLOC"])) {
                $sloc = $row["gr_detail"][0]["TR_GR_DETAIL_SLOC"];
            }
            $sheet->setCellValue('A' . ($counter), $sloc);

            $sheet->setCellValue('B' . ($counter), $open_balance[$i]["LG_MATERIAL_CODE"]);
            $sheet->setCellValue('C' . ($counter), $matname);
            $sheet->setCellValue('D' . ($counter), number_format(($open_balance[$i]["actual_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
            $sheet->setCellValue('E' . ($counter), number_format(($open_balance[$i]["receipt_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
            $sheet->setCellValue('F' . ($counter), number_format(abs($open_balance[$i]["issued_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
            $sheet->setCellValue('G' . ($counter), number_format(($open_balance[$i]["closing_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
            $counter++;
            $id++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name_url);
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return response()->download($file_name_url, $file_name, $headers)->deleteFileAfterSend(true);
    }

    public function detail(Request $request) {
        $open_balance = [];
        $receipt_balance = [];
        $issued_balance = [];
        $gr_detail = [];

        if ($request->plant_code != NULL && $request->start_date != NULL && $request->end_date != NULL) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }
            $sdate = html_entity_decode($request->start_date);
            $edate = html_entity_decode($request->end_date);

            $open_balance = $this->get_opening_balance($request->plant_code, convert_to_y_m_d($sdate), $request->sloc_code, $request->material_code, TRUE);
            $receipt_balance = $this->get_receipt_balance($request->plant_code, convert_to_y_m_d($sdate), convert_to_y_m_d($edate), $request->sloc_code, $request->material_code, TRUE);
            $issued_balance = $this->get_issued_balance($request->plant_code, convert_to_y_m_d($sdate), convert_to_y_m_d($edate), $request->sloc_code, $request->material_code, TRUE);

            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($sdate), $request->sloc_code, $request->material_code, FALSE, convert_to_y_m_d($edate));
            if (!empty($request->dump)) {
                echo json_encode($sdate);
                echo "<br/>";
                echo json_encode($edate);
                echo "<br/>";
                echo json_encode(convert_to_y_m_d($sdate));
                echo "<br/>";
                echo json_encode(convert_to_y_m_d($edate));
                echo "<br/>";
                echo json_encode($gr_detail);
                echo "<br/>";
            }
            $list_mat_code[$request->material_code] = 0;
            $nm_material = $this->get_mat_name($request->plant_code, array_keys($list_mat_code));

            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));

                $matname = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $receipt_balance[$i]["TR_GR_DETAIL_SLOC"];
                    $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"] = $matname;
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $receipt_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $matname,
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                $matname = $nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $issued_balance[$i]["TR_GR_DETAIL_SLOC"];
                    $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"] = $matname;
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $issued_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $matname,
                        "LG_MATERIAL_UOM" => $issued_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "issued_qty" => $issued_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($open_balance); $i++) {
                if (!isset($open_balance[$i]["actual_qty"])) {
                    $open_balance[$i]["actual_qty"] = "0";
                }
                if (!isset($open_balance[$i]["issued_qty"])) {
                    $open_balance[$i]["issued_qty"] = "0";
                }
                if (!isset($open_balance[$i]["receipt_qty"])) {
                    $open_balance[$i]["receipt_qty"] = "0";
                }
                if (!isset($open_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"])) {
                    $open_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"] = $nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                }
                $open_balance[$i]["closing_qty"] = $open_balance[$i]["actual_qty"] + $open_balance[$i]["receipt_qty"] - abs($open_balance[$i]["issued_qty"]);
                $open_balance[$i]["gr_detail"] = [];
                for ($j = 0; $j < count($gr_detail); $j++) {
                    if ($gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"] == $open_balance[$i]["LG_MATERIAL_CODE"]) {
                        $open_balance[$i]["gr_detail"][] = $gr_detail[$j];
                    }
                }
            }
        }
//        dd($open_balance);
        return view('report/stock_detail', [
            "open_balance" => $open_balance,
            "start_date" => htmlentities($request->start_date),
            "end_date" => htmlentities($request->end_date),
            "sloc" => $request->sloc_code,
            "mat_code" => $request->material_code,
            "plant_selected" => $request->plant_code,
            "date" => $request->date
        ]);
    }

    public function detail_excel(Request $request) {
        $open_balance = [];
        $receipt_balance = [];
        $issued_balance = [];
        $gr_detail = [];
        if ($request->plant_code != NULL && $request->start_date != NULL && $request->end_date != NULL) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }
            $sdate = html_entity_decode($request->start_date);
            $edate = html_entity_decode($request->end_date);

            $open_balance = $this->get_opening_balance($request->plant_code, convert_to_y_m_d($sdate), $request->sloc_code, $request->material_code, TRUE);
            $receipt_balance = $this->get_receipt_balance($request->plant_code, convert_to_y_m_d($sdate), convert_to_y_m_d($edate), $request->sloc_code, $request->material_code, TRUE);
            $issued_balance = $this->get_issued_balance($request->plant_code, convert_to_y_m_d($sdate), convert_to_y_m_d($edate), $request->sloc_code, $request->material_code, TRUE);
                
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($sdate), $request->sloc_code, $request->material_code, FALSE, convert_to_y_m_d($edate));

            $list_mat_code[$request->material_code] = 0;
            $nm_material = $this->get_mat_name($request->plant_code, array_keys($list_mat_code));

            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                $matname = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $receipt_balance[$i]["TR_GR_DETAIL_SLOC"];
                    $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"] = $matname;
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $receipt_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $matname,
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
                
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                $matname = $nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $issued_balance[$i]["TR_GR_DETAIL_SLOC"];
                    $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"] = $matname;
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $issued_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $matname,
                        "LG_MATERIAL_UOM" => $issued_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "issued_qty" => $issued_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($open_balance); $i++) {
                if (!isset($open_balance[$i]["actual_qty"])) {
                    $open_balance[$i]["actual_qty"] = "0";
                }
                if (!isset($open_balance[$i]["issued_qty"])) {
                    $open_balance[$i]["issued_qty"] = "0";
                }
                if (!isset($open_balance[$i]["receipt_qty"])) {
                    $open_balance[$i]["receipt_qty"] = "0";
                }
                if (!isset($open_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"])) {
                    $open_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"] = $nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                }
                $open_balance[$i]["closing_qty"] = $open_balance[$i]["actual_qty"] + $open_balance[$i]["receipt_qty"] - abs($open_balance[$i]["issued_qty"]);
                $open_balance[$i]["gr_detail"] = [];
                for ($j = 0; $j < count($gr_detail); $j++) {
                    if ($gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"] == $open_balance[$i]["LG_MATERIAL_CODE"]) {
                        $open_balance[$i]["gr_detail"][] = $gr_detail[$j];
                    }
                }
            }
        }
        $dtkmnt = date("YmdHis");
        $file_name = "stock_report_detail_" . $request->plant_code . "_" . $request->sloc_code . "_" . $request->material_code . "_" . $dtkmnt . ".xlsx";
        $file_name_url = "storage/app/$file_name";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', "SLoc");
        $sheet->setCellValue('B1', "Material Code");
        $sheet->setCellValue('C1', "Material Name");
        $sheet->setCellValue('D1', "Opening Qty");
        $sheet->setCellValue('E1', "Total Receipt Qty");
        $sheet->setCellValue('F1', "Total Issued Qty");
        $sheet->setCellValue('G1', "Closing Qty");
        $sheet->setCellValue('H1', "SAP Batch");
        $sheet->setCellValue('I1', "Expired Date");
        $sheet->setCellValue('J1', "Actual Qty");
        $counter = 2;
        $id = 1;
        for ($i = 0; $i < count($open_balance); $i++) {
            $row = $open_balance[$i];

            if (!empty($row["TR_GR_DETAIL_MATERIAL_NAME"])) {
                $matname = $row["TR_GR_DETAIL_MATERIAL_NAME"];
            } else {
                $matname = "";
            }

            $sloc = (empty($row["TR_GR_DETAIL_SLOC"]) ? "" : $row["TR_GR_DETAIL_SLOC"]);
//if (!empty($request->dump)) {
//                echo json_encode($row);
//                echo "<br/>";    
//                echo json_encode($row["gr_detail"]);
//                echo "<br/>";    
//                die();
//}            
            foreach ($row["gr_detail"] as $gr_detail) {
                $sheet->setCellValue('A' . ($counter), $sloc);
                $sheet->setCellValue('B' . ($counter), $row["LG_MATERIAL_CODE"]);
                $sheet->setCellValue('C' . ($counter), $matname);
                $sheet->setCellValue('D' . ($counter), number_format(($row["actual_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
                $sheet->setCellValue('E' . ($counter), number_format(($row["receipt_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
                $sheet->setCellValue('F' . ($counter), number_format(abs($row["issued_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
                $sheet->setCellValue('G' . ($counter), number_format(($row["closing_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);

                $sheet->setCellValue('H' . ($counter), $gr_detail["TR_GR_DETAIL_SAP_BATCH"]);
                $sheet->setCellValue('I' . ($counter), convert_to_web_dmy($gr_detail["TR_GR_DETAIL_EXP_DATE"]));
                $sheet->setCellValue('J' . ($counter), number_format($gr_detail["TR_GR_DETAIL_LEFT_QTY"], 2) . " " . $gr_detail["TR_GR_DETAIL_BASE_UOM"]);
                $counter++;
            }


            $counter++;
            $id++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name_url);
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return response()->download($file_name_url, $file_name, $headers)->deleteFileAfterSend(true);
    }

    public function detail_excel2(Request $request) {
        $open_balance = [];
        $receipt_balance = [];
        $issued_balance = [];
        $gr_detail = [];

        if ($request->plant_code != NULL && $request->start_date != NULL && $request->end_date != NULL) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }
            $sdate = html_entity_decode($request->start_date);
            $edate = html_entity_decode($request->end_date);

            $open_balance = $this->get_opening_balance($request->plant_code, convert_to_y_m_d($sdate), $request->sloc_code, $request->material_code, TRUE);
            $receipt_balance = $this->get_receipt_balance($request->plant_code, convert_to_y_m_d($sdate), convert_to_y_m_d($edate), $request->sloc_code, $request->material_code, TRUE);
            $issued_balance = $this->get_issued_balance($request->plant_code, convert_to_y_m_d($sdate), convert_to_y_m_d($edate), $request->sloc_code, $request->material_code, TRUE);
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($sdate), $request->sloc_code, $request->material_code, FALSE, convert_to_y_m_d($edate));

            $list_mat_code[$request->material_code] = 0;
            $nm_material = $this->get_mat_name($request->plant_code, array_keys($list_mat_code));

            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                $matname = $nm_material[$receipt_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $receipt_balance[$i]["TR_GR_DETAIL_SLOC"];
                    $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"] = $matname;
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $receipt_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $matname,
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                $matname = $nm_material[$issued_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                    $open_balance[$key]["TR_GR_DETAIL_SLOC"] = $issued_balance[$i]["TR_GR_DETAIL_SLOC"];
                    $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"] = $matname;
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_SLOC" => $issued_balance[$i]["TR_GR_DETAIL_SLOC"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $matname,
                        "LG_MATERIAL_UOM" => $issued_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "issued_qty" => $issued_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($open_balance); $i++) {
                if (!isset($open_balance[$i]["actual_qty"])) {
                    $open_balance[$i]["actual_qty"] = "0";
                }
                if (!isset($open_balance[$i]["issued_qty"])) {
                    $open_balance[$i]["issued_qty"] = "0";
                }
                if (!isset($open_balance[$i]["receipt_qty"])) {
                    $open_balance[$i]["receipt_qty"] = "0";
                }
                if (!isset($open_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"])) {
                    $open_balance[$i]["TR_GR_DETAIL_MATERIAL_NAME"] = $nm_material[$open_balance[$i]["LG_MATERIAL_CODE"]]["DESC"];
                }
                $open_balance[$i]["closing_qty"] = $open_balance[$i]["actual_qty"] + $open_balance[$i]["receipt_qty"] - abs($open_balance[$i]["issued_qty"]);
                $open_balance[$i]["gr_detail"] = [];
                for ($j = 0; $j < count($gr_detail); $j++) {
                    if ($gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"] == $open_balance[$i]["LG_MATERIAL_CODE"]) {
                        $open_balance[$i]["gr_detail"][] = $gr_detail[$j];
                    }
                }
            }
        }
        $dtkmnt = date("YmdHis");
        $file_name = "stock_report_detail_" . $request->plant_code . "_" . $request->sloc_code . "_" . $request->material_code . "_" . $dtkmnt . ".xlsx";
        $file_name_url = "storage/app/$file_name";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', "SLoc");
        $sheet->setCellValue('B1', "Material Code");
        $sheet->setCellValue('C1', "Material Name");
        $sheet->setCellValue('D1', "Opening Qty");
        $sheet->setCellValue('E1', "Total Receipt Qty");
        $sheet->setCellValue('F1', "Total Issued Qty");
        $sheet->setCellValue('G1', "Closing Qty");
        $sheet->setCellValue('H1', "SAP Batch");
        $sheet->setCellValue('I1', "Expired Date");
        $sheet->setCellValue('J1', "Actual Qty");
        $counter = 2;
        $id = 1;
        for ($i = 0; $i < count($open_balance); $i++) {
            $row = $open_balance[$i];

            if (!empty($row["TR_GR_DETAIL_MATERIAL_NAME"])) {
                $matname = $row["TR_GR_DETAIL_MATERIAL_NAME"];
            } else {
                $matname = "";
            }

            $sloc = (empty($row["TR_GR_DETAIL_SLOC"]) ? "" : $row["TR_GR_DETAIL_SLOC"]);
            $sheet->setCellValue('A' . ($counter), $sloc);
            $sheet->setCellValue('B' . ($counter), $row["LG_MATERIAL_CODE"]);
            $sheet->setCellValue('C' . ($counter), $matname);
            $sheet->setCellValue('D' . ($counter), number_format(($row["actual_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
            $sheet->setCellValue('E' . ($counter), number_format(($row["receipt_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
            $sheet->setCellValue('F' . ($counter), number_format(abs($row["issued_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
            $sheet->setCellValue('G' . ($counter), number_format(($row["closing_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);

            if (empty($row['gr_detail'])) {
                
            } else {
                $gr_detail0 = $row['gr_detail'][0];
                $sheet->setCellValue('H' . ($counter), $gr_detail0["TR_GR_DETAIL_SAP_BATCH"]);
                $sheet->setCellValue('I' . ($counter), convert_to_web_dmy($gr_detail0["TR_GR_DETAIL_EXP_DATE"]));
                $sheet->setCellValue('J' . ($counter), number_format($gr_detail0["TR_GR_DETAIL_LEFT_QTY"], 2) . " " . $gr_detail0["TR_GR_DETAIL_BASE_UOM"]);
            }
            $counter++;
            $id++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name_url);
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return response()->download($file_name_url, $file_name, $headers)->deleteFileAfterSend(true);
    }
}

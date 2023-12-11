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
                "LG_MATERIAL_CODE", "TR_GR_DETAIL_MATERIAL_NAME",
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
            "group_by" => ["TR_GR_DETAIL_SLOC", "LG_MATERIAL_CODE", "TR_GR_DETAIL_MATERIAL_NAME", "LG_MATERIAL_UOM"]
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
                    "operator" => "<",
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
                    "operator" => "<",
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

    public function get_gr_detail($plant_code, $start_date, $sloc_code = "", $mat_code = "") {
        $statement = [
//            "select" => ["*"],
            "select" => ["TR_GR_DETAIL_MATERIAL_CODE", "TR_GR_DETAIL_MATERIAL_NAME", "TR_GR_DETAIL_SAP_BATCH", "TR_GR_DETAIL_LEFT_QTY", "TR_GR_DETAIL_BASE_UOM", "TR_GR_DETAIL_EXP_DATE"],
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
                    "field_name" => "TR_GR_HEADER_PSTG_DATE",
                    "operator" => "=",
                    "value" => $start_date
                ],
                [
                    "field_name" => "TR_GR_DETAIL_IS_CANCELLED",
                    "operator" => "=",
                    "value" => false
                ],
            ],
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
        $data = std_get($statement);
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
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($request->end_date), convert_to_y_m_d($request->end_date));
            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                } else {
//                    dd($receipt_balance);
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
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
                $open_balance[$i]["closing_qty"] = $open_balance[$i]["actual_qty"] + $open_balance[$i]["receipt_qty"] - abs($open_balance[$i]["issued_qty"]);
                $open_balance[$i]["gr_detail"] = [];
                for ($j = 0; $j < count($gr_detail); $j++) {
                    if ($gr_detail[$j]["TR_GR_DETAIL_MATERIAL_CODE"] == $open_balance[$i]["LG_MATERIAL_CODE"]) {
                        $open_balance[$i]["gr_detail"][] = $gr_detail[$j];
                    }
                }
//                die();
            }
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
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($request->end_date), convert_to_y_m_d($request->end_date));

            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
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
        $file_name = "stock_report_" . $request->plant_code . "_" . $dtkmnt . ".xlsx";
        $file_name_url = "storage/app/$file_name";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', "Storage Location");
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
            $mat_name = '';
            if (!empty($open_balance[$i]['gr_detail'][0])) {
                $mat_name = $row['gr_detail'][0]['TR_GR_DETAIL_MATERIAL_NAME'];
            }
            $sloc = (empty($open_balance[$i]["TR_GR_DETAIL_SLOC"]) ? "" : $open_balance[$i]["TR_GR_DETAIL_SLOC"]);
            $sheet->setCellValue('A' . ($counter), $sloc);

            $sheet->setCellValue('B' . ($counter), $open_balance[$i]["LG_MATERIAL_CODE"]);
            $sheet->setCellValue('C' . ($counter), $mat_name);
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
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($edate), $request->sloc_code, $request->material_code);
            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
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
            $gr_detail = $this->get_gr_detail($request->plant_code, convert_to_y_m_d($edate), $request->sloc_code, $request->material_code);

            for ($i = 0; $i < count($receipt_balance); $i++) {
                $key = array_search($receipt_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["receipt_qty"] = $receipt_balance[$i]["qty"];
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $receipt_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
                        "LG_MATERIAL_UOM" => $receipt_balance[$i]["LG_MATERIAL_UOM"],
                        "actual_qty" => "0",
                        "receipt_qty" => $receipt_balance[$i]["qty"]
                    ]);
                }
            }

            for ($i = 0; $i < count($issued_balance); $i++) {
                $key = array_search($issued_balance[$i]["LG_MATERIAL_CODE"], array_column($open_balance, 'LG_MATERIAL_CODE'));
                if ($key !== false) {
                    $open_balance[$key]["issued_qty"] = $issued_balance[$i]["qty"];
                } else {
                    array_push($open_balance, [
                        "LG_MATERIAL_CODE" => $issued_balance[$i]["LG_MATERIAL_CODE"],
                        "TR_GR_DETAIL_MATERIAL_NAME" => $open_balance[$key]["TR_GR_DETAIL_MATERIAL_NAME"],
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

        $sheet->setCellValue('A1', "Storage Location");
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
                $sloc = (empty($row["TR_GR_DETAIL_SLOC"]) ? "" : $row["TR_GR_DETAIL_SLOC"]);
                $sheet->setCellValue('A' . ($counter), $sloc);
                $sheet->setCellValue('B' . ($counter), $row["LG_MATERIAL_CODE"]);
                $sheet->setCellValue('C' . ($counter), $row['TR_GR_DETAIL_MATERIAL_NAME']);
                $sheet->setCellValue('D' . ($counter), number_format(($row["actual_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
                $sheet->setCellValue('E' . ($counter), number_format(($row["receipt_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
                $sheet->setCellValue('F' . ($counter), number_format(abs($row["issued_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);
                $sheet->setCellValue('G' . ($counter), number_format(($row["closing_qty"]), 2) . " " . $row["LG_MATERIAL_UOM"]);

                if (empty($row['gi_details'])) {
                    
                } else {
                    $gr_detail0 = $row['gi_details'][0];
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

<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GoodMovementController extends Controller {

    public function index(Request $request) {
        $data = [];

        if (empty($request->start_date)) {
            $request->start_date = "01" . date("/m/Y");
        }
        if (empty($request->end_date)) {
            $request->end_date = date("d/m/Y");
        }

        if ($request->plant_code != NULL && $request->start_date && $request->end_date) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }

            $data = std_get([
                "select" => [
                    "TR_GR_HEADER_DOC_DATE",
                    "TR_GR_HEADER_PO_NUMBER",
                    "TR_GR_DETAIL_BASE_UOM",
                    "TR_GR_HEADER_SAP_DOC",
                    "TR_GR_DETAIL_MATERIAL_NAME",
                    "TR_GR_DETAIL_BASE_UOM",
                    "TR_GR_DETAIL_SAP_BATCH",
                    "LG_MATERIAL_PLANT_CODE",
                    "LG_MATERIAL_CODE",
                    "LG_MATERIAL_POSTING_DATE",
                    "LG_MATERIAL_POSTING_DATE",
                    "LG_MATERIAL_MVT_TYPE",
                    "LG_MATERIAL_QTY",
                    DB::raw('TO_CHAR("LG_MATERIAL_CREATED_TIMESTAMP",\'DD/MM/YYYY\') as "ENTRY_DATE"'),
                    DB::raw('TO_CHAR("LG_MATERIAL_CREATED_TIMESTAMP",\'HH24::II:SS\') as "ENTRY_TIME"'),
                    "MA_SLOC_CODE",
                    "MA_SLOC.MA_SLOC_DESC",
                    "MA_USRACC_FULL_NAME",
//                    "TR_GR_HEADER.TR_GR_HEADER_SAP_DOC",
                ],
                "table_name" => "LG_MATERIAL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_HEADER",
                        "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "MA_USRACC",
                        "on1" => DB::raw('"LG_MATERIAL_CREATED_BY"::integer'),
                        "operator" => "=",
                        "on2" => "MA_USRACC.MA_USRACC_ID",
                    ],
                    [
                        "join_type" => "multi_clause",
                        "table_name" => "MA_SLOC",
                        "clauses" => [
                            [
                                "on1" => "MA_SLOC.MA_SLOC_CODE",
                                "operator" => "=",
                                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_SLOC"
                            ],
                            [
                                "on1" => "MA_SLOC.MA_SLOC_PLANT",
                                "operator" => "=",
                                "on2" => "LG_MATERIAL.LG_MATERIAL_PLANT_CODE"
                            ]
                        ]
                    ]
                ],
//                "dump" => true,
                "where" => [
                    [
                        "field_name" => "LG_MATERIAL_PLANT_CODE",
                        "operator" => "=",
                        "value" => $request->plant_code
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => ">=",
                        "value" => convert_to_y_m_d($request->start_date)
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => "<=",
                        "value" => convert_to_y_m_d($request->end_date)
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
                        "field" => "MA_SLOC_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "TR_GR_DETAIL_SAP_BATCH",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CREATED_TIMESTAMP",
                        "type" => "ASC",
                    ],
                ],
            ]);
        }

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

        return view('report/good_movement_mb51', [
            "data" => $data,
            "start" => $request->start_date,
            "end" => $request->end_date,
            "plant_selected" => $request->plant_code,
            "sloc_selected" => $request->sloc_code,
            "plant" => $plant
        ]);
    }

    public function excel(Request $request) {
        $data = [];
        if ($request->plant_code != NULL && $request->start_date && $request->end_date) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }

            $data = std_get([
                "select" => [
                    "TR_GR_HEADER_DOC_DATE",
                    "TR_GR_HEADER_PO_NUMBER",
                    "TR_GR_DETAIL_BASE_UOM",
                    "TR_GR_HEADER_SAP_DOC",
                    "TR_GR_DETAIL_MATERIAL_NAME",
                    "TR_GR_DETAIL_BASE_UOM",
                    "TR_GR_DETAIL_SAP_BATCH",
                    "LG_MATERIAL_PLANT_CODE",
                    "LG_MATERIAL_CODE",
                    "LG_MATERIAL_POSTING_DATE",
                    "LG_MATERIAL_POSTING_DATE",
                    "LG_MATERIAL_MVT_TYPE",
                    "LG_MATERIAL_QTY",
                    DB::raw('TO_CHAR("LG_MATERIAL_CREATED_TIMESTAMP",\'DD/MM/YYYY\') as "ENTRY_DATE"'),
                    DB::raw('TO_CHAR("LG_MATERIAL_CREATED_TIMESTAMP",\'HH24::II:SS\') as "ENTRY_TIME"'),
                    "MA_SLOC_CODE",
                    "MA_SLOC.MA_SLOC_DESC",
                    "MA_USRACC_FULL_NAME",
//                    "TR_GR_HEADER.TR_GR_HEADER_SAP_DOC",
                ],
                "table_name" => "LG_MATERIAL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_HEADER",
                        "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "MA_USRACC",
                        "on1" => DB::raw('"LG_MATERIAL_CREATED_BY"::integer'),
                        "operator" => "=",
                        "on2" => "MA_USRACC.MA_USRACC_ID",
                    ],
                    [
                        "join_type" => "multi_clause",
                        "table_name" => "MA_SLOC",
                        "clauses" => [
                            [
                                "on1" => "MA_SLOC.MA_SLOC_CODE",
                                "operator" => "=",
                                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_SLOC"
                            ],
                            [
                                "on1" => "MA_SLOC.MA_SLOC_PLANT",
                                "operator" => "=",
                                "on2" => "LG_MATERIAL.LG_MATERIAL_PLANT_CODE"
                            ]
                        ]
                    ]
                ],
//                "dump" => true,
                "where" => [
                    [
                        "field_name" => "LG_MATERIAL_PLANT_CODE",
                        "operator" => "=",
                        "value" => $request->plant_code
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => ">=",
                        "value" => convert_to_y_m_d($request->start_date)
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => "<=",
                        "value" => convert_to_y_m_d($request->end_date)
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
                        "field" => "MA_SLOC_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "TR_GR_DETAIL_SAP_BATCH",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CREATED_TIMESTAMP",
                        "type" => "ASC",
                    ],
                ],
            ]);
        }


        $file_name_url = "storage/app/good_movement_" . date("YmdHis") . ".xlsx";
        $file_name = "good_movement_" . date("YmdHis") . ".xlsx";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue("A1", "Material");
        $sheet->setCellValue("B1", "Material Desc");
        $sheet->setCellValue("C1", "Quantity in Unit Entry");
        $sheet->setCellValue("D1", "Entri Unit");
        $sheet->setCellValue("E1", "Posting Date");
        $sheet->setCellValue("F1", "Doc. Date");
        $sheet->setCellValue("G1", "PO");
        $sheet->setCellValue("H1", "Plant");
        $sheet->setCellValue("I1", "Batch");
        $sheet->setCellValue("J1", "Sloc");
        $sheet->setCellValue("K1", "Mvt Type");
        $sheet->setCellValue("L1", "Mat. Doc.");
        $sheet->setCellValue("M1", "Entry Date");
        $sheet->setCellValue("N1", "Time");
        $sheet->setCellValue("O1", "User");

        $counter = 2;
        $id = 1;
        for ($i = 0; $i < count($data); $i++) {
            $sheet->setCellValue('A' . ($counter), $data[$i]["LG_MATERIAL_CODE"]);
            $sheet->setCellValue('B' . ($counter), $data[$i]["TR_GR_DETAIL_MATERIAL_NAME"]);
            $sheet->setCellValue('C' . ($counter), number_format($data[$i]["LG_MATERIAL_QTY"], 2));
            $sheet->setCellValue('D' . ($counter), $data[$i]["TR_GR_DETAIL_BASE_UOM"]);
            $sheet->setCellValue('E' . ($counter), $data[$i]["LG_MATERIAL_POSTING_DATE"]);
            $sheet->setCellValue('F' . ($counter), $data[$i]["TR_GR_HEADER_DOC_DATE"]);
            $sheet->setCellValue('G' . ($counter), $data[$i]["TR_GR_HEADER_PO_NUMBER"]);
            $sheet->setCellValue('H' . ($counter), $data[$i]["LG_MATERIAL_PLANT_CODE"]);
            $sheet->setCellValue('I' . ($counter), $data[$i]["TR_GR_DETAIL_SAP_BATCH"]);
            $sheet->setCellValue('J' . ($counter), $data[$i]["MA_SLOC_CODE"]);
            $sheet->setCellValue('K' . ($counter), $data[$i]["LG_MATERIAL_MVT_TYPE"]);
            $sheet->setCellValue('L' . ($counter), $data[$i]["TR_GR_HEADER_SAP_DOC"]);
            $sheet->setCellValue('M' . ($counter), $data[$i]["ENTRY_DATE"]);
            $sheet->setCellValue('N' . ($counter), $data[$i]["ENTRY_TIME"]);
            $sheet->setCellValue('O' . ($counter), $data[$i]["MA_USRACC_FULL_NAME"]);
            $counter++;
            $id++;
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name_url);
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return response()->download($file_name_url, $file_name, $headers)->deleteFileAfterSend(true);
//        return response()->file($file_name);
    }

    public function detail(Request $request) {
        $data = [];
        if ($request->plant_code != NULL && $request->start_date && $request->end_date) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }

            $data = std_get([
                "select" => [
                    "LG_MATERIAL_CODE",
                    "TR_GR_DETAIL_BASE_UOM",
                    "TR_GR_DETAIL_BASE_UOM",
                    "MA_SLOC_CODE", "MA_SLOC.MA_SLOC_DESC",
                    "LG_MATERIAL_MVT_TYPE",
                    "LG_MATERIAL_QTY", "TR_GR_DETAIL.*", "LG_MATERIAL_CREATED_TIMESTAMP",
//                    "TR_GR_HEADER.TR_GR_HEADER_SAP_DOC",
                ],
                "table_name" => "LG_MATERIAL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_HEADER",
                        "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                    ],
                    [
                        "join_type" => "multi_clause",
                        "table_name" => "MA_SLOC",
                        "clauses" => [
                            [
                                "on1" => "MA_SLOC.MA_SLOC_CODE",
                                "operator" => "=",
                                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_SLOC"
                            ],
                            [
                                "on1" => "MA_SLOC.MA_SLOC_PLANT",
                                "operator" => "=",
                                "on2" => "LG_MATERIAL.LG_MATERIAL_PLANT_CODE"
                            ]
                        ]
                    ]
                ],
//                "dump" => true,
                "where" => [
                    [
                        "field_name" => "LG_MATERIAL_PLANT_CODE",
                        "operator" => "=",
                        "value" => $request->plant_code
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => ">=",
                        "value" => convert_to_y_m_d($request->start_date)
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => "<=",
                        "value" => convert_to_y_m_d($request->end_date)
                    ],
                    [
                        "field_name" => "LG_MATERIAL_CODE",
                        "operator" => "=",
                        "value" => $request->material_code
                    ],
                    [
                        "field_name" => "TR_GR_DETAIL_SLOC",
                        "operator" => "=",
                        "value" => $request->sloc_code
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
                        "field" => "MA_SLOC_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "TR_GR_DETAIL_SAP_BATCH",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CREATED_TIMESTAMP",
                        "type" => "ASC",
                    ],
                ],
            ]);
        }

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

//        $data_collection = $data->mapToGroups(function ($item, $key) {
//        return [$item['LG_MATERIAL_CODE'] => $item];
//    });
//        dd($data);
        return view('report/good_movement_detail', [
            "data" => $data,
            "sdate" => $request->start_date,
            "edate" => $request->end_date,
            "plant_selected" => $request->plant_code,
            "sloc_selected" => $request->sloc_code,
            "mat_selected" => $request->material_code,
            "plant" => $plant
        ]);
    }

    public function detail_excel(Request $request) {
        $data = [];
        if ($request->plant_code != NULL && $request->start_date && $request->end_date) {
            if (session("user_role") != 6) {
                if ($request->plant_code != session("plant")) {
                    abort(404);
                }
            }

            $data = std_get([
                "select" => [
                    "LG_MATERIAL_CODE",
                    "TR_GR_DETAIL_BASE_UOM",
                    "LG_MATERIAL_POSTING_DATE",
                    "MA_SLOC_CODE", "MA_SLOC.MA_SLOC_DESC",
                    "LG_MATERIAL_MVT_TYPE",
                    "LG_MATERIAL_QTY", "TR_GR_DETAIL.*", "LG_MATERIAL_CREATED_TIMESTAMP",
//                    "TR_GR_HEADER.TR_GR_HEADER_SAP_DOC",
                ],
                "table_name" => "LG_MATERIAL",
                "join" => [
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_DETAIL",
                        "on1" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                        "operator" => "=",
                        "on2" => "LG_MATERIAL.LG_MATERIAL_GR_DETAIL_ID",
                    ],
                    [
                        "join_type" => "inner",
                        "table_name" => "TR_GR_HEADER",
                        "on1" => "TR_GR_HEADER.TR_GR_HEADER_ID",
                        "operator" => "=",
                        "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_HEADER_ID",
                    ],
                    [
                        "join_type" => "multi_clause",
                        "table_name" => "MA_SLOC",
                        "clauses" => [
                            [
                                "on1" => "MA_SLOC.MA_SLOC_CODE",
                                "operator" => "=",
                                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_SLOC"
                            ],
                            [
                                "on1" => "MA_SLOC.MA_SLOC_PLANT",
                                "operator" => "=",
                                "on2" => "LG_MATERIAL.LG_MATERIAL_PLANT_CODE"
                            ]
                        ]
                    ]
                ],
//                "dump" => true,
                "where" => [
                    [
                        "field_name" => "LG_MATERIAL_PLANT_CODE",
                        "operator" => "=",
                        "value" => $request->plant_code
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => ">=",
                        "value" => convert_to_y_m_d($request->start_date)
                    ],
                    [
                        "field_name" => "LG_MATERIAL_POSTING_DATE",
                        "operator" => "<=",
                        "value" => convert_to_y_m_d($request->end_date)
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
                        "field" => "MA_SLOC_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CODE",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "TR_GR_DETAIL_SAP_BATCH",
                        "type" => "ASC",
                    ],
                    [
                        "field" => "LG_MATERIAL_CREATED_TIMESTAMP",
                        "type" => "ASC",
                    ],
                ],
            ]);
        }

        $file_name_url = "storage/app/good_movement_detail_" . date("YmdHis") . ".xlsx";
        $file_name = "good_movement_detail_" . date("YmdHis") . ".xlsx";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', "Storage Location");
        $sheet->setCellValue('B1', "Material Code");
        $sheet->setCellValue('C1', "Material Name");
        $sheet->setCellValue('D1', "Expired Date");
        $sheet->setCellValue('E1', "Batch ID");
        $sheet->setCellValue('F1', "Status");
        $sheet->setCellValue('G1', "Qty");
        $sheet->setCellValue('H1', "UoM");
        $sheet->setCellValue('I1', "Movement Type");
        $sheet->setCellValue('J1', "Transaction Date");
        $counter = 2;
        $id = 1;
        for ($i = 0; $i < count($data); $i++) {
            $sheet->setCellValue('A' . ($counter), $data[$i]["MA_SLOC_CODE"]);

            $sheet->setCellValue('B' . ($counter), $data[$i]["LG_MATERIAL_CODE"]);
            $sheet->setCellValue('C' . ($counter), $data[$i]["TR_GR_DETAIL_MATERIAL_NAME"]);
            $sheet->setCellValue('D' . ($counter), convert_to_web_dmy($data[$i]["TR_GR_DETAIL_EXP_DATE"]));
            $sheet->setCellValue('E' . ($counter), $data[$i]["TR_GR_DETAIL_SAP_BATCH"]);
            if ($data[$i]["LG_MATERIAL_QTY"] >= 0) {
                $status = 'IN';
            } else {
                $status = 'OUT';
            }
            $sheet->setCellValue('F' . ($counter), $status);
            $sheet->setCellValue('G' . ($counter), number_format(abs($data[$i]["LG_MATERIAL_QTY"]), 2));
            $sheet->setCellValue('H' . ($counter), $data[$i]["TR_GR_DETAIL_BASE_UOM"]);
            $sheet->setCellValue('I' . ($counter), $data[$i]["LG_MATERIAL_MVT_TYPE"]);
            $sheet->setCellValue('J' . ($counter), $data[$i]["LG_MATERIAL_CREATED_TIMESTAMP"]);
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

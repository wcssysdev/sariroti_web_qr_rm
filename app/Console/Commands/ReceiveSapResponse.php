<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReceiveSapResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:receive_transaction_sap_response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive GI / GR / TP / Cancellation SAP Response';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $base_url = storage_path('app/public/OUTGOING2/MIGO/');
        $base_url_success = storage_path('app/public/OUTGOING2/MIGO_BACKUP/');
        $files = glob($base_url."MIGO_OUT_*.csv");
        usort(
            $files, 
            function($a,$b){
                return basename($a) <=> basename($b);
            }
        );
        for ($i=0; $i < count($files); $i++) {
            $arr_data = null;
            $file = fopen($files[$i], "r");
            $is_zret = false;
            while (! feof($file)) {
                $file_rows = fgetcsv($file, null, ";");
                $error_message = [];
                if ($file_rows != false) {
                    if ($file_rows[0] != null && $file_rows[0] != "" && $file_rows[0] != "EOF") {
                        $res_status = NULL;
                        if ($file_rows[4] == "S") {
                            $res_status = "SUCCESS";
                        }
                        else if ($file_rows[4] == "E") {
                            $res_status = "ERROR";
                        }
                        else if ($file_rows[4] == "W") {
                            $res_status = "WARNING";
                        }
                        else{
                            $res_status = $file_rows[4];
                        }

                        if (isset($file_rows[6]) && $file_rows[6] == "ZRET") {
                            $is_zret = true;
                        }
                        $error_message = array_merge($error_message, [
                            $file_rows[5]
                        ]);
                        $arr_data = [
                            "id_web" => $file_rows[0],
                            "transaction_code" => $file_rows[1],
                            "sap_number" => $file_rows[2],
                            "sap_doc_year" => $file_rows[3],
                            "result" => $res_status,
                            "sap_message" => implode("|",$error_message)
                        ];
                    }
                }
            }
            
            if ($is_zret === true) {
                $update_res = std_update([
                    "table_name" => "TR_GI_SAPHEADER",
                    "where" => ["TR_GI_SAPHEADER_ID" => $arr_data["id_web"]],
                    "data" => [
                        "TR_GI_SAPHEADER_SAP_DOC" => $arr_data["sap_number"],
                        "TR_GI_SAPHEADER_SAP_YEAR" => $arr_data["sap_doc_year"],
                        "TR_GI_SAPHEADER_ERROR" => $arr_data["sap_message"],
                        "TR_GI_SAPHEADER_STATUS" => $arr_data["result"]
                    ]
                ]);
            }
            else{
                if ($arr_data["transaction_code"] == "GR") {
                    if ($arr_data["result"] == "SUCCESS" || $arr_data["result"] == "WARNING") {
                        $gr_detail_data = std_get([
                            "select" => ["TR_GR_DETAIL.*","TR_GR_HEADER.TR_GR_HEADER_PSTG_DATE","TR_GR_HEADER.TR_GR_HEADER_ID","TR_GR_HEADER.TR_GR_HEADER_MVT_CODE"],
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
                                    "field_name" => "TR_GR_DETAIL_HEADER_ID",
                                    "operator" => "=",
                                    "value" => $arr_data["id_web"],
                                ]
                            ],
                            "first_row" => false
                        ]);

                        foreach ($gr_detail_data as $row) {
                            $po_detail_data = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_PO_DETAIL",
                                "where" => [
                                    [
                                        "field_name" => "TR_PO_DETAIL_ID",
                                        "operator" => "=",
                                        "value" => $row["TR_GR_DETAIL_PO_DETAIL_ID"]
                                    ]
                                ],
                                "first_row" => true
                            ]);

                            //var_dump($row["TR_GR_DETAIL_PO_DETAIL_ID"]);die();

                            std_update([
                                "table_name" => "TR_GR_DETAIL",
                                "where" => ["TR_GR_DETAIL_ID" => $row["TR_GR_DETAIL_ID"]],
                                "data" => [
                                    "TR_GR_DETAIL_LEFT_QTY" => $row["TR_GR_DETAIL_BASE_QTY"]
                                ]
                            ]);

                            std_update([
                                "table_name" => "TR_PO_DETAIL",
                                "where" => [
                                    "TR_PO_DETAIL_ID" => $row["TR_GR_DETAIL_PO_DETAIL_ID"]],
                                "data" => [
                                    "TR_PO_DETAIL_QTY_DELIV" => $po_detail_data["TR_PO_DETAIL_QTY_DELIV"] + $row["TR_GR_DETAIL_QTY"]
                                ]
                            ]);

                            insert_material_log([
                                "material_code" => $row["TR_GR_DETAIL_MATERIAL_CODE"],
                                "plant_code" => $row["TR_GR_DETAIL_UNLOADING_PLANT"],
                                "posting_date" => $row["TR_GR_HEADER_PSTG_DATE"],
                                "movement_type" => $row["TR_GR_HEADER_MVT_CODE"],
                                "gr_detail_id" => $row["TR_GR_DETAIL_ID"],
                                "base_qty" => $row["TR_GR_DETAIL_BASE_QTY"],
                                "base_uom" => $row["TR_GR_DETAIL_BASE_UOM"],
                                "created_by" => "0"
                            ]);
                        }
                    }

                    $update_res = std_update([
                        "table_name" => "TR_GR_HEADER",
                        "where" => ["TR_GR_HEADER_ID" => $arr_data["id_web"]],
                        "data" => [
                            "TR_GR_HEADER_SAP_DOC" => $arr_data["sap_number"],
                            "TR_GR_HEADER_SAP_YEAR" => $arr_data["sap_doc_year"],
                            "TR_GR_HEADER_ERROR" => $arr_data["sap_message"],
                            "TR_GR_HEADER_STATUS" => $arr_data["result"]
                        ]
                    ]);
                }
                elseif ($arr_data["transaction_code"] == "GI") {
//                    echo "masuk gi";
                    $update_res = std_update([
                        "table_name" => "TR_GI_SAPHEADER",
                        "where" => ["TR_GI_SAPHEADER_ID" => $arr_data["id_web"]],
                        "data" => [
                            "TR_GI_SAPHEADER_SAP_DOC" => $arr_data["sap_number"],
                            "TR_GI_SAPHEADER_SAP_YEAR" => $arr_data["sap_doc_year"],
                            "TR_GI_SAPHEADER_ERROR" => $arr_data["sap_message"],
                            "TR_GI_SAPHEADER_STATUS" => $arr_data["result"]
                        ]
                    ]);
                }
                elseif ($arr_data["transaction_code"] == "TP" || $arr_data["transaction_code"] == "BIWA") {
                    $update_res = std_update([
                        "table_name" => "TR_TP_HEADER",
                        "where" => ["TR_TP_HEADER_ID" => $arr_data["id_web"]],
                        "data" => [
                            "TR_TP_HEADER_SAP_DOC" => $arr_data["sap_number"],
                            "TR_TP_HEADER_SAP_YEAR" => $arr_data["sap_doc_year"],
                            "TR_TP_HEADER_ERROR" => $arr_data["sap_message"],
                            "TR_TP_HEADER_STATUS" => $arr_data["result"]
                        ]
                    ]);
                }
                elseif ($arr_data["transaction_code"] == "CANCEL") {
                    if ($arr_data["result"] == "SUCCESS" || $arr_data["result"] == "WARNING") {
                        $cancellation_header = std_get([
                            "select" => ["*"],
                            "table_name" => "TR_CANCELATION_MVT",
                            "where" => [
                                [
                                    "field_name" => "TR_CANCELLATION_MVT_ID",
                                    "operator" => "=",
                                    "value" => $arr_data["id_web"]
                                ]
                            ],
                            "order_by" => [
                                [
                                    "field" => "TR_CANCELLATION_MVT_ID",
                                    "type" => "DESC",
                                ]
                            ],
                            "first_row" => true
                        ]);
    
                        if ($cancellation_header["TR_CANCELLATION_MVT_SAP_CODE"] == "102") {
                            //GR Doc
                            $gr_header = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_GR_HEADER",
                                "where" => [
                                    [
                                        "field_name" => "TR_GR_HEADER_SAP_DOC",
                                        "operator" => "=",
                                        "value" => $cancellation_header["TR_CANCELLATION_MVT_TR_DOC"]
                                    ],
                                    [
                                        "field_name" => "TR_GR_HEADER_SAP_YEAR",
                                        "operator" => "=",
                                        "value" => $arr_data["sap_doc_year"]
                                    ],
                                ],
                                "first_row" => true
                            ]);
    
                            $gr_detail = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_GR_DETAIL",
                                "where" => [
                                    [
                                        "field_name" => "TR_GR_DETAIL_HEADER_ID",
                                        "operator" => "=",
                                        "value" => $gr_header["TR_GR_HEADER_ID"]
                                    ]
                                ],
                                "first_row" => false
                            ]);                            

                            foreach ($gr_detail as $row) {
                                $po_detail_data = std_get([
                                    "select" => ["*"],
                                    "table_name" => "TR_PO_DETAIL",
                                    "where" => [
                                        [
                                            "field_name" => "TR_PO_DETAIL_ID",
                                            "operator" => "=",
                                            "value" => $row["TR_GR_DETAIL_PO_DETAIL_ID"]
                                        ]
                                    ],
                                    "first_row" => true
                                ]);
                                
                                std_update([
                                    "table_name" => "TR_GR_DETAIL",
                                    "where" => ["TR_GR_DETAIL_ID" => $row["TR_GR_DETAIL_ID"]],
                                    "data" => [
                                        "TR_GR_DETAIL_LEFT_QTY" => 0
                                    ]
                                ]);

                                insert_material_log([
                                    "material_code" => $row["TR_GR_DETAIL_MATERIAL_CODE"],
                                    "plant_code" => $row["TR_GR_DETAIL_UNLOADING_PLANT"],
                                    "posting_date" => $gr_header["TR_GR_HEADER_PSTG_DATE"],
                                    "movement_type" => $gr_header["TR_GR_HEADER_MVT_CODE"],
                                    "gr_detail_id" => $row["TR_GR_DETAIL_ID"],
                                    "base_qty" => -$row["TR_GR_DETAIL_BASE_QTY"],
                                    "base_uom" => $row["TR_GR_DETAIL_BASE_UOM"],
                                    "created_by" => "0"
                                ]);

                                std_update([
                                    "table_name" => "TR_PO_DETAIL",
                                    "where" => [
                                        "TR_PO_DETAIL_ID" => $row["TR_GR_DETAIL_PO_DETAIL_ID"]],
                                    "data" => [
                                        "TR_PO_DETAIL_QTY_DELIV" => $po_detail_data["TR_PO_DETAIL_QTY_DELIV"] - $row["TR_GR_DETAIL_QTY"]
                                    ]
                                ]);

                            }
                            std_update([
                                "table_name" => "TR_GR_HEADER",
                                "where" => [
                                    "TR_GR_HEADER_ID" => $gr_header["TR_GR_HEADER_ID"]
                                ],
                                "data" => [
                                    "TR_GR_HEADER_IS_CANCELLED" => true
                                ]
                            ]);
                        }
                        elseif ($cancellation_header["TR_CANCELLATION_MVT_SAP_CODE"] == "162" || $cancellation_header["TR_CANCELLATION_MVT_SAP_CODE"] == "352") {
                            //GI Doc
                            $gi_header = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_GI_SAPHEADER",
                                "where" => [
                                    [
                                        "field_name" => "TR_GI_SAPHEADER_SAP_DOC",
                                        "operator" => "=",
                                        "value" => $cancellation_header["TR_CANCELLATION_MVT_TR_DOC"]
                                    ],
                                    [
                                        "field_name" => "TR_GI_SAPHEADER_SAP_YEAR",
                                        "operator" => "=",
                                        "value" => $arr_data["sap_doc_year"]
                                    ],
                                ],
                                "first_row" => true
                            ]);
    
                            $gi_detail = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_GI_SAPDETAIL",
                                "where" => [
                                    [
                                        "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                                        "operator" => "=",
                                        "value" => $gi_header["TR_GI_SAPHEADER_ID"]
                                    ]
                                ],
                                "first_row" => false
                            ]);

                            foreach ($gi_detail as $row) {
                                std_update([
                                    "table_name" => "TR_GR_DETAIL",
                                    "where" => ["TR_GR_DETAIL_ID" => $row["TR_GI_SAPDETAIL_GR_DETAIL_ID"]],
                                    "data" => [
                                        "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" + '.$row["TR_GI_SAPDETAIL_BASE_QTY"])
                                    ]
                                ]);

                                insert_material_log([
                                    "material_code" => $row["TR_GI_SAPDETAIL_MATERIAL_CODE"],
                                    "plant_code" => $gi_header["TR_GI_SAPHEADER_CREATED_PLANT_CODE"],
                                    "posting_date" => $gi_header["TR_GI_SAPHEADER_PSTG_DATE"],
                                    "movement_type" => $gi_header["TR_GI_SAPHEADER_MVT_CODE"],
                                    "gr_detail_id" => $row["TR_GI_SAPDETAIL_GR_DETAIL_ID"],
                                    "base_qty" => $row["TR_GI_SAPDETAIL_BASE_QTY"],
                                    "base_uom" => $row["TR_GI_SAPDETAIL_BASE_UOM"],
                                    "created_by" => "0"
                                ]);
                            }

                            std_update([
                                "table_name" => "TR_GI_SAPHEADER",
                                "where" => [
                                    "TR_GI_SAPHEADER_ID" => $gi_header["TR_GI_SAPHEADER_ID"]
                                ],
                                "data" => [
                                    "TR_GI_SAPHEADER_IS_CANCELLED" => true
                                ]
                            ]);
                        }
                        elseif ($cancellation_header["TR_CANCELLATION_MVT_SAP_CODE"] == "312" || $cancellation_header["TR_CANCELLATION_MVT_SAP_CODE"] == "552") {
                            //TP Doc
                            $tp_header = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_TP_HEADER",
                                "where" => [
                                    [
                                        "field_name" => "TR_TP_HEADER_SAP_DOC",
                                        "operator" => "=",
                                        "value" => $cancellation_header["TR_CANCELLATION_MVT_TR_DOC"]
                                    ],
                                    [
                                        "field_name" => "TR_TP_HEADER_SAP_YEAR",
                                        "operator" => "=",
                                        "value" => $arr_data["sap_doc_year"]
                                    ],
                                ],
                                "first_row" => true
                            ]);
    
                            $tp_detail = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_TP_DETAIL",
                                "where" => [
                                    [
                                        "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                                        "operator" => "=",
                                        "value" => $tp_header["TR_TP_HEADER_ID"]
                                    ]
                                ],
                                "first_row" => false
                            ]);

                            foreach ($tp_detail as $row) {
                                std_update([
                                    "table_name" => "TR_GR_DETAIL",
                                    "where" => ["TR_GR_DETAIL_ID" => $row["TR_TP_DETAIL_GR_DETAIL_ID"]],
                                    "data" => [
                                        "TR_GR_DETAIL_LEFT_QTY" => DB::raw('"TR_GR_DETAIL_LEFT_QTY" + '.$row["TR_TP_DETAIL_MOBILE_QTY"])
                                    ]
                                ]);

                                insert_material_log([
                                    "material_code" => $row["TR_TP_DETAIL_MATERIAL_CODE"],
                                    "plant_code" => $tp_header["TR_TP_HEADER_PLANT_CODE"],
                                    "posting_date" => $tp_header["TR_TP_HEADER_PSTG_DATE"],
                                    "movement_type" => $tp_header["TR_TP_HEADER_MVT_CODE"],
                                    "gr_detail_id" => $row["TR_TP_DETAIL_GR_DETAIL_ID"],
                                    "base_qty" => $row["TR_TP_DETAIL_MOBILE_QTY"],
                                    "base_uom" => $row["TR_TP_DETAIL_BASE_UOM"],
                                    "created_by" => "0"
                                ]);

                                if ($row["TR_TP_DETAIL_SLOC"] == "1419" || $row["TR_TP_DETAIL_SLOC"] == "1900") {
                                    $gr_detail = std_get([
                                        "select" => ["*"],
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
                                                "field_name" => "TR_GR_DETAIL_GR_REFERENCE",
                                                "operator" => "=",
                                                "value" => $row["TR_TP_DETAIL_GR_DETAIL_ID"]
                                            ]
                                        ],
                                        "first_row" => true
                                    ]);
                                    std_update([
                                        "table_name" => "TR_GR_DETAIL",
                                        "where" => ["TR_GR_DETAIL_ID" => $gr_detail["TR_GR_DETAIL_ID"]],
                                        "data" => [
                                            "TR_GR_DETAIL_LEFT_QTY" => 0
                                        ]
                                    ]);

                                    insert_material_log([
                                        "material_code" => $gr_detail["TR_GR_DETAIL_MATERIAL_CODE"],
                                        "plant_code" => $gr_detail["TR_GR_DETAIL_UNLOADING_PLANT"],
                                        "posting_date" => $gr_detail["TR_GR_HEADER_PSTG_DATE"],
                                        "movement_type" => $gr_detail["TR_GR_HEADER_MVT_CODE"],
                                        "gr_detail_id" => $gr_detail["TR_GR_DETAIL_ID"],
                                        "base_qty" => -$gr_detail["TR_GR_DETAIL_BASE_QTY"],
                                        "base_uom" => $gr_detail["TR_GR_DETAIL_BASE_UOM"],
                                        "created_by" => "0"
                                    ]);
                                }
                            }

                            std_update([
                                "table_name" => "TR_TP_HEADER",
                                "where" => [
                                    "TR_TP_HEADER_ID" => $tp_header["TR_TP_HEADER_ID"]
                                ],
                                "data" => [
                                    "TR_TP_HEADER_IS_CANCELLED" => true
                                ]
                            ]);
                        }
                        elseif ($cancellation_header["TR_CANCELLATION_MVT_SAP_CODE"] == "Y22") {
                            //TP Doc
                            $tp_header = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_TP_HEADER",
                                "where" => [
                                    [
                                        "field_name" => "TR_TP_HEADER_SAP_DOC",
                                        "operator" => "=",
                                        "value" => $cancellation_header["TR_CANCELLATION_MVT_TR_DOC"]
                                    ],
                                    [
                                        "field_name" => "TR_TP_HEADER_SAP_YEAR",
                                        "operator" => "=",
                                        "value" => $cancellation_header["TR_CANCELLATION_MVT_TR_DOC_YEAR"]
                                    ],
                                ],
                                "first_row" => true
                            ]);
    
                            $tp_detail = std_get([
                                "select" => ["*"],
                                "table_name" => "TR_TP_DETAIL",
                                "where" => [
                                    [
                                        "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                                        "operator" => "=",
                                        "value" => $tp_header["TR_TP_HEADER_ID"]
                                    ]
                                ],
                                "first_row" => false
                            ]);

                            foreach ($tp_detail as $row) {
                                $gr_detail = std_get([
                                    "select" => ["*"],
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
                                            "field_name" => "TR_GR_DETAIL_Y21_TP_REF",
                                            "operator" => "=",
                                            "value" => $row["TR_TP_DETAIL_Y21_GR_REF"]
                                        ]
                                    ],
                                    "first_row" => true
                                ]);
                                std_update([
                                    "table_name" => "TR_GR_DETAIL",
                                    "where" => ["TR_GR_DETAIL_ID" => $gr_detail["TR_GR_DETAIL_ID"]],
                                    "data" => [
                                        "TR_GR_DETAIL_LEFT_QTY" => 0
                                    ]
                                ]);

                                insert_material_log([
                                    "material_code" => $row["TR_TP_DETAIL_MATERIAL_CODE"],
                                    "plant_code" => $tp_header["TR_TP_HEADER_PLANT_CODE"],
                                    "posting_date" => $tp_header["TR_TP_HEADER_PSTG_DATE"],
                                    "movement_type" => $tp_header["TR_TP_HEADER_MVT_CODE"],
                                    "gr_detail_id" => $row["TR_TP_DETAIL_GR_DETAIL_ID"],
                                    "base_qty" => -($row["TR_TP_DETAIL_MOBILE_QTY"]),
                                    "base_uom" => $row["TR_TP_DETAIL_BASE_UOM"],
                                    "created_by" => "0"
                                ]);
                            }
                        }
                    }
                    
                    $update_res = std_update([
                        "table_name" => "TR_CANCELATION_MVT",
                        "where" => ["TR_CANCELLATION_MVT_ID" => $arr_data["id_web"]],
                        "data" => [
                            "TR_CANCELLATION_MVT_MATDOC" => $arr_data["sap_number"],
                            "TR_CANCELLATION_MVT_MATDOC_YEAR" => $arr_data["sap_doc_year"],
                            "TR_CANCELLATION_MVT_ERROR" => $arr_data["sap_message"],
                            "TR_CANCELLATION_MVT_STATUS" => $arr_data["result"]
                        ]
                    ]);
                }
            }

            if ($update_res != false) {
                if (copy($files[$i], $base_url_success.basename($files[$i]))) {
                    echo "file success";
                    unlink($files[$i]);
                } else {
                    echo "Error on copy file to backup";
                }
            }
            fclose($file);
        }
        $this->info('Receive GI / GR / TP Cron Successfully Triggered');
        return 0;
    }
}

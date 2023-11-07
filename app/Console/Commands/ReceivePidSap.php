<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReceivePidSap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:receive_pid_sap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive PID Data from SAP';

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
        $base_url = storage_path('app/public/OUTGOING2/PID/');
        $base_url_success = storage_path('app/public/OUTGOING2/PID__BACKUP/');
        $files = glob($base_url."PID_DATA_*.csv");
        $data = null;
        for ($i=0; $i < count($files); $i++) {
            $file = fopen($files[$i], "r");
            $pid_header = null;
            $pid_items = [];
            $insert_result = true;
            $pid_header_number = null;
            $counter = 0;
            while (! feof($file)) {
                $file_rows = fgetcsv($file, null, ";");
                if ($counter == 0) {
                    if ($file_rows[0] != null && $file_rows[0] != "" && $file_rows[0] != "EOF") {
                        $pid_header_number = $file_rows[0];
                        $pid_header = [
                            "TR_PID_HEADER_SAP_NO" => $file_rows[0],
                            "TR_PID_HEADER_YEAR" => $file_rows[1],
                            "TR_PID_HEADER_STATUS" => $file_rows[2],
                            "TR_PID_HEADER_SAP_CREATED_DATE" => convert_to_y_m_d($file_rows[3]),
                            "TR_PID_HEADER_SAP_CREATED_BY" => $file_rows[4],
                            "TR_PID_HEADER_PLANT" => $file_rows[5],
                            "TR_PID_HEADER_SLOC" => $file_rows[6],
                            "TR_PID_MOBILE_ALLOW_TO_INPUT" => TRUE,
                            "TR_PID_HEADER_CREATED_BY" => "SYSTEM",
                            "TR_PID_HEADER_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                            "TR_PID_HEADER_UPDATED_BY" => false,
                            "TR_PID_HEADER_UPDATED_TIMESTAMP" => NULL,
                            "TR_PID_HEADER_APPROVAL_STATUS" => NULL,
                            "TR_PID_HEADER_APPROVAL_BY" => false,
                            "TR_PID_HEADER_APPROVAL_TIMESTAMP" => NULL,
                            "TR_PID_HEADER_APPROVAL_COUNTER" => 0,
                            "TR_PID_HEADER_IS_DELETED" => false
                        ];
                    }
                    $counter++;
                } else {
                    if (isset($file_rows[0]) && $file_rows[0] != null) {
                        $material_data = std_get([
                            "select" => ["MA_MATL_DESC"],
                            "table_name" => "MA_MATL",
                            "where" => [
                                [
                                    "field_name" => "MA_MATL_CODE",
                                    "operator" => "=",
                                    "value" => $file_rows[1],
                                ]
                            ],
                            "order_by" => [
                                [
                                    "field" => "MA_MATL_ID",
                                    "type" => "DESC",
                                ]
                            ],
                            "first_row" => true
                        ]);
                        if ($material_data != NULL) {
                            $material_name = $material_data["MA_MATL_DESC"];
                        }
                        else{
                            $material_name = NULL;
                        }
                        $pid_items = array_merge($pid_items, [
                            [
                                "TR_PID_DETAIL_HEADER_ID" => $pid_header_number,
                                "TR_PID_DETAIL_LINE_MATERIAL" => $file_rows[0],
                                "TR_PID_DETAIL_MATERIAL_CODE" => $file_rows[1],
                                "TR_PID_DETAIL_MATERIAL_NAME" => $material_name,
                                "TR_PID_DETAIL_MATERIAL_SAP_BATCH" => $file_rows[2],
                                "TR_PID_DETAIL_MATERIAL_UOM" => $file_rows[3],
                                "TR_PID_DETAIL_MATERIAL_MOBILE_QTY" => NULL,
                            ]
                        ]);
                    }
                }
            }
            
            if ($pid_header != null && $pid_items != null) {
                if ($pid_header["TR_PID_HEADER_STATUS"] == "I") {
                    std_insert([
                        "table_name" => "TR_PID_HEADER",
                        "data" => $pid_header
                    ]);

                    std_insert([
                        "table_name" => "TR_PID_DETAIL",
                        "data" => $pid_items
                    ]);
                }
                else if ($pid_header["TR_PID_HEADER_STATUS"] == "U") {
                    std_delete([
                        "table_name" => "TR_PID_HEADER",
                        "where" => ["TR_PID_HEADER_SAP_NO" => $pid_header_number]
                    ]);
                    std_delete([
                        "table_name" => "TR_PID_DETAIL",
                        "where" => ["TR_PID_DETAIL_HEADER_ID" => $pid_header_number]
                    ]);
                    std_insert([
                        "table_name" => "TR_PID_HEADER",
                        "data" => $pid_header
                    ]);
                    std_insert([
                        "table_name" => "TR_PID_DETAIL",
                        "data" => $pid_items
                    ]);
                }
            }
            else{
                if ($pid_header["TR_PID_HEADER_STATUS"] == "D") {
                    std_update([
                        "table_name" => "TR_PID_HEADER",
                        "where" => ["TR_PID_HEADER_SAP_NO" => $pid_header_number],
                        "data" => [
                            "TR_PID_HEADER_IS_DELETED" => true
                        ]
                    ]);
                }
            }

            if ($insert_result == true) {
                if (copy($files[$i], $base_url_success.basename($files[$i]))) {
                    unlink($files[$i]);
                }
            }
        }
        $this->info('Receive PID Cron Log Successfully Triggred');
        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReceivePidSapResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:receive_pid_sap_response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive PID SAP Response';

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
        $base_url = storage_path('app/public/OUTGOING2/PID_DOC/');
        $base_url_success = storage_path('app/public/OUTGOING2/PID__DOC_BACKUP/');
        $files = glob($base_url."PID_OUT_*.csv");
        usort(
            $files, 
            function($a,$b){
                return basename($a) <=> basename($b);
            }
        );
        for ($i=0; $i < count($files); $i++) {
            $arr_data = null;
            $file = fopen($files[$i], "r");
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
                        $error_message = array_merge($error_message, [
                            $file_rows[5]
                        ]);
                        $arr_data = [
                            "id_web" => $file_rows[0],
                            // "transaction_code" => $file_rows[2],
                            "sap_number" => $file_rows[2],
                            "sap_doc_year" => $file_rows[3],
                            "result" => $res_status,
                            "sap_message" => implode("|",$error_message)
                        ];
                    }
                }
            }
            
            $update_res = std_update([
                "table_name" => "TR_PID_HEADER",
                "where" => ["TR_PID_HEADER_SAP_NO" => $arr_data["id_web"]],
                "data" => [
                    "TR_PID_HEADER_SAP_RETURN_NO" => $arr_data["sap_number"],
                    "TR_PID_HEADER_SAP_RETURN_YEAR" => $arr_data["sap_doc_year"],
                    "TR_PID_HEADER_SAP_RETURN_ERROR" => $arr_data["sap_message"],
                    "TR_PID_HEADER_STATUS" => $arr_data["result"]
                ]
            ]);
        
            if ($update_res != false) {
                if (copy($files[$i], $base_url_success.basename($files[$i]))) {
                    unlink($files[$i]);
                } else {
                    echo "Error on copy file to backup";
                }
            }
            fclose($file);
        }
        $this->info('Receive PID Response From SAP Cron Log Successfully Triggred');
        return 0;
    }
}

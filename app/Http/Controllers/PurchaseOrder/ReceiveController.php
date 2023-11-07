<?php

namespace App\Http\Controllers\PurchaseOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiveController extends Controller
{
    public function index(Request $request)
    {
        $base_url = storage_path('app/public/OUTGOING2/PO_DATA/');
        $base_url_success = storage_path('app/public/OUTGOING2/PO_DATA_BACKUP/');
        $files = glob($base_url."PO_*.csv");
        usort(
            $files, 
            function($a,$b){
                return basename($a) <=> basename($b);
            }
        );
        $data = null;
        for ($i=0; $i < count($files); $i++) {
            $file = fopen($files[$i], "r");
            $po_header = null;
            $po_items = [];
            $insert_result = true;
            $po_header_number = null;
            $counter = 0;
            while (! feof($file)) {
                $file_rows = fgetcsv($file, null, ";");
                if ($counter == 0) {
                    if ($file_rows[0] != null && $file_rows[0] != "" && $file_rows[0] != "EOF") {
                        $po_header_number = $file_rows[0];
                        $po_header = [
                            "TR_PO_HEADER_NUMBER" => $file_rows[0],
                            "TR_PO_HEADER_STATUS" => $file_rows[1],
                            "TR_PO_HEADER_TYPE" => $file_rows[2],
                            "TR_PO_HEADER_SAP_CREATED_DATE" => convert_to_y_m_d($file_rows[3]),
                            "TR_PO_HEADER_SAP_CREATED_BY" => $file_rows[4],
                            "TR_PO_HEADER_VENDOR" => $file_rows[5],
                            "TR_PO_HEADER_SUP_PLANT" => $file_rows[6],
                            "TR_PO_HEADER_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                            "TR_PO_HEADER_CREATED_BY" => "SYSTEM",
                            "TR_PO_HEADER_IS_DELETED" => false
                        ];
                    }
                    $counter++;
                } else {
                    if (isset($file_rows[0]) && $file_rows[0] != null) {
                        $po_items = array_merge($po_items, [
                            [
                                "TR_PO_DETAIL_MATERIAL_LINE_NUM" => $file_rows[0],
                                "TR_PO_DETAIL_PO_HEADER_NUMBER" => $po_header_number,
                                "TR_PO_DETAIL_MATERIAL_CODE" => $file_rows[1],
                                "TR_PO_DETAIL_MATERIAL_NAME" => $file_rows[2],
                                "TR_PO_DETAIL_MATERIAL_BATCH" => $file_rows[3],
                                // "TR_PO_DETAIL_MATERIAL_DESC" => $file_rows[2],
                                // "TR_PO_DETAIL_BATCH" => (double) str_replace(",","",$file_rows[4]),
                                "TR_PO_DETAIL_QTY_ORDER" => (double) str_replace(",","",$file_rows[4]),
                                "TR_PO_DETAIL_QTY_DELIV" => (double) str_replace(",","",$file_rows[5]),
                                "TR_PO_DETAIL_UOM" => $file_rows[6],
                                "TR_PO_DETAIL_PLANT_RCV" => $file_rows[7],
                                "TR_PO_DETAIL_SLOC" => $file_rows[8],
                                // "TR_PO_DETAIL_FLAG" => $file_rows[9]
                            ]
                        ]);
                    }
                }
            }
            
            if ($po_header != null && $po_items != null) {
                if ($po_header["TR_PO_HEADER_STATUS"] == "I") {
                    std_insert([
                        "table_name" => "TR_PO_HEADER",
                        "data" => $po_header
                    ]);

                    std_insert([
                        "table_name" => "TR_PO_DETAIL",
                        "data" => $po_items
                    ]);
                }
                else if ($po_header["TR_PO_HEADER_STATUS"] == "U") {
                    std_delete([
                        "table_name" => "TR_PO_HEADER",
                        "where" => ["TR_PO_HEADER_NUMBER" => $po_header_number]
                    ]);
                    std_delete([
                        "table_name" => "TR_PO_DETAIL",
                        "where" => ["TR_PO_DETAIL_PO_HEADER_NUMBER" => $po_header_number]
                    ]);
                    std_insert([
                        "table_name" => "TR_PO_HEADER",
                        "data" => $po_header
                    ]);
                    std_insert([
                        "table_name" => "TR_PO_DETAIL",
                        "data" => $po_items
                    ]);
                }
                else if ($po_header["TR_PO_HEADER_STATUS"] == "D") {
                    std_update([
                        "table_name" => "TR_PO_HEADER",
                        "where" => ["TR_PO_HEADER_NUMBER" => $po_header_number],
                        "data" => [
                            "TR_PO_HEADER_IS_DELETED" => true
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
    }
}

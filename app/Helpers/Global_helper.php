<?php

use Illuminate\Support\Facades\Log;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;

function get_master_data($table_name = null, $selects = null, $limit = null, $offset = null, $is_find = false, $conditions = [], $orders = null, $where_in = null) {
    if (isset($selects)) {
        $select_fields = $selects;
    } else {
        $select_fields = ["*"];
    }

    if (isset($limit)) {
        $limit = $limit;
    }

    if (isset($offset)) {
        $offset = $offset;
    }

    if (isset($is_find) && $is_find == true) {
        $first_row = true;
    } else {
        $first_row = false;
    }

    if (isset($conditions) && is_array($conditions)) {
        $conditions = [];
        for ($i = 0; $i < count($conditions); $i++) {
            if (isset($conditions[$i]["field_name"]) && isset($conditions[$i]["operator"]) && isset($conditions[$i]["value"])) {
                $conditions[$i]["field_name"] = $conditions[$i]["field_name"];
                $conditions[$i]["operator"] = $conditions[$i]["operator"];
                $conditions[$i]["value"] = $conditions[$i]["value"];
            }
        }
    }

    if (isset($orders) && is_array($orders)) {
        $orders = [];
        for ($i = 0; $i < count($orders); $i++) {
            if (isset($orders[$i]["field"])) {
                $orders[$i]["field"] = $orders[$i]["field"];
                if (isset($orders[$i]["type"])) {
                    $orders[$i]["type"] = $orders[$i]["type"];
                } else {
                    $orders[$i]["type"] = "ASC";
                }
            }
        }
    }

    if (isset($where_in) && is_array($where_in)) {
        if (isset($where_in["field_name"]) && isset($where_in["ids"]) && is_array($where_in["ids"])) {
            $where_in["field_name"] = $where_in["field_name"];
            $where_in["ids"] = $where_in["ids"];
        }
    }

    $data = std_get([
        "select" => $select_fields,
        "table_name" => $table_name,
        "special_where" => $conditions,
        "where_in" => $where_in,
        "order_by" => $orders,
        "limit" => $limit,
        "offset" => $offset,
        "first_row" => $first_row
    ]);

    return $data;
}

function export_request_master_data_csv($type_csv_string, $type_file_name) {
    $export_csv_po_header[] = $type_csv_string;
    $export_csv_po_header[] = "U";
    $path = storage_path('app/public/INCOMING2/MASTER_DATA_INCOMING/');
    if (!Storage::exists($path)) {
        Storage::makeDirectory('public/INCOMING2/MASTER_DATA_INCOMING/');
    }
    $fileName = "MASTERDATA_IN_" . $type_file_name . "_" . date("dmY") . date("His") . ".csv";
    $file = fopen($path . $fileName, 'w');

    $columns = array($type_file_name, 'U');

    fputcsv($file, $columns, ";");

    fclose($file);
    $response = [
        "code" => 200
    ];
    return $response;
}

function export_request_po_data_csv($type_csv_string, $type_file_name) {
    $export_csv_po_header[] = $type_csv_string;
    $export_csv_po_header[] = "U";
    $path = storage_path('app/public/INCOMING2/UPDATE_DATA/');
    if (!Storage::exists($path)) {
        Storage::makeDirectory('public/INCOMING2/UPDATE_DATA/');
    }
    $fileName = "UPDATE_IN_" . date("dmY") . date("His") . ".csv";
    $file = fopen($path . $fileName, 'w');

    $columns = array($type_file_name, 'U');

    fputcsv($file, $columns, ";");

    fclose($file);
    $response = [
        "code" => 200
    ];
    return $response;
}

function get_outgoing_folder_name($master_type) {
    if ($master_type == "CC") {
        return "COST_CENTER";
    } elseif ($master_type == "GL") {
        return "GL_ACCOUNT";
    } elseif ($master_type == "MAT") {
        return "MATERIAL";
    } elseif ($master_type == "MAT_UoM") {
        return "MATERIAL_UOM";
    } elseif ($master_type == "MVT") {
        return "MOV_TYPE";
    } elseif ($master_type == "PLANT") {
        return $master_type;
    } elseif ($master_type == "SLOC") {
        return $master_type;
    } elseif ($master_type == "VENDOR") {
        return $master_type;
    }
}

function insert_data($insert_values, $table_name) {
    if ($insert_values != null) {
        std_truncate([
            "table_name" => $table_name
        ]);

        if ($table_name == "MA_MATL" || $table_name == "MA_VENDOR") {
            $insert_values = collect($insert_values);
            $chunks = $insert_values->chunk(1000);
            foreach ($chunks as $chunk) {
                std_insert([
                    "table_name" => $table_name,
                    "data" => $chunk->toArray()
                ]);
            }
        } else {
            std_insert([
                "table_name" => $table_name,
                "data" => $insert_values
            ]);
        }
    }
}

function check_string($data) {
    $data = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $data);
    return $data;
}

function sync_master_data($master_type = null) {
    $base_url = storage_path('app/public/OUTGOING2/MASTERDATA/' . get_outgoing_folder_name($master_type) . "/");
    $base_url_success = storage_path('app/public/OUTGOING2/MASTERDATA_BACKUP/');
    $files = glob($base_url . "MASTERBC_" . $master_type . "_*.csv");
    if ($files == null) {
        $response = [
            "code" => 404,
            "message" => "No new master data found"
        ];
        return $response;
    }

    for ($i = 0; $i < count($files); $i++) {
        $file = fopen($files[$i], "r");

        $insert_data = null;
        $update_data = null;
        $insert_values = null;
        $update_values = null;
        $insert_result = true;
        $update_result = true;

        while (!feof($file)) {
            $file_rows = fgetcsv($file, null, ";");
            if ($file_rows != false) {
                if ($file_rows[0] != null && $file_rows[0] != "" && $file_rows[0] != "EOF") {
                    if ($master_type == "PLANT") {
                        $insert_values[] = [
                            "MA_PLANT_CODE" => $file_rows[0],
                            "MA_PLANT_NAME" => $file_rows[1],
                            "MA_PLANT_STREET" => $file_rows[2],
                            "MA_PLANT_CITY" => $file_rows[3],
                            "MA_PLANT_POSTAL_CODE" => $file_rows[4],
                            "MA_PLANT_TELP" => $file_rows[5],
                            "MA_PLANT_FAX" => $file_rows[6],
                            "MA_PLANT_CREATED_BY" => "system",
                            "MA_PLANT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
                        ];
                    } elseif ($master_type == "SLOC") {
                        $insert_values[] = [
                            "MA_SLOC_PLANT" => $file_rows[0],
                            "MA_SLOC_CODE" => $file_rows[1],
                            "MA_SLOC_DESC" => $file_rows[2],
                            "MA_SLOC_CREATED_BY" => "system",
                            "MA_SLOC_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
                        ];
                    } elseif ($master_type == "MAT") {
                        $batch_flag = false;
                        if ($file_rows[9] == "X") {
                            $batch_flag = true;
                        }

                        $file_rows[7] = str_replace("-", "", $file_rows[7]);
                        $file_rows[7] = str_replace(",", "", $file_rows[7]);

                        $insert_values[] = [
                            "MA_MATL_CODE" => $file_rows[0],
                            "MA_MATL_DESC" => check_string($file_rows[1]),
                            "MA_MATL_TYPE" => $file_rows[2],
                            "MA_MATL_GROUP" => $file_rows[3],
                            "MA_MATL_PLANT" => $file_rows[4],
                            "MA_MATL_SLOC" => $file_rows[5],
                            "MA_MATL_BATCH" => $file_rows[6],
                            "MA_MATL_QTY" => $file_rows[7],
                            "MA_MATL_UOM" => $file_rows[8],
                            "MA_MATL_FLAG_BATCH" => $batch_flag,
                            "MA_MATL_CREATED_BY" => "system",
                            "MA_MATL_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                        ];
                    } elseif ($master_type == "GL") {
                        $insert_values[] = [
                            "MA_GLACC_CODE" => $file_rows[0],
                            "MA_GLACC_DESC" => preg_replace('/[^a-zA-Z0-9_ -]/s', '', $file_rows[1]),
                            "MA_GLACC_CREATED_BY" => "system",
                            "MA_GLACC_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
                        ];
                    } elseif ($master_type == "CC") {
                        $insert_values[] = [
                            "MA_COSTCNTR_CODE" => $file_rows[0],
                            "MA_COSTCNTR_DESC" => preg_replace('/[^a-zA-Z0-9_ -]/s', '', $file_rows[1]),
                            "MA_COSTCNTR_CREATED_BY" => "system",
                            "MA_COSTCNTR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                        ];
                    } elseif ($master_type == "MAT_UoM") {
                        $file_rows[2] = str_replace(",", "", $file_rows[2]);
                        $file_rows[3] = str_replace(",", "", $file_rows[3]);
                        $insert_values[] = [
                            "MA_UOM_MATCODE" => $file_rows[0],
                            "MA_UOM_UOM" => $file_rows[1],
                            "MA_UOM_NUM" => $file_rows[2],
                            "MA_UOM_DEN" => $file_rows[3],
                            "MA_UOM_CREATED_BY" => "system",
                            "MA_UOM_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
                        ];
                    } elseif ($master_type == "VENDOR") {
                        $insert_values[] = [
                            "MA_VENDOR_CODE" => $file_rows[0],
                            "MA_VENDOR_NAME" => check_string($file_rows[1]),
                            "MA_VENDOR_STREET" => check_string($file_rows[2]),
                            "MA_VENDOR_CITY" => $file_rows[3],
                            "MA_VENDOR_TELP" => $file_rows[4],
                            "MA_VENDOR_FAX" => $file_rows[5],
                            "MA_VENDOR_CREATED_BY" => "system",
                            "MA_VENDOR_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
                        ];
                    } elseif ($master_type == "MVT") {
                        $insert_values[] = [
                            "MA_MVT_CODE" => $file_rows[0],
                            "MA_MVT_DESC" => $file_rows[1],
                            "MA_MVT_CREATED_BY" => "system",
                            "MA_MVT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                        ];
                    }
                }
            }
        }
        if ($master_type == "PLANT") {
            insert_data($insert_values, "MA_PLANT");
        } elseif ($master_type == "SLOC") {
            insert_data($insert_values, "MA_SLOC");
        } elseif ($master_type == "MAT") {
            insert_data($insert_values, "MA_MATL");
        } elseif ($master_type == "GL") {
            insert_data($insert_values, "MA_GLACC");
        } elseif ($master_type == "CC") {
            insert_data($insert_values, "MA_COSTCNTR");
        } elseif ($master_type == "MAT_UoM") {
            insert_data($insert_values, "MA_UOM");
        } elseif ($master_type == "VENDOR") {
            insert_data($insert_values, "MA_VENDOR");
        } elseif ($master_type == "MVT") {
            insert_data($insert_values, "MA_MVT");
        }

        if ($insert_result == true) {
            if (copy($files[$i], $base_url_success . basename($files[$i]))) {
                unlink($files[$i]);
                // update_cron_log($log_id, 1);
            } else {
                // update_cron_log($log_id, 2);
            }
        }
        fclose($file);
        $response = [
            "code" => 200,
            "message" => "successfuly save master data" . $master_type
        ];
        return $response;
    }
}

function decode_token($token) {
    $decoded = JWT::decode($token, "example_key", array('HS256'));
    return (array) $decoded;
}

function update_image_source($data, $is_find, $array_key, $location) {
    if ($is_find == true) {
        if (isset($data[$array_key])) {
            if ($data[$array_key] == "" || $data[$array_key] == null) {
                $data[$array_key] = null;
            } else {
                $data[$array_key] = env('AWS_S3_DEFAULT_URL') . $location . $data[$array_key];
            }
        } else {
            return $data;
        }
    } else {
        for ($i = 0; $i < count($data); $i++) {
            if (isset($data[$i][$array_key])) {
                if ($data[$i][$array_key] == "" || $data[$i][$array_key] == null) {
                    $data[$i][$array_key] = null;
                } else {
                    $data[$i][$array_key] = env('AWS_S3_DEFAULT_URL') . $location . $data[$i][$array_key];
                }
            } else {
                $data[$i][$array_key] = null;
            }
        }
    }
    return $data;
}

function get_image_url($data) {
    $url = 'https://' . env('AWS_BUCKET') . '.s3-' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/images/';
    return $url . $data;
}

function base_assets_url($service_type, $image_name) {
    return hostname_dictionary($service_type) . "public/" . $image_name;
}

function ldap_login($email = null, $password = null) {
    if ($email != null && $password != null) {
        $server = gethostbyname("sso.sariroti.com");
        $ds = ldap_connect($server, 389);
        if (false === $ds) {
            return 500;
        } else {
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
            $dn = "OU=SSO-APPS,DC=sariroti,DC=com";
            try {
                $bind = ldap_bind($ds, $email, $password);
                if ($bind === true) {
                    $results = ldap_search($ds, $dn, "CN=User SSO");
                    $entries = ldap_get_entries($ds, $results);
                    if (isset($entries[0]["displayname"][0])) {
                        return [
                            200,
                            $entries[0]["displayname"][0]
                        ];
                    } else {
                        return 500;
                    }
                } else {
                    return 404;
                }
            } catch (\Throwable $th) {
                return 501;
            }
        }
    } else {
        return 400;
    }
}

function convert_to_y_m_d($date) {
    $day = substr($date, 0, 2);
    $month = substr($date, 3, 2);
    $year = substr($date, 6, 4);
    return $year . "-" . $month . "-" . $day;
}

function convert_to_dmy($date) {
    $day = substr($date, 8, 2);
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);
    return $day . $month . $year;
}

function convert_to_web_dmy($date) {
    $day = substr($date, 8, 2);
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);
    return $day . "-" . $month . "-" . $year;
}

function get_sloc($plant_code) {
    $data = std_get([
        "select" => ["MA_SLOC_CODE", "MA_SLOC_DESC"],
        "table_name" => "MA_SLOC",
        "where" => [
            [
                "field_name" => "MA_SLOC_PLANT",
                "operator" => "=",
                "value" => $plant_code
            ]
        ],
        "order_by" => [
            [
                "field" => "MA_SLOC_CODE",
                "type" => "ASC",
            ]
        ],
    ]);

    $sloc_arr = [];
    foreach ($data as $row) {
        $sloc_arr = array_merge($sloc_arr, [
            [
                "id" => $row["MA_SLOC_CODE"],
                "text" => $row["MA_SLOC_CODE"] . " - " . $row["MA_SLOC_DESC"]
            ]
        ]);
    }
    return $sloc_arr;
}

function generate_gr_csv($gr_header_id, $plant_code) {
    $header_data = std_get([
        "select" => ["*"],
        "table_name" => "TR_GR_HEADER",
        "where" => [
            [
                "field_name" => "TR_GR_HEADER_ID",
                "operator" => "=",
                "value" => $gr_header_id
            ]
        ],
        "first_row" => true
    ]);

    $detail_data = std_get([
        "select" => ["TR_GR_DETAIL.*", "TR_PO_DETAIL_MATERIAL_LINE_NUM"],
        "table_name" => "TR_GR_DETAIL",
        "join" => [
            [
                "join_type" => "inner",
                "table_name" => "TR_PO_DETAIL",
                "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                "operator" => "=",
                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
            ]
        ],
        "where" => [
            [
                "field_name" => "TR_GR_DETAIL_HEADER_ID",
                "operator" => "=",
                "value" => $gr_header_id
            ]
        ],
        "order_by" => [
            [
                "field" => "TR_GR_DETAIL_SAPLINE_ID",
                "type" => "ASC",
            ]
        ],
    ]);

    $fp = fopen(storage_path('app/public/INCOMING2/MIGO/' . "MIGO_IN_" . $plant_code . "_GR_" . str_pad($gr_header_id, 7, "0", STR_PAD_LEFT) . "_" . date("YmdHis") . ".csv"), 'w');
    fputcsv($fp, [
        "GR",
        $header_data["TR_GR_HEADER_PO_NUMBER"],
        "",
        "",
        $header_data["TR_GR_HEADER_MVT_CODE"],
        convert_to_dmy(str_replace("-", ".", $header_data["TR_GR_HEADER_PSTG_DATE"])),
        convert_to_dmy(str_replace("-", ".", $header_data["TR_GR_HEADER_DOC_DATE"])),
        $header_data["TR_GR_HEADER_TXT"],
        $header_data["TR_GR_HEADER_BOL"],
        $gr_header_id,
            ], ";");

    $counter = 1;
    foreach ($detail_data as $row) {
        fputcsv($fp, [
            $row["TR_PO_DETAIL_MATERIAL_LINE_NUM"],
            $row["TR_GR_DETAIL_MATERIAL_CODE"],
            $row["TR_GR_DETAIL_SAP_BATCH"],
            $row["TR_GR_DETAIL_QTY"],
            $row["TR_GR_DETAIL_UOM"],
            $row["TR_GR_DETAIL_UNLOADING_PLANT"],
            $row["TR_GR_DETAIL_SLOC"],
            "",
            "",
            $row["TR_GR_DETAIL_NOTES"],
            $header_data["TR_GR_HEADER_RECIPIENT"],
            $row["TR_GR_DETAIL_QR_CODE_NUMBER"],
            $row["TR_GR_DETAIL_GL_ACCOUNT"],
            $row["TR_GR_DETAIL_COST_CENTER"],
                ], ";");
        $counter++;
    }

    fclose($fp);
}

function generate_gi_csv($gi_header_id, $plant_code) {
    $header_data = std_get([
        "select" => ["*"],
        "table_name" => "TR_GI_SAPHEADER",
        "where" => [
            [
                "field_name" => "TR_GI_SAPHEADER_ID",
                "operator" => "=",
                "value" => $gi_header_id
            ]
        ],
        "first_row" => true
    ]);

    $detail_data = std_get([
        "select" => ["TR_GI_SAPDETAIL.*", "TR_PO_DETAIL_MATERIAL_LINE_NUM"],
        "table_name" => "TR_GI_SAPDETAIL",
        "join" => [
            [
                "join_type" => "inner",
                "table_name" => "TR_GR_DETAIL",
                "on1" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_GR_DETAIL_ID",
                "operator" => "=",
                "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
            ],
            [
                "join_type" => "inner",
                "table_name" => "TR_PO_DETAIL",
                "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                "operator" => "=",
                "on2" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_PO_DETAIL_ID",
            ]
        ],
        "where" => [
            [
                "field_name" => "TR_GI_SAPDETAIL_SAPHEADER_ID",
                "operator" => "=",
                "value" => $gi_header_id
            ]
        ],
        "order_by" => [
            [
                "field" => "TR_PO_DETAIL_MATERIAL_LINE_NUM",
                "type" => "ASC",
            ],
            [
                "field" => "TR_GI_SAPDETAIL_SAPLINE_ID",
                "type" => "ASC",
            ]
        ],
    ]);

    if ($header_data["TR_GI_SAPHEADER_MVT_CODE"] == "101") {
        $fp = fopen(storage_path('app/public/INCOMING2/MIGO/' . "MIGO_IN_" . $plant_code . "_GR_" . str_pad($gi_header_id, 7, "0", STR_PAD_LEFT) . "_" . date("YmdHis") . ".csv"), 'w');

        fputcsv($fp, [
            "GR",
            $header_data["TR_GI_SAPHEADER_PO_NUMBER"],
            "",
            "",
            $header_data["TR_GI_SAPHEADER_MVT_CODE"],
            convert_to_dmy(str_replace("-", ".", $header_data["TR_GI_SAPHEADER_PSTG_DATE"])),
            convert_to_dmy(str_replace("-", ".", $header_data["TR_GI_SAPHEADER_DOC_DATE"])),
            $header_data["TR_GI_SAPHEADER_TXT"],
            $header_data["TR_GI_SAPHEADER_BOL"],
            $gi_header_id,
                ], ";");
    } else {
        $fp = fopen(storage_path('app/public/INCOMING2/MIGO/' . "MIGO_IN_" . $plant_code . "_GI_" . str_pad($gi_header_id, 7, "0", STR_PAD_LEFT) . "_" . date("YmdHis") . ".csv"), 'w');

        fputcsv($fp, [
            "GI",
            $header_data["TR_GI_SAPHEADER_PO_NUMBER"],
            "",
            "",
            $header_data["TR_GI_SAPHEADER_MVT_CODE"],
            convert_to_dmy(str_replace("-", ".", $header_data["TR_GI_SAPHEADER_PSTG_DATE"])),
            convert_to_dmy(str_replace("-", ".", $header_data["TR_GI_SAPHEADER_DOC_DATE"])),
            $header_data["TR_GI_SAPHEADER_TXT"],
            $header_data["TR_GI_SAPHEADER_BOL"],
            $gi_header_id,
                ], ";");
    }

    $counter = 1;
    foreach ($detail_data as $row) {
        fputcsv($fp, [
            $row["TR_PO_DETAIL_MATERIAL_LINE_NUM"],
            $row["TR_GI_SAPDETAIL_MATERIAL_CODE"],
            $row["TR_GI_SAPDETAIL_SAP_BATCH"],
            $row["TR_GI_SAPDETAIL_GI_QTY"],
            $row["TR_GI_SAPDETAIL_GI_UOM"],
            $plant_code,
            $row["TR_GI_SAPDETAIL_SLOC"],
            "",
            "",
            $row["TR_GI_SAPDETAIL_NOTES"],
            "",
            $row["TR_GI_SAPDETAIL_QR_CODE_NUMBER"],
            "",
            "",
                ], ";");
        $counter++;
    }

    fclose($fp);
}

function generate_tp_csv($tp_header_id, $plant_code) {
    $header_data = std_get([
        "select" => ["*"],
        "table_name" => "TR_TP_HEADER",
        "where" => [
            [
                "field_name" => "TR_TP_HEADER_ID",
                "operator" => "=",
                "value" => $tp_header_id
            ]
        ],
        "first_row" => true
    ]);

    if ($header_data["TR_TP_HEADER_MVT_CODE"] == "Y21") {
        $detail_data = std_get([
            "select" => ["TR_TP_DETAIL.*"],
            "table_name" => "TR_TP_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $tp_header_id
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_TP_DETAIL_SAPLINE_ID",
                    "type" => "ASC",
                ]
            ],
        ]);

        $fp = fopen(storage_path('app/public/INCOMING2/MIGO/' . "MIGO_IN_" . $plant_code . "_TP_" . str_pad($tp_header_id, 7, "0", STR_PAD_LEFT) . "_" . date("YmdHis") . ".csv"), 'w');

        fputcsv($fp, [
            "TP",
            "",
            "",
            "",
            $header_data["TR_TP_HEADER_MVT_CODE"],
            convert_to_dmy(str_replace("-", ".", $header_data["TR_TP_HEADER_PSTG_DATE"])),
            convert_to_dmy(str_replace("-", ".", $header_data["TR_TP_HEADER_DOC_DATE"])),
            $header_data["TR_TP_HEADER_TXT"],
            $header_data["TR_TP_HEADER_BOL"],
            $tp_header_id,
                ], ";");

        $counter = 1;
        foreach ($detail_data as $row) {
            fputcsv($fp, [
                $counter . "0",
                $row["TR_TP_DETAIL_MATERIAL_CODE"],
                $row["TR_TP_DETAIL_SAP_BATCH"],
                $row["TR_TP_DETAIL_MOBILE_QTY"],
                $row["TR_TP_DETAIL_MOBILE_UOM"],
                $plant_code,
                $row["TR_TP_DETAIL_SLOC_Y21_FROM"],
                $plant_code,
                $row["TR_TP_DETAIL_SLOC"],
                $row["TR_TP_DETAIL_NOTES"],
                "",
                $row["TR_TP_DETAIL_QR_CODE_NUMBER"],
                $header_data["TR_TP_GL_ACCOUNT_CODE"],
                $header_data["TR_TP_COST_CENTER_CODE"],
                    ], ";");
            $counter++;
        }
        fclose($fp);
    } else {
        $detail_data = std_get([
            "select" => ["TR_TP_DETAIL.*", "TR_GR_DETAIL_SLOC", "TR_GR_DETAIL_UNLOADING_PLANT"],
            "table_name" => "TR_TP_DETAIL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ],
            // [
            //     "join_type" => "inner",
            //     "table_name" => "TR_PO_DETAIL",
            //     "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
            //     "operator" => "=",
            //     "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
            // ]
            ],
            "where" => [
                [
                    "field_name" => "TR_TP_DETAIL_TP_HEADER_ID",
                    "operator" => "=",
                    "value" => $tp_header_id
                ]
            ],
            "order_by" => [
                [
                    "field" => "TR_TP_DETAIL_SAPLINE_ID",
                    "type" => "ASC",
                ]
            ],
        ]);

        if ($header_data["TR_TP_HEADER_MVT_CODE"] == "551") {
            $fp = fopen(storage_path('app/public/INCOMING2/MIGO/' . "MIGO_IN_" . $plant_code . "_BIWA_" . str_pad($tp_header_id, 7, "0", STR_PAD_LEFT) . "_" . date("YmdHis") . ".csv"), 'w');

            fputcsv($fp, [
                "BIWA",
                "",
                "",
                "",
                $header_data["TR_TP_HEADER_MVT_CODE"],
                convert_to_dmy(str_replace("-", ".", $header_data["TR_TP_HEADER_PSTG_DATE"])),
                convert_to_dmy(str_replace("-", ".", $header_data["TR_TP_HEADER_DOC_DATE"])),
                $header_data["TR_TP_HEADER_TXT"],
                $header_data["TR_TP_HEADER_BOL"],
                $tp_header_id,
                    ], ";");

            $counter = 1;
            foreach ($detail_data as $row) {
                fputcsv($fp, [
                    $counter,
                    $row["TR_TP_DETAIL_MATERIAL_CODE"],
                    $row["TR_TP_DETAIL_SAP_BATCH"],
                    $row["TR_TP_DETAIL_MOBILE_QTY"],
                    $row["TR_TP_DETAIL_MOBILE_UOM"],
                    $plant_code,
                    $row["TR_TP_DETAIL_SLOC"],
                    NULL,
                    NULL,
                    $row["TR_TP_DETAIL_NOTES"],
                    "",
                    $row["TR_TP_DETAIL_QR_CODE_NUMBER"],
                    $header_data["TR_TP_GL_ACCOUNT_CODE"],
                    $header_data["TR_TP_COST_CENTER_CODE"],
                        ], ";");
                $counter++;
            }
            fclose($fp);
        } else {
            $fp = fopen(storage_path('app/public/INCOMING2/MIGO/' . "MIGO_IN_" . $plant_code . "_TP_" . str_pad($tp_header_id, 7, "0", STR_PAD_LEFT) . "_" . date("YmdHis") . ".csv"), 'w');

            fputcsv($fp, [
                "TP",
                "",
                "",
                "",
                $header_data["TR_TP_HEADER_MVT_CODE"],
                convert_to_dmy(str_replace("-", ".", $header_data["TR_TP_HEADER_PSTG_DATE"])),
                convert_to_dmy(str_replace("-", ".", $header_data["TR_TP_HEADER_DOC_DATE"])),
                $header_data["TR_TP_HEADER_TXT"],
                $header_data["TR_TP_HEADER_BOL"],
                $tp_header_id,
                    ], ";");

            $counter = 1;
            foreach ($detail_data as $row) {
                fputcsv($fp, [
                    $counter,
                    $row["TR_TP_DETAIL_MATERIAL_CODE"],
                    $row["TR_TP_DETAIL_SAP_BATCH"],
                    $row["TR_TP_DETAIL_MOBILE_QTY"],
                    $row["TR_TP_DETAIL_MOBILE_UOM"],
                    $row["TR_GR_DETAIL_UNLOADING_PLANT"],
                    $row["TR_GR_DETAIL_SLOC"],
                    $plant_code,
                    $row["TR_TP_DETAIL_SLOC"],
                    $row["TR_TP_DETAIL_NOTES"],
                    "",
                    $row["TR_TP_DETAIL_QR_CODE_NUMBER"],
                    $header_data["TR_TP_GL_ACCOUNT_CODE"],
                    $header_data["TR_TP_COST_CENTER_CODE"],
                        ], ";");
                $counter++;
            }
            fclose($fp);
        }
    }
}

function generate_cancellation_csv($cancellation_id, $plant_code) {
    $header_data = std_get([
        "select" => ["*"],
        "table_name" => "TR_CANCELATION_MVT",
        "where" => [
            [
                "field_name" => "TR_CANCELLATION_MVT_ID",
                "operator" => "=",
                "value" => $cancellation_id
            ]
        ],
        "first_row" => true
    ]);

    $detail_data = [];
    //GR
    if ($header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "102") {
        $detail_data = std_get([
            "select" => ["TR_CANCELATION_MVT_DETAIL.*", "TR_PO_DETAIL.TR_PO_DETAIL_MATERIAL_LINE_NUM"],
            "table_name" => "TR_CANCELATION_MVT_DETAIL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "TR_PO_DETAIL",
                    "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $header_data["TR_CANCELLATION_MVT_ID"]
                ]
            ]
        ]);
    }
    //GI
    else if ($header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "162" || $header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "352") {
        // $detail_data = std_get([
        //     "select" => ["TR_CANCELATION_MVT_DETAIL.*","TR_PO_DETAIL.TR_PO_DETAIL_MATERIAL_LINE_NUM"],
        //     "table_name" => "TR_CANCELATION_MVT_DETAIL",
        //     "join" => [
        //         [
        //             "join_type" => "inner",
        //             "table_name" => "TR_GI_SAPDETAIL",
        //             "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
        //             "operator" => "=",
        //             "on2" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_SAPHEADER_ID",
        //         ],
        //         [
        //             "join_type" => "inner",
        //             "table_name" => "TR_GR_DETAIL",
        //             "on1" => "TR_GI_SAPDETAIL.TR_GI_SAPDETAIL_GR_DETAIL_ID",
        //             "operator" => "=",
        //             "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
        //         ],
        //         [
        //             "join_type" => "inner",
        //             "table_name" => "TR_PO_DETAIL",
        //             "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
        //             "operator" => "=",
        //             "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
        //         ]
        //     ],
        //     "where" => [
        //         [
        //             "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
        //             "operator" => "=",
        //             "value" => $header_data["TR_CANCELLATION_MVT_ID"]
        //         ]
        //     ]
        // ]);
        $detail_data = std_get([
            "select" => ["TR_CANCELATION_MVT_DETAIL.*"],
            "table_name" => "TR_CANCELATION_MVT_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $header_data["TR_CANCELLATION_MVT_ID"]
                ]
            ]
        ]);
    }
    //TP
    else if ($header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "312" || $header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "552") {
        /*       Hapus karena join ke PO
          $detail_data = std_get([
          "select" => ["TR_CANCELATION_MVT_DETAIL.*","TR_PO_DETAIL.TR_PO_DETAIL_MATERIAL_LINE_NUM"],
          "table_name" => "TR_CANCELATION_MVT_DETAIL",
          "join" => [
          [
          "join_type" => "inner",
          "table_name" => "TR_TP_DETAIL",
          "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
          "operator" => "=",
          "on2" => "TR_TP_DETAIL.TR_TP_DETAIL_ID",
          ],
          [
          "join_type" => "inner",
          "table_name" => "TR_GR_DETAIL",
          "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
          "operator" => "=",
          "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
          ],
          [
          "join_type" => "inner",
          "table_name" => "TR_PO_DETAIL",
          "on1" => "TR_PO_DETAIL.TR_PO_DETAIL_ID",
          "operator" => "=",
          "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_PO_DETAIL_ID",
          ]
          ],
          "where" => [
          [
          "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
          "operator" => "=",
          "value" => $header_data["TR_CANCELLATION_MVT_ID"]
          ]
          ]
          ]);
          if(!$detail_data)
          { */
        $detail_data = std_get([
            "select" => ["TR_CANCELATION_MVT_DETAIL.*"],
            "table_name" => "TR_CANCELATION_MVT_DETAIL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_TP_DETAIL",
                    "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                    "operator" => "=",
                    "on2" => "TR_TP_DETAIL.TR_TP_DETAIL_ID",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_GR_DETAIL_ID",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_ID",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $header_data["TR_CANCELLATION_MVT_ID"]
                ]
            ]
        ]);
        //}
    } else if ($header_data["TR_CANCELLATION_MVT_SAP_CODE"] == "Y22") {
        $detail_data = std_get([
            "select" => ["TR_CANCELATION_MVT_DETAIL.*"],
            "table_name" => "TR_CANCELATION_MVT_DETAIL",
            "join" => [
                [
                    "join_type" => "inner",
                    "table_name" => "TR_TP_DETAIL",
                    "on1" => "TR_CANCELATION_MVT_DETAIL.TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS",
                    "operator" => "=",
                    "on2" => "TR_TP_DETAIL.TR_TP_DETAIL_ID",
                ],
                [
                    "join_type" => "inner",
                    "table_name" => "TR_GR_DETAIL",
                    "on1" => "TR_TP_DETAIL.TR_TP_DETAIL_Y21_GR_REF",
                    "operator" => "=",
                    "on2" => "TR_GR_DETAIL.TR_GR_DETAIL_Y21_TP_REF",
                ]
            ],
            "where" => [
                [
                    "field_name" => "TR_CANCELATION_MVT_DETAIL_HEADER_ID",
                    "operator" => "=",
                    "value" => $header_data["TR_CANCELLATION_MVT_ID"]
                ]
            ]
        ]);
    }
//dd($detail_data);
    $fp = fopen(storage_path('app/public/INCOMING2/MIGO/' . "MIGO_IN_" . $plant_code . "_CANCEL_" . str_pad($cancellation_id, 7, "0", STR_PAD_LEFT) . date("YmdHis") . ".csv"), 'w');

    fputcsv($fp, [
        "CANCEL",
        "",
        $header_data["TR_CANCELLATION_MVT_TR_DOC"],
        $header_data["TR_CANCELLATION_MVT_TR_DOC_YEAR"],
        $header_data["TR_CANCELLATION_MVT_CODE"],
        convert_to_dmy(str_replace("-", ".", $header_data["TR_CANCELLATION_MVT_POSTING_DATE"])),
        date("dmY"),
        "",
        "",
        $cancellation_id
            ], ";");

    foreach ($detail_data as $row) {
        fputcsv($fp, [
            $row["TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_ID"],
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
                ], ";");
    }
    fclose($fp);
}

function generate_stock_opname_csv($pid_id, $plant_code) {
    $header_data = std_get([
        "select" => ["*"],
        "table_name" => "TR_PID_HEADER",
        "where" => [
            [
                "field_name" => "TR_PID_HEADER_ID",
                "operator" => "=",
                "value" => $pid_id
            ]
        ],
        "first_row" => true
    ]);

    $detail_data = std_get([
        "select" => ["*"],
        "table_name" => "TR_PID_DETAIL",
        "where" => [
            [
                "field_name" => "TR_PID_DETAIL_HEADER_ID",
                "operator" => "=",
                "value" => $header_data["TR_PID_HEADER_SAP_NO"]
            ]
        ],
        "order_by" => [
            [
                "field" => "TR_PID_DETAIL_LINE_MATERIAL",
                "type" => "ASC",
            ]
        ],
    ]);

    $fp = fopen(storage_path('app/public/INCOMING2/PID/' . "PID_IN_" . $plant_code . "_" . str_pad($pid_id, 7, "0", STR_PAD_LEFT) . "_" . date("YmdHis") . ".csv"), 'w');

    fputcsv($fp, [
        $header_data["TR_PID_HEADER_SAP_NO"],
        $header_data["TR_PID_HEADER_YEAR"],
        convert_to_dmy(str_replace("-", ".", $header_data["TR_PID_COUNT_DATE"])),
        convert_to_dmy(str_replace("-", ".", $header_data["TR_PID_POSTING_DATE"])),
        $header_data["TR_PID_HEADER_PLANT"],
        $header_data["TR_PID_HEADER_SLOC"]
            ], ";");

    $counter = 1;
    foreach ($detail_data as $row) {
        if ($row["TR_PID_DETAIL_MATERIAL_MOBILE_QTY"] == 0 || $row["TR_PID_DETAIL_MATERIAL_MOBILE_QTY"] == null) {
            fputcsv($fp, [
                $row["TR_PID_DETAIL_LINE_MATERIAL"],
                $row["TR_PID_DETAIL_MATERIAL_CODE"],
                $row["TR_PID_DETAIL_MATERIAL_SAP_BATCH"],
                $row["TR_PID_DETAIL_MATERIAL_MOBILE_QTY"],
                $row["TR_PID_DETAIL_MATERIAL_UOM"],
                "X"
                    ], ";");
        } else {
            fputcsv($fp, [
                $row["TR_PID_DETAIL_LINE_MATERIAL"],
                $row["TR_PID_DETAIL_MATERIAL_CODE"],
                $row["TR_PID_DETAIL_MATERIAL_SAP_BATCH"],
                $row["TR_PID_DETAIL_MATERIAL_MOBILE_QTY"],
                $row["TR_PID_DETAIL_MATERIAL_UOM"]
                    ], ";");
        }
        $counter++;
    }
    fclose($fp);
}

function get_gr_detail_qr($gr_detail_id) {
    $gr_detail = std_get([
        "select" => ["TR_GR_DETAIL_QR_CODE_NUMBER"],
        "table_name" => "TR_GR_DETAIL",
        "where" => [
            [
                "field_name" => "TR_GR_DETAIL_ID",
                "operator" => "=",
                "value" => $gr_detail_id,
            ]
        ],
        "first_row" => true
    ]);
    if ($gr_detail == NULL) {
        return NULL;
    } else {
        return $gr_detail["TR_GR_DETAIL_QR_CODE_NUMBER"];
    }
}

function get_tp_material($params = NULL) {
    if ($params != NULL) {
        $query = DB::table($params["table_name"]);
        if (isset($params["select"])) {
            $query->select($params["select"]);
        }
        if (isset($params["where"])) {
            $query->where($params["where"]);
        }
        if (isset($params["special_where"])) {
            foreach ($params["special_where"] as $row) {
                $query->where($row["field_name"], $row["operator"], $row["value"]);
            }
        }

        if (isset($params["where_in"])) {
            $query->whereIn($params["where_in"]["field_name"], $params["where_in"]["ids"]);
        }

        if (isset($params["or_where"])) {
            $query->orWhere($params["or_where"]);
        }
        if (isset($params["join"])) {
            foreach ($params["join"] as $row) {
                if (isset($row["join_type"]) && isset($row["table_name"]) && isset($row["on1"]) && isset($row["operator"]) && isset($row["on2"])) {
                    if (strtolower($row["join_type"]) == "inner") {
                        $query->join($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    } elseif (strtolower($row["join_type"]) == "left") {
                        $query->leftJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    } elseif (strtolower($row["join_type"]) == "right") {
                        $query->rightJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                }
            }
        }
        if (isset($params["order_by"])) {
            foreach ($params["order_by"] as $row) {
                if (isset($row["field"]) && isset($row["type"])) {
                    $query->orderBy($row["field"], $row["type"]);
                }
            }
        }

        if (isset($params["group_by"])) {
            $query->groupBy($params["group_by"]);
        }

        if (isset($params["limit"])) {
            $query->limit($params["limit"]);
        }

        if (isset($params["offset"])) {
            $query->offset($params["offset"]);
        }

        if (isset($params["dump"]) && $params["dump"] == true) {
            $query->dump();
        }

        if (isset($params["distinct"]) && $params["distinct"] == true) {
            $query->distinct();
        }

        if (isset($params["count"]) && $params["count"] == true) {
            return $query->count();
        }
        if (isset($params["max"])) {
            return $query->max($params["max"]);
        }
        if (isset($params["avg"])) {
            return $query->avg($params["avg"]);
        }
        if (isset($params["is_exist"]) && $params["is_exist"] === true) {
            return $query->exist();
        }
        if (isset($params["doesnt_exist"]) && $params["doesnt_exist"] === true) {
            return $query->doesntExist();
        }

        $query->where(function ($query) {
            $query->where('TR_GR_DETAIL_SLOC', '=', "1419")
                    ->orWhere('TR_GR_DETAIL_SLOC', '=', "1900");
        });

        if (isset($params["first_row"]) && $params["first_row"] === true) {
            return (array) $query->first();
        } else {
            return json_decode($query->get()->toJSON(), true);
        }
    } else {
        return false;
    }
}

function insert_material_log($array_data) {
    return std_insert([
        "table_name" => "LG_MATERIAL",
        "data" => [
            "LG_MATERIAL_CODE" => $array_data["material_code"],
            "LG_MATERIAL_PLANT_CODE" => $array_data["plant_code"],
            "LG_MATERIAL_POSTING_DATE" => $array_data["posting_date"],
            "LG_MATERIAL_MVT_TYPE" => $array_data["movement_type"],
            "LG_MATERIAL_GR_DETAIL_ID" => $array_data["gr_detail_id"],
            "LG_MATERIAL_QTY" => $array_data["base_qty"],
            "LG_MATERIAL_UOM" => $array_data["base_uom"],
            "LG_MATERIAL_CREATED_BY" => $array_data["created_by"],
            "LG_MATERIAL_CREATED_TIMESTAMP" => date("Y-m-d H:i:s")
        ]
    ]);
}

function get_cost_center($params = NULL) {
    if ($params != NULL) {
        $query = DB::table($params["table_name"]);
        if (isset($params["select"])) {
            $query->select($params["select"]);
        }

        $query->where('MA_COSTCNTR_CODE', 'like', "%50200");
        $query->orWhere('MA_COSTCNTR_CODE', 'like', "%40100");
        $query->orWhere('MA_COSTCNTR_CODE', 'like', "%10200");
        $query->orWhere('MA_COSTCNTR_CODE', 'like', "%10100");
        $query->orWhere('MA_COSTCNTR_CODE', 'like', "%20100");
        if (isset($params["where_in"])) {
            $query->whereIn($params["where_in"]["field_name"], $params["where_in"]["ids"]);
        }

        if (isset($params["join"])) {
            foreach ($params["join"] as $row) {
                if (isset($row["join_type"]) && isset($row["table_name"]) && isset($row["on1"]) && isset($row["operator"]) && isset($row["on2"])) {
                    if (strtolower($row["join_type"]) == "inner") {
                        $query->join($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    } elseif (strtolower($row["join_type"]) == "left") {
                        $query->leftJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    } elseif (strtolower($row["join_type"]) == "right") {
                        $query->rightJoin($row["table_name"], $row["on1"], $row["operator"], $row["on2"]);
                    }
                }
            }
        }
        if (isset($params["order_by"])) {
            foreach ($params["order_by"] as $row) {
                if (isset($row["field"]) && isset($row["type"])) {
                    $query->orderBy($row["field"], $row["type"]);
                }
            }
        }

        if (isset($params["group_by"])) {
            $query->groupBy($params["group_by"]);
        }

        if (isset($params["limit"])) {
            $query->limit($params["limit"]);
        }

        if (isset($params["offset"])) {
            $query->offset($params["offset"]);
        }

        if (isset($params["dump"]) && $params["dump"] == true) {
            $query->dump();
        }

        if (isset($params["distinct"]) && $params["distinct"] == true) {
            $query->distinct();
        }

        if (isset($params["count"]) && $params["count"] == true) {
            return $query->count();
        }
        if (isset($params["max"])) {
            return $query->max($params["max"]);
        }
        if (isset($params["avg"])) {
            return $query->avg($params["avg"]);
        }
        if (isset($params["is_exist"]) && $params["is_exist"] === true) {
            return $query->exist();
        }
        if (isset($params["doesnt_exist"]) && $params["doesnt_exist"] === true) {
            return $query->doesntExist();
        }
        if (isset($params["first_row"]) && $params["first_row"] === true) {
            return (array) $query->first();
        } else {
            return json_decode($query->get()->toJSON(), true);
        }
    } else {
        return false;
    }
}

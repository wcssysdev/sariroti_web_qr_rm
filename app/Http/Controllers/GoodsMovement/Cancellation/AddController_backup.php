<?php

namespace App\Http\Controllers\GoodsMovement\Cancellation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AddController extends Controller
{
    public function index(Request $request)
    {
        return view('transaction/goods_movement/cancellation/add');
    }

    public function get_doc_number(Request $request)
    {
        if ($request->doc_type == "GR") {
            return std_get([
                "select" => ["TR_GR_HEADER_SAP_DOC as id", "TR_GR_HEADER_SAP_DOC as text"],
                "table_name" => "TR_GR_HEADER",
                "where" => [
                    // [
                    //     "field_name" => "TR_GR_HEADER_STATUS",
                    //     "operator" => "=",
                    //     "value" => "SUCCESS"
                    // ]
                    [
                        "field_name" => "TR_GR_HEADER_SAP_DOC",
                        "operator" => "!=",
                        "value" => null
                    ]
                ],
                "first_row" => false
            ]);
        }
        else if ($request->doc_type == "GI") {
            return std_get([
                "select" => ["TR_GI_SAPHEADER_SAP_DOC as id", "TR_GI_SAPHEADER_SAP_DOC as text"],
                "table_name" => "TR_GI_SAPHEADER",
                "where" => [
                    // [
                    //     "field_name" => "TR_GI_SAPHEADER_STATUS",
                    //     "operator" => "=",
                    //     "value" => "SUCCESS"
                    // ]
                    [
                        "field_name" => "TR_GI_SAPHEADER_SAP_DOC",
                        "operator" => "!=",
                        "value" => null
                    ]
                ],
                "first_row" => false
            ]);
        }
        else if ($request->doc_type == "TP") {
            return std_get([
                "select" => ["TR_TP_HEADER_SAP_DOC as id", "TR_TP_HEADER_SAP_DOC as text"],
                "table_name" => "TR_TP_HEADER",
                "where" => [
                    // [
                    //     "field_name" => "TR_TP_HEADER_STATUS",
                    //     "operator" => "=",
                    //     "value" => "SUCCESS"
                    // ]
                    [
                        "field_name" => "TR_TP_HEADER_SAP_DOC",
                        "operator" => "!=",
                        "value" => null
                    ]
                ],
                "first_row" => false
            ]);
        }
        else{
            return [];
        }
    }

    public function get_doc_number_detail(Request $request)
    {
        if ($request->doc_type == "GR") {
            $header = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_GR_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ]
                ],
                "first_row" => true
            ]);

            $detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_DETAIL",
                "where" => [
                    [
                        "field_name" => "TR_GR_DETAIL_HEADER_ID",
                        "operator" => "=",
                        "value" => $header["TR_GR_HEADER_ID"]
                    ]
                ]
            ]);

            // return view('transaction/goods_movement/cancellation/detail', [
            //     "doc_type" => $request->doc_type,
            //     "doc_number" => $request->doc_number,
            //     "TR_CANCELLATION_MVT_POSTING_DATE" => $request->TR_CANCELLATION_MVT_POSTING_DATE,
            //     "type" => "GR",
            //     "header" => $header,
            //     "detail" => $detail,
            // ]);
        }
        else if ($request->doc_type == "GI") {
            $res = std_get([
                "select" => ["TR_GI_SAPHEADER_ID"],
                "table_name" => "TR_GI_SAPHEADER",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPHEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ]
                ],
                "first_row" => true
            ]);
            return [
                "type" => "GI",
                "data" => $res
            ];
        }
        else if ($request->doc_type == "TP") {
            $res = std_get([
                "select" => ["TR_TP_HEADER_ID"],
                "table_name" => "TR_TP_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_TP_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ]
                ],
                "first_row" => true
            ]);
            return [
                "type" => "TP",
                "data" => $res
            ];
        }
    }
    
    public function save_validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "type" => "required|in:GR,GI,TP",
            "doc_number" => "required|max:255",
            "selected_ids" => "required",
            "TR_CANCELLATION_MVT_POSTING_DATE" => "required:max:10"
        ]);

        $attributeNames = [
            "type" => "Document Type",
            "doc_number" => "Header ID",
            "selected_ids" => "Detail IDS",
            "TR_CANCELLATION_MVT_POSTING_DATE" => "Posting Date"
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function save(Request $request)
    {
        $validation_res = $this->save_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        if ($request->type == "GR") {
            $doc_header = std_get([
                "select" => ["TR_GR_HEADER_SAP_YEAR as doc_year","TR_GR_HEADER_MVT_CODE as movement","TR_GR_HEADER_ID"],
                "table_name" => "TR_GR_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_GR_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ]
                ],
                "first_row" => true
            ]);

            $gr_already_used = false;

            $gr_detail = std_get([
                "select" => ["*"],
                "table_name" => "TR_GR_DETAIL",
                "where_in" => [
                    "field_name" => "TR_GR_DETAIL_ID",
                    "ids" => $request->selected_ids
                ],
                "first_row" => false
            ]);
            foreach ($gr_detail as $row) {
                if ($row["TR_GR_DETAIL_BASE_QTY"] != $row["TR_GR_DETAIL_LEFT_QTY"]) {
                    $gr_already_used = true;
                    break;
                }
            }
            if ($gr_already_used === true) {
                return response()->json([
                    'message' => "GR data already used!"
                ],500);
            }
        }
        else if ($request->type == "GI") {
            $doc_header = std_get([
                "select" => ["TR_GI_SAPHEADER_SAP_YEAR as doc_year","TR_GI_SAPHEADER_MVT_CODE as movement"],
                "table_name" => "TR_GI_SAPHEADER",
                "where" => [
                    [
                        "field_name" => "TR_GI_SAPHEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ]
                ],
                "first_row" => true
            ]);
        }
        else if ($request->type == "TP") {
            $doc_header = std_get([
                "select" => ["TR_TP_HEADER_SAP_YEAR as doc_year","TR_TP_HEADER_MVT_CODE as movement"],
                "table_name" => "TR_TP_HEADER",
                "where" => [
                    [
                        "field_name" => "TR_TP_HEADER_SAP_DOC",
                        "operator" => "=",
                        "value" => $request->doc_number
                    ]
                ],
                "first_row" => true
            ]);
        }

        if ($doc_header["movement"] == "101") {
            $doc_header["movement"] = "102";
        }
        else if ($doc_header["movement"] == "351") {
            $doc_header["movement"] = "352";
        }
        else if ($doc_header["movement"] == "161") {
            $doc_header["movement"] = "162";
        }
        else if ($doc_header["movement"] == "311") {
            $doc_header["movement"] = "312";
        }
        else if ($doc_header["movement"] == "411") {
            $doc_header["movement"] = "412";
        }
        else if ($doc_header["movement"] == "Y21") {
            $doc_header["movement"] = "Y22";
        }
        else if ($doc_header["movement"] == "551") {
            $doc_header["movement"] = "552";
        }

        $cancellation_id = std_insert_get_id([
            "table_name" => "TR_CANCELATION_MVT",
            "data" => [
                "TR_CANCELLATION_PLANT_CODE" => session("plant"),
                "TR_CANCELLATION_MVT_SAP_CODE" => $doc_header["movement"],
                "TR_CANCELLATION_MVT_TR_DOC" => $request->doc_number,
                "TR_CANCELLATION_MVT_TR_DOC_YEAR" => $doc_header["doc_year"],
                "TR_CANCELLATION_MVT_MATDOC" => NULL,
                "TR_CANCELLATION_MVT_MATDOC_YEAR" => NULL,
                "TR_CANCELLATION_MVT_POSTING_DATE" => convert_to_y_m_d($request->TR_CANCELLATION_MVT_POSTING_DATE),
                "TR_CANCELLATION_MVT_STATUS" => "PENDING",
                "TR_CANCELLATION_MVT_NOTES" => NULL,
                "TR_CANCELLATION_MVT_CODE" => NULL,
                "TR_CANCELLATION_BOL" => NULL,
                "TR_CANCELLATION_TXT" => NULL,
                "TR_CANCELLATION_MVT_CREATED_BY" => session("id"),
                "TR_CANCELLATION_MVT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TR_CANCELLATION_MVT_UPDATED_BY" => NULL,
                "TR_CANCELLATION_MVT_UPDATED_TIMESTAMP" => NULL
            ]
        ]);

        if ($cancellation_id == false) {
            return response()->json([
                'message' => "Error on saving Cancellation Data"
            ],500);
        }
        $detail_insert_data = [];
        foreach ($request->selected_ids as $detail_id) {

            //add for sap line ID because SAP not flexible
            if ($request->type == "GR") {
                $sap_line = std_get([
                    "select" => ["TR_GR_DETAIL_SAPLINE_ID as sap_line"],
                    "table_name" => "TR_GR_DETAIL",
                    "where" => [
                        [
                            "field_name" => "TR_GR_DETAIL_ID",
                            "operator" => "=",
                            "value" => $detail_id
                        ]
                    ],
                    "first_row" => true
                ]);
            }
            else if ($request->type == "GI") {
                $sap_line = std_get([
                    "select" => ["TR_GI_SAPDETAIL_SAPLINE_ID as sap_line"],
                    "table_name" => "TR_GI_SAPDETAIL",
                    "where" => [
                        [
                            "field_name" => "TR_GI_SAPDETAIL_ID",
                            "operator" => "=",
                            "value" => $detail_id
                        ]
                    ],
                    "first_row" => true
                ]);
            }
            else if ($request->type == "TP") {
                $sap_line = std_get([
                    "select" => ["TR_TP_DETAIL_SAPLINE_ID as sap_line"],
                    "table_name" => "TR_TP_DETAIL",
                    "where" => [
                        [
                            "field_name" => "TR_TP_DETAIL_ID",
                            "operator" => "=",
                            "value" => $detail_id
                        ]
                    ],
                    "first_row" => true
                ]);
            }


            $detail_insert_data = array_merge($detail_insert_data, [
                [
                    "TR_CANCELATION_MVT_DETAIL_HEADER_ID" => $cancellation_id,
                    "TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_ID" => $sap_line["sap_line"],
                    "TR_CANCELATION_MVT_DETAIL_TRANSACTION_DETAIL_IDS" => $detail_id
                ]
            ]);
        }
        if ($detail_insert_data != NULL) {
            std_insert([
                "table_name" => "TR_CANCELATION_MVT_DETAIL",
                "data" => $detail_insert_data
            ]);
        }
        
        if ($request->type == "GR") {
            std_update([
                "table_name" => "TR_GR_HEADER",
                "where" => ["TR_GR_HEADER_SAP_DOC" => $request->doc_number],
                "data" => [
                    "TR_GR_HEADER_IS_CANCELLED" => true
                ]
            ]);
            foreach ($request->selected_ids as $detail_id) {
                std_update([
                    "table_name" => "TR_GR_DETAIL",
                    "where" => ["TR_GR_DETAIL_ID" => $detail_id],
                    "data" => [
                        "TR_GR_DETAIL_IS_CANCELLED" => true
                    ]
                ]);
            }
        }
        else if ($request->type == "GI") {
            std_update([
                "table_name" => "TR_GI_SAPHEADER",
                "where" => ["TR_GI_SAPHEADER_SAP_DOC" => $request->doc_number],
                "data" => [
                    "TR_GI_SAPHEADER_IS_CANCELLED" => true
                ]
            ]);
            foreach ($request->selected_ids as $detail_id) {
                std_update([
                    "table_name" => "TR_GI_SAPDETAIL",
                    "where" => ["TR_GI_SAPDETAIL_ID" => $detail_id],
                    "data" => [
                        "TR_GI_SAPDETAIL_IS_CANCELLED" => true
                    ]
                ]);
            }
        }
        else if ($request->type == "TP") {
            std_update([
                "table_name" => "TR_TP_HEADER",
                "where" => ["TR_TP_HEADER_SAP_DOC" => $request->doc_number],
                "data" => [
                    "TR_TP_HEADER_IS_CANCELLED" => true
                ]
            ]);
            foreach ($request->selected_ids as $detail_id) {
                std_update([
                    "table_name" => "TR_TP_DETAIL",
                    "where" => ["TR_TP_DETAIL_ID" => $detail_id],
                    "data" => [
                        "TR_TP_DETAIL_IS_CANCELLED" => true
                    ]
                ]);
            }
        }

        generate_cancellation_csv($cancellation_id, session("plant"));

        return response()->json([
            'message' => "Cancellation Transaction Successfully Created"
        ],200);
    }
}
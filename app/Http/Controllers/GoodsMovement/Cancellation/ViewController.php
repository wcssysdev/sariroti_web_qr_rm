<?php

namespace App\Http\Controllers\GoodsMovement\Cancellation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index(Request $request)
    {
        $conditions = [];
        if (!isset($request->start_date) || $request->start_date == "") {
            $request->start_date = date("Y-m-")."01";
        }
        else{
            $request->start_date = convert_to_y_m_d($request->start_date);
        }

        if (isset($request->start_date) && $request->start_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_CANCELLATION_MVT_CREATED_TIMESTAMP",
                    "operator" => ">=",
                    "value" => $request->start_date." 00:00:00"
                ]
            ]);
        }

        if (!isset($request->end_date) || $request->end_date == "") {
            $request->end_date = date("Y-m-d");
        }
        else{
            $request->end_date = convert_to_y_m_d($request->end_date);
        }

        if (isset($request->end_date) && $request->end_date != "") {
            $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_CANCELLATION_MVT_CREATED_TIMESTAMP",
                    "operator" => "<=",
                    "value" => $request->end_date." 23:59:59"
                ]
            ]);
        }

        $conditions = array_merge($conditions, [
                [
                    "field_name" => "TR_CANCELLATION_PLANT_CODE",
                    "operator" => "=",
                    "value" =>  session("plant")
                ]
            ]);

        $cancellation_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_CANCELATION_MVT",
            "where" => $conditions,
            "first_row" => false
        ]);
        
        return view('transaction/goods_movement/cancellation/view', [
            "start" => $request->start_date,
            "end" => $request->end_date,
            'data' => $cancellation_data
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $gr_data = std_get([
            "select" => ["*"],
            "table_name" => "TR_GR_DETAIL",
            "where" => [
                [
                    "field_name" => "TR_GR_DETAIL_LEFT_QTY",
                    "operator" => ">",
                    "value" => 0
                ],
                [
                    "field_name" => "TR_GR_DETAIL_EXP_DATE",
                    "operator" => ">",
                    "value" => date("Y-m-d H:i:s")
                ]
            ]
        ]);

        if ($gr_data != NULL) {
            Mail::send('mail', $gr_data, function($message) {
                $message->to('abc@gmail.com', 'Tutorials Point')->subject
                    ('Laravel HTML Testing Mail');
                $message->from('xyz@gmail.com','Virat Gandhi');
            });
        }
        
    }
}
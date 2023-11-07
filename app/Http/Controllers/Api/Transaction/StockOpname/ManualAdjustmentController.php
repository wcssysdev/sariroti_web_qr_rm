<?php

namespace App\Http\Controllers\Api\Transaction\StockOpname;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManualAdjustmentController extends Controller
{
    public function save_validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "TR_MANUAL_ADJUSTMENT_GR_HEADER_ID" => "required|max:255",
            "TR_MANUAL_ADJUSTMENT_GR_DETAIL_ID" => "required|max:255",
            "TR_MANUAL_ADJUSTMENT_QTY_BEFORE" => "required",
            "TR_MANUAL_ADJUSTMENT_QTY_AFTER" => "required",
            "TR_MANUAL_ADJUSTMENT_UOM" => "required|max:255",
            "TR_MANUAL_ADJUSTMENT_NOTES" => "required|max:1000"
        ]);

        $attributeNames = [
            "TR_MANUAL_ADJUSTMENT_GR_HEADER_ID" => "TR_MANUAL_ADJUSTMENT_GR_HEADER_ID",
            "TR_MANUAL_ADJUSTMENT_GR_DETAIL_ID" => "TR_MANUAL_ADJUSTMENT_GR_DETAIL_ID",
            "TR_MANUAL_ADJUSTMENT_QTY_BEFORE" => "TR_MANUAL_ADJUSTMENT_QTY_BEFORE",
            "TR_MANUAL_ADJUSTMENT_QTY_AFTER" => "TR_MANUAL_ADJUSTMENT_QTY_AFTER",
            "TR_MANUAL_ADJUSTMENT_UOM" => "TR_MANUAL_ADJUSTMENT_UOM",
            "TR_MANUAL_ADJUSTMENT_NOTES" => "TR_MANUAL_ADJUSTMENT_NOTES"
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }
    
    public function submit(Request $request)
    {
        $validation_res = $this->save_validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $timestamp = date("Y-m-d H:i:s");

        $filename = null;
        if (isset($request->photo) && $request->photo != NULL && $request->photo != "") {
            $upload_dir = public_path()."/storage/Adjustment_images/";
            $img = $request->photo;
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $image_data = base64_decode($img);
            $unique_id = uniqid();
            $file = $upload_dir.$unique_id.'.jpeg';
            $success = file_put_contents($file, $image_data);
            $filename = $unique_id.'.jpeg';
        }

        std_insert([
            "table_name" => "TR_MANUAL_ADJUSTMENT",
            "data" => [
                "TR_MANUAL_ADJUSTMENT_GR_HEADER_ID" => $request->TR_MANUAL_ADJUSTMENT_GR_HEADER_ID,
                "TR_MANUAL_ADJUSTMENT_GR_DETAIL_ID" => $request->TR_MANUAL_ADJUSTMENT_GR_DETAIL_ID,
                "TR_MANUAL_ADJUSTMENT_QTY_BEFORE" => $request->TR_MANUAL_ADJUSTMENT_QTY_BEFORE,
                "TR_MANUAL_ADJUSTMENT_QTY_AFTER" => $request->TR_MANUAL_ADJUSTMENT_QTY_AFTER,
                "TR_MANUAL_ADJUSTMENT_PHOTO" => $filename,
                "TR_MANUAL_ADJUSTMENT_CREATED_BY" => $request->user_data->user_id,
                "TR_MANUAL_ADJUSTMENT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                "TR_MANUAL_ADJUSTMENT_UOM" => $request->TR_MANUAL_ADJUSTMENT_UOM,
                "TR_MANUAL_ADJUSTMENT_NOTES" => $request->TR_MANUAL_ADJUSTMENT_NOTES
            ]
        ]);

        std_update([
            "table_name" => "TR_GR_DETAIL",
            "where" => [
                "TR_GR_DETAIL_ID" => $request->TR_MANUAL_ADJUSTMENT_GR_DETAIL_ID,
            ],
            "data" => [
                "TR_GR_DETAIL_LEFT_QTY" => $request->TR_MANUAL_ADJUSTMENT_QTY_AFTER
            ]
        ]);

        return response()->json([
            "status" => "OK",
            "data" => "Adjustment Successfully Saved"
        ],200);
    }
}
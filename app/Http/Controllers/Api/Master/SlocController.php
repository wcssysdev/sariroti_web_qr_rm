<?php

namespace App\Http\Controllers\Api\Master;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SlocController extends Controller
{
    public function index(Request $request)
	{
        $sloc = get_sloc($request->user_data->plant);
        return response()->json([
            "status" => "OK",
            "data" => $sloc
        ],200);
	}
}
<?php

namespace App\Http\Controllers\GoodsMovement\Cancellation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    public function index(Request $request)
    {
        generate_cancellation_csv($request->id, session("plant"));
        return redirect()->back()->with('message', 'Cancellation Successfully Reposted');   
    }
}

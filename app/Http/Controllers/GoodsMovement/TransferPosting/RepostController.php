<?php

namespace App\Http\Controllers\GoodsMovement\TransferPosting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RepostController extends Controller
{
    public function index(Request $request)
    {
        generate_tp_csv($request->id, session("plant"));
        return redirect()->back()->with('message', 'TP Successfully Reposted');   
    }
}
